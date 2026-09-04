<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'phone' => '555-0199',
            'subject' => 'Puppy inquiry',
            'message' => 'I am interested in one of your puppies.',
        ];
    }

    public function test_contact_store_saves_message(): void
    {
        Notification::fake();

        $response = $this->post(route('contact.store'), $this->validPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'Puppy inquiry',
        ]);
    }

    public function test_contact_store_validates_required_fields(): void
    {
        $response = $this->post(route('contact.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_store_sends_notification_to_admin(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->post(route('contact.store'), $this->validPayload());

        Notification::assertSentTo($admin, NewContactMessage::class);
    }

    public function test_contact_store_sets_is_read_false_by_default(): void
    {
        Notification::fake();

        $this->post(route('contact.store'), $this->validPayload());

        $this->assertDatabaseHas('contact_messages', ['is_read' => false]);
    }
}
