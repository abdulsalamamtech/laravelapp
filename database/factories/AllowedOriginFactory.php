<?php

namespace Database\Factories;

use App\Models\AllowedOrigin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AllowedOrigin>
 */
class AllowedOriginFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'domain' => fake()->unique()->url(),
            'is_active' => true,
        ];
    }
}
