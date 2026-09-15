<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            AuditLog::factory()->create();
        }
    }
}
