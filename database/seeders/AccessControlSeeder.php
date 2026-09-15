<?php

namespace Database\Seeders;

use App\Models\AccessControl;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(AccessControl::LABELS) as $key) {
            AccessControl::firstOrCreate(['key' => $key], ['is_blocked' => false]);
        }
    }
}
