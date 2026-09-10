<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_warns_when_credentials_not_configured(): void
    {
        Config::set('services.google.client_id', null);
        Config::set('services.google.client_secret', null);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('google');
    }

    public function test_google_callback_registers_new_user_and_sends_welcome_notification(): void
    {
        Notification::fake();

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-123456');
        $googleUser->shouldReceive('getName')->andReturn('Marcus Aurelius');
        $googleUser->shouldReceive('getEmail')->andReturn('marcus@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $response->assertRedirect(route('account.dashboard'));

        $user = User::where('email', 'marcus@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('google-123456', $user->google_id);
        $this->assertEquals('Marcus Aurelius', $user->name);
        $this->assertEquals('customer', $user->role);

        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_google_callback_authenticates_existing_user_and_updates_google_id(): void
    {
        Notification::fake();

        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing Corso Fan',
            'google_id' => null,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-789012');
        $googleUser->shouldReceive('getName')->andReturn('Existing Corso Fan');
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($existingUser);
        $response->assertRedirect(route('account.dashboard'));

        $existingUser->refresh();
        $this->assertEquals('google-789012', $existingUser->google_id);
    }
}
