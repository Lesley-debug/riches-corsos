<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Puppy;
use App\Models\PuppyImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PuppyModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Suppress PuppyObserver notification dispatches during factory creates
        Notification::fake();
    }

    public function test_slug_is_auto_generated_on_save(): void
    {
        $puppy = Puppy::factory()->create(['name' => 'Bella']);

        $this->assertNotEmpty($puppy->slug);
        $this->assertStringStartsWith('bella', $puppy->slug);
    }

    public function test_slug_is_not_overwritten_when_already_set(): void
    {
        $puppy = Puppy::factory()->create(['slug' => 'my-custom-slug']);

        $this->assertSame('my-custom-slug', $puppy->slug);
    }

    public function test_age_in_weeks_is_appended_to_model(): void
    {
        $puppy = Puppy::factory()->create([
            'date_of_birth' => now()->subWeeks(8),
        ]);

        $this->assertArrayHasKey('age_in_weeks', $puppy->toArray());
        $this->assertSame(8, $puppy->age_in_weeks);
    }

    public function test_images_relation_returns_puppy_images_ordered_by_sort_order(): void
    {
        $puppy = Puppy::factory()->create();
        PuppyImage::factory()->create(['puppy_id' => $puppy->id, 'sort_order' => 2]);
        PuppyImage::factory()->create(['puppy_id' => $puppy->id, 'sort_order' => 1]);

        $images = $puppy->images;

        $this->assertCount(2, $images);
        $this->assertSame(1, $images->first()->sort_order);
    }

    public function test_orders_relation_returns_related_orders(): void
    {
        $puppy = Puppy::factory()->create();
        Order::factory()->create(['puppy_id' => $puppy->id]);

        $this->assertCount(1, $puppy->orders);
    }
}
