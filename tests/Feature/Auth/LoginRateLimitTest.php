<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        RateLimiter::clear('owner@example.com|127.0.0.1');
        RateLimiter::clear('customer@example.com|127.0.0.1');

        parent::tearDown();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('correct-password'),
            'role' => 'admin',
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'email' => 'owner@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->from('/login')->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'correct-password',
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_successful_login_clears_previous_failed_attempts(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('correct-password'),
            'role' => 'customer',
        ]);

        $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'correct-password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, RateLimiter::attempts('customer@example.com|127.0.0.1'));
    }
}
