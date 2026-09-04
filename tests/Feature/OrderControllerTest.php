<?php

namespace Tests\Feature;

use App\Models\Puppy;
use App\Models\User;
use App\Notifications\NewOrderPlaced;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(int $puppyId): array
    {
        return [
            'puppy_id' => $puppyId,
            'buyer_name' => 'Jane Doe',
            'buyer_email' => 'jane@example.com',
            'buyer_phone' => '555-0100',
            'buyer_address' => '123 Main St',
            'notes' => 'Please call first.',
        ];
    }

    public function test_store_creates_order_and_marks_puppy_pending(): void
    {
        Notification::fake();

        $puppy = Puppy::factory()->create(['status' => 'available']);

        $response = $this->post(route('orders.store'), $this->validPayload($puppy->id));

        $response->assertRedirect(route('puppies.show', $puppy->slug));
        $this->assertDatabaseHas('orders', ['puppy_id' => $puppy->id, 'buyer_email' => 'jane@example.com']);
        $this->assertSame('pending', $puppy->fresh()->status);
    }

    public function test_store_rejects_unavailable_puppy(): void
    {
        Notification::fake();

        $puppy = Puppy::factory()->unavailable()->create();

        $response = $this->post(route('orders.store'), $this->validPayload($puppy->id));

        $response->assertRedirect();
        $response->assertSessionHasErrors('puppy_id');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post(route('orders.store'), []);

        $response->assertSessionHasErrors(['puppy_id', 'buyer_name', 'buyer_email', 'buyer_phone']);
    }

    public function test_store_attaches_authenticated_user_id(): void
    {
        Notification::fake();

        $puppy = Puppy::factory()->create(['status' => 'available']);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('orders.store'), $this->validPayload($puppy->id));

        $this->assertDatabaseHas('orders', ['puppy_id' => $puppy->id, 'user_id' => $user->id]);
    }

    public function test_store_sends_notification_to_admin(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $puppy = Puppy::factory()->create(['status' => 'available']);

        $this->post(route('orders.store'), $this->validPayload($puppy->id));

        Notification::assertSentTo($admin, NewOrderPlaced::class);
    }
}
