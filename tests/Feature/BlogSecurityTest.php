<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_post_is_not_publicly_accessible(): void
    {
        $post = BlogPost::create([
            'title' => 'Draft',
            'slug' => 'draft-post',
            'body' => '<p>Draft content</p>',
            'published_at' => null,
        ]);

        $this->get(route('blog.show', $post))->assertNotFound();
    }

    public function test_future_post_is_not_publicly_accessible(): void
    {
        $post = BlogPost::create([
            'title' => 'Scheduled',
            'slug' => 'scheduled-post',
            'body' => '<p>Scheduled content</p>',
            'published_at' => now()->addHour(),
        ]);

        $this->get(route('blog.show', $post))->assertNotFound();
    }

    public function test_published_post_body_is_sanitized(): void
    {
        $post = BlogPost::create([
            'title' => 'Published',
            'slug' => 'published-post',
            'body' => '<p onclick="alert(1)">Safe</p><script>alert(1)</script>',
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('blog.show', $post));

        $response->assertOk();
        $body = $response->inertiaProps('post.body');
        $this->assertStringNotContainsString('<script', $body);
        $this->assertStringNotContainsString('onclick', $body);
        $this->assertStringContainsString('<p>Safe</p>', $body);
    }
}