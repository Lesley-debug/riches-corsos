<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);

        $privacy = $this->get(route('privacy'));
        $privacy->assertStatus(200);

        $terms = $this->get(route('terms'));
        $terms->assertStatus(200);
    }
}
