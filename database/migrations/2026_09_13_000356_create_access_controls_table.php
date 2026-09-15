<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_controls', function (Blueprint $table) {
            $table->id();
            $table->string('key', 30)->unique();
            $table->boolean('is_blocked')->default(false);
            $table->string('reason', 500)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        foreach (['login', 'registration', 'orders', 'payments'] as $key) {
            DB::table('access_controls')->insert(['key' => $key, 'is_blocked' => false, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('access_controls');
    }
};
