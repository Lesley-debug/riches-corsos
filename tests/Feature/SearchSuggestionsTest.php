<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Puppy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SearchSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_suggestions_return_only_public_content(): void
    {
        $publicPuppy = Puppy::factory()->create([
            'visibility' => 'published',
            'status' => 'available',
        ]);
        Puppy::factory()->create([
            'visibility' => 'draft',
            'status' => 'available',
        ]);

        $publishedPost = BlogPost::create([
            'title' => 'Published care guide',
            'slug' => 'published-care-guide',
            'body' => 'Published content',
            'published_at' => now()->subMinute(),
        ]);
        BlogPost::create([
            'title' => 'Draft care guide',
            'slug' => 'draft-care-guide',
            'body' => 'Draft content',
            'published_at' => null,
        ]);

        $this->getJson(route('search.suggestions'))
            ->assertOk()
            ->assertJsonCount(1, 'puppies')
            ->assertJsonCount(1, 'posts')
            ->assertJsonPath('puppies.0.id', $publicPuppy->id)
            ->assertJsonPath('posts.0.id', $publishedPost->id);
    }

    public function test_search_catalog_is_not_embedded_in_every_inertia_response(): void
    {
        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->missing('searchPuppies')
                ->missing('searchPosts')
            );
    }
}