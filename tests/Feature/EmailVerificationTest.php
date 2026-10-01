<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_can_view_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk();
    }

    public function test_unverified_user_is_redirected_from_dashboard_and_orders(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($user)
            ->get(route('account.orders'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_can_still_use_wishlist_and_checkout(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('wishlist.index'))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'buyer_name' => 'Unverified Customer',
                'buyer_email' => $user->email,
                'buyer_phone' => '+1 555 0100',
                'buyer_address' => '123 Test Street',
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors('cart');
    }

    public function test_user_can_verify_email_with_signed_link(): void
    {
        Event::fake();
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('account.dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_user_can_request_another_verification_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertSessionHas('status');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verified_user_can_view_dashboard_and_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('account.orders'))
            ->assertOk();
    }
}