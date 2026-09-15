<?php

namespace Database\Factories;

use App\Models\PromotionBanner;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PromotionBanner> */
class PromotionBannerFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => 'Promosi Kotakia', 'image_path' => 'promotion-banners/example.png', 'is_active' => true, 'expires_at' => now()->addDays(7), 'created_by' => User::factory()->manager(), 'updated_by' => fn (array $attributes) => $attributes['created_by']];
    }
}
