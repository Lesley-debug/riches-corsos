<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Puppy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_cart(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_when_adding_to_cart(): void
    {
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->post(route('cart.store'), [
            'puppy_id' => $puppy->id,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_cart_page_renders_with_cart_items_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $puppy1 = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);
        $puppy2 = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy1->id, $puppy2->id]])
            ->get(route('cart.index'));

        $response->assertStatus(200);
        $cartItems = collect($response->inertiaProps('cartItems'));
        $this->assertCount(2, $cartItems);
        $this->assertEqualsCanonicalizing(
            [$puppy1->id, $puppy2->id],
            $cartItems->pluck('id')->all()
        );
    }

    public function test_available_published_puppy_can_be_added_to_cart_by_authenticated_user(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'puppy_id' => $puppy->id,
        ]);

        $response->assertSessionHas('cart.puppy_ids', [$puppy->id]);
        $response->assertSessionHas('success');
    }

    public function test_duplicate_puppy_cannot_be_added_twice(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy->id]])
            ->post(route('cart.store'), [
                'puppy_id' => $puppy->id,
            ]);

        $response->assertSessionHas('cart.puppy_ids', [$puppy->id]);
        $response->assertSessionHas('success', "{$puppy->name} is already in your cart.");
    }

    public function test_unavailable_puppy_cannot_be_added_to_cart(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'sold', 'visibility' => 'published']);

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'puppy_id' => $puppy->id,
        ]);

        $response->assertSessionHasErrors(['puppy_id']);
        $this->assertEmpty(session('cart.puppy_ids', []));
    }

    public function test_unpublished_puppy_cannot_be_added_to_cart(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'private']);

        $response = $this->actingAs($user)->post(route('cart.store'), [
            'puppy_id' => $puppy->id,
        ]);

        $response->assertSessionHasErrors(['puppy_id']);
        $this->assertEmpty(session('cart.puppy_ids', []));
    }

    public function test_puppy_can_be_removed_from_cart(): void
    {
        $user = User::factory()->create();
        $puppy1 = Puppy::factory()->create();
        $puppy2 = Puppy::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy1->id, $puppy2->id]])
            ->delete(route('cart.destroy', $puppy1));

        $response->assertSessionHas('cart.puppy_ids', [$puppy2->id]);
        $response->assertSessionHas('success', "{$puppy1->name} was removed from your cart.");
    }

    public function test_checkout_validates_required_contact_fields(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy->id]])
            ->post(route('checkout.store'), []);

        $response->assertSessionHasErrors(['buyer_name', 'buyer_email', 'buyer_phone']);
        $this->assertDatabaseEmpty('orders');
    }

    public function test_checkout_fails_when_cart_is_empty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => []])
            ->post(route('checkout.store'), [
                'buyer_name' => 'Jane Doe',
                'buyer_email' => 'jane@example.com',
                'buyer_phone' => '555-123-4567',
            ]);

        $response->assertSessionHasErrors(['cart']);
        $this->assertDatabaseEmpty('orders');
    }

    public function test_checkout_places_orders_for_cart_items_and_marks_puppy_pending(): void
    {
        $user = User::factory()->create();
        $puppy1 = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);
        $puppy2 = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy1->id, $puppy2->id]])
            ->post(route('checkout.store'), [
                'buyer_name' => 'John Doe',
                'buyer_email' => 'john@example.com',
                'buyer_phone' => '555-987-6543',
                'buyer_address' => '123 Main St, Springfield',
                'notes' => 'Looking forward to meeting the puppies.',
            ]);

        $response->assertRedirect(route('puppies.index'));
        $response->assertSessionHas('success');
        $this->assertNull(session('cart.puppy_ids'));

        $this->assertDatabaseHas('orders', [
            'puppy_id' => $puppy1->id,
            'buyer_name' => 'John Doe',
            'buyer_email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('orders', [
            'puppy_id' => $puppy2->id,
            'buyer_name' => 'John Doe',
            'buyer_email' => 'john@example.com',
        ]);

        $this->assertSame('pending', $puppy1->fresh()->status);
        $this->assertSame('pending', $puppy2->fresh()->status);
    }

    public function test_checkout_fails_if_any_puppy_in_cart_is_no_longer_available(): void
    {
        $user = User::factory()->create();
        $availablePuppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);
        $soldPuppy = Puppy::factory()->create(['status' => 'sold', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$availablePuppy->id, $soldPuppy->id]])
            ->post(route('checkout.store'), [
                'buyer_name' => 'John Doe',
                'buyer_email' => 'john@example.com',
                'buyer_phone' => '555-987-6543',
            ]);

        $response->assertSessionHasErrors(['cart']);
        $this->assertDatabaseEmpty('orders');
        $this->assertSame('available', $availablePuppy->fresh()->status);
    }

    public function test_checkout_associates_logged_in_user_with_orders(): void
    {
        $user = User::factory()->create();
        $puppy = Puppy::factory()->create(['status' => 'available', 'visibility' => 'published']);

        $response = $this->actingAs($user)
            ->withSession(['cart.puppy_ids' => [$puppy->id]])
            ->post(route('checkout.store'), [
                'buyer_name' => 'Registered Customer',
                'buyer_email' => $user->email,
                'buyer_phone' => '555-111-2222',
            ]);

        $response->assertRedirect(route('puppies.index'));
        $this->assertDatabaseHas('orders', [
            'puppy_id' => $puppy->id,
            'user_id' => $user->id,
            'buyer_name' => 'Registered Customer',
        ]);
    }
}
