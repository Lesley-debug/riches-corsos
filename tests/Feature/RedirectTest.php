<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_shop_urls_redirect_permanently_to_puppies(): void
    {
        $this->get('/shop')->assertStatus(301)->assertRedirect('/puppies');
        $this->get('/shop/index.php')->assertStatus(301)->assertRedirect('/puppies');
        $this->get('/shop/product.php')->assertStatus(301)->assertRedirect('/puppies');
        $this->get('/shop/product.php?id=12')->assertStatus(301)->assertRedirect('/puppies');
        $this->get('/puppy.php')->assertStatus(301)->assertRedirect('/puppies');
    }

    public function test_legacy_php_pages_redirect_permanently_to_clean_urls(): void
    {
        $this->get('/about.php')->assertStatus(301)->assertRedirect('/about');
        $this->get('/about-us.php')->assertStatus(301)->assertRedirect('/about');
        $this->get('/contact.php')->assertStatus(301)->assertRedirect('/contact');
        $this->get('/contact-us.php')->assertStatus(301)->assertRedirect('/contact');
        $this->get('/blog.php')->assertStatus(301)->assertRedirect('/blog');
        $this->get('/faq.php')->assertStatus(301)->assertRedirect('/faqs');
        $this->get('/faqs.php')->assertStatus(301)->assertRedirect('/faqs');
        $this->get('/testimonials.php')->assertStatus(301)->assertRedirect('/testimonials');
    }

    public function test_unauthenticated_user_accessing_account_is_redirected_to_login(): void
    {
        $this->get(route('account.dashboard'))->assertStatus(302)->assertRedirect(route('login'));
        $this->get(route('account.orders'))->assertStatus(302)->assertRedirect(route('login'));
        $this->get(route('wishlist.index'))->assertStatus(302)->assertRedirect(route('login'));
    }

    public function test_authenticated_user_accessing_guest_routes_is_redirected_to_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('login'))->assertStatus(302)->assertRedirect(route('account.dashboard'));
        $this->actingAs($user)->get(route('register'))->assertStatus(302)->assertRedirect(route('account.dashboard'));
        $this->actingAs($user)->get(route('password.request'))->assertStatus(302)->assertRedirect(route('account.dashboard'));
    }
}
