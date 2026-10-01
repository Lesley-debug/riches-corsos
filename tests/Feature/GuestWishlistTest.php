<?php

namespace Tests\Feature;

use App\Models\Puppy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestWishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_like_and_unlike_puppy_without_account(): void
    {
        $puppy = Puppy::factory()->create();

        $this->post(route('wishlist.toggle'), ['puppy_id' => $puppy->id])
            ->assertSessionHas('wishlist.puppy_ids', [$puppy->id]);

        $this->post(route('wishlist.toggle'), ['puppy_id' => $puppy->id])
            ->assertSessionHas('wishlist.puppy_ids', []);
    }

    public function test_guest_must_login_to_view_wishlist(): void
    {
        $this->get(route('wishlist.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unverified_user_can_like_but_must_verify_to_view_wishlist(): void
    {
        $user = User::factory()->unverified()->create();
        $puppy = Puppy::factory()->create();

        $this->actingAs($user)
            ->post(route('wishlist.toggle'), ['puppy_id' => $puppy->id])
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'puppy_id' => $puppy->id,
        ]);

        $this->actingAs($user)
            ->get(route('wishlist.index'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_guest_likes_are_merged_into_account_after_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('puppy123'),
        ]);
        $puppy = Puppy::factory()->create();

        $this->withSession(['wishlist.puppy_ids' => [$puppy->id]])
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'puppy123',
            ])
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'puppy_id' => $puppy->id,
        ]);
        $this->assertSame([], session('wishlist.puppy_ids', []));
    }
}