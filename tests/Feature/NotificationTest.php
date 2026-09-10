<?php

namespace Tests\Feature;

use App\Models\Puppy;
use App\Models\User;
use App\Notifications\NewPuppyPostedNotification;
use App\Notifications\OrderPlacedCustomerNotification;
use App\Notifications\PasswordResetSuccessNotification;
use App\Notifications\WelcomeNotification;
use App\Notifications\WishlistAddedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_welcome_notification(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'password' => 'password-1234',
            'password_confirmation' => 'password-1234',
        ]);

        $response->assertRedirect(route('account.dashboard'));

        $user = User::where('email', 'sarah@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_placing_order_sends_order_notification_to_customer(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'name' => 'John Wick',
            'email' => 'john@example.com',
        ]);

        $puppy = Puppy::factory()->create([
            'status' => 'available',
            'visibility' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy->id]])
            ->post('/checkout', [
                'buyer_name' => 'John Wick',
                'buyer_email' => 'john@example.com',
                'buyer_phone' => '+12145550199',
                'buyer_address' => 'Continental Hotel, NYC',
            ]);

        $response->assertRedirect(route('puppies.index'));

        Notification::assertSentTo($user, OrderPlacedCustomerNotification::class);
    }

    public function test_adding_to_wishlist_dispatches_wishlist_notification(): void
    {
        $puppy = Puppy::factory()->create(['visibility' => 'published', 'status' => 'available']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wishlist/toggle', [
            'puppy_id' => $puppy->id,
        ]);

        $response->assertSessionHas('success', 'Added to your wishlist.');
        $wishlistNotif = $user->notifications()->where('data->type', 'wishlist_added')->first();
        $this->assertNotNull($wishlistNotif);
        $this->assertEquals('wishlist_added', $wishlistNotif->data['type']);
    }

    public function test_publishing_new_puppy_notifies_registered_users(): void
    {
        Notification::fake();

        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);

        $puppy = Puppy::factory()->create([
            'name' => 'Titan',
            'visibility' => 'published',
            'status' => 'available',
        ]);

        Notification::assertSentTo([$user1, $user2], NewPuppyPostedNotification::class);
    }

    public function test_notification_center_renders_and_marks_notifications_as_read(): void
    {
        $puppy = Puppy::factory()->create();
        $user = User::factory()->create();

        // Create database notification
        $user->notify(new WishlistAddedNotification($puppy));

        $this->assertEquals(1, $user->unreadNotifications()->count());

        $notificationId = $user->unreadNotifications->first()->id;

        // View notifications page
        $response = $this->actingAs($user)->get(route('account.notifications'));
        $response->assertStatus(200);

        // Mark single notification read
        $markResponse = $this->actingAs($user)->post(route('account.notifications.read', $notificationId));
        $markResponse->assertRedirect();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_mark_all_notifications_read(): void
    {
        $puppy1 = Puppy::factory()->create();
        $puppy2 = Puppy::factory()->create();
        $user = User::factory()->create();

        $user->notify(new WishlistAddedNotification($puppy1));
        $user->notify(new WishlistAddedNotification($puppy2));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user)->post(route('account.notifications.read-all'));
        $response->assertRedirect();

        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
