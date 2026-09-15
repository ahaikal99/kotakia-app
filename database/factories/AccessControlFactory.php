<?php

namespace Database\Factories;

use App\Models\AccessControl;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AccessControl> */
class AccessControlFactory extends Factory
{
    public function definition(): array
    {
        return ['key' => 'test-control', 'is_blocked' => false, 'reason' => null];
    }
}
