<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_error_views_exist(): void
    {
        foreach ([403, 404, 419, 429, 500, 503] as $status) {
            $this->assertTrue(
                view()->exists("errors.{$status}"),
                "Missing custom {$status} error view."
            );
        }
    }

    public function test_missing_page_uses_custom_404_view(): void
    {
        $this->get('/this-page-does-not-exist-security-test')
            ->assertNotFound()
            ->assertSee('Page not found');
    }
}