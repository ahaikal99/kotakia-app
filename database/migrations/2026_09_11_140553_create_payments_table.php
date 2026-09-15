<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->unsignedInteger('amount_cents');
            $table->string('currency', 3)->default('MYR');
            $table->string('status')->default('simulated');
            $table->string('method')->default('dummy');
            $table->timestamp('simulated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
