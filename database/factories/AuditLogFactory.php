<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AuditLog> */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 'action' => 'page.viewed', 'outcome' => 'success',
            'ip_address' => fake()->ipv4(), 'user_agent' => 'Kotakia test browser',
            'method' => 'GET', 'route_name' => 'dashboard', 'route_path' => '/dashboard', 'status_code' => 200,
        ];
    }
}
