<?php

namespace Database\Factories;

use App\Models\Puppy;
use App\Models\PuppyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PuppyImage>
 */
class PuppyImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'puppy_id' => Puppy::factory(),
            'path' => 'puppies/'.fake()->uuid().'.jpg',
            'sort_order' => 0,
        ];
    }
}
