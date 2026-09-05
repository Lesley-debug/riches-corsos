<?php

namespace Database\Factories;

use App\Models\Puppy;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Puppy> */
class PuppyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->firstName(),
            'slug'        => '',
            'breed'       => 'Cane Corso',
            'date_of_birth' => fake()->dateTimeBetween('-16 weeks', '-4 weeks'),
            'sex'         => fake()->randomElement(['male', 'female']),
            'price'       => fake()->randomFloat(2, 1500, 5000),
            'status'      => 'available',
            'description' => fake()->paragraph(),
            'visibility'  => 'published',
        ];
    }

    public function unavailable(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
