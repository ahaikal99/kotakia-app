<?php

namespace Database\Factories;

use App\Models\Design;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Design>
 */
class DesignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'TEST'.fake()->unique()->numerify('######'),
            'name' => fake()->words(2, true),
            'theme' => 'perkahwinan',
            'is_active' => false,
        ];
    }
}
