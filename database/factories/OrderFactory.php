<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Puppy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
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
            'buyer_name' => fake()->name(),
            'buyer_email' => fake()->safeEmail(),
            'buyer_phone' => fake()->phoneNumber(),
            'buyer_address' => fake()->address(),
            'notes' => null,
            'status' => 'new',
        ];
    }
}
