<?php

namespace Database\Seeders;

use App\Models\PromotionBanner;
use Illuminate\Database\Seeder;

class PromotionBannerSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            PromotionBanner::factory()->create(['is_active' => false]);
        }
    }
}
