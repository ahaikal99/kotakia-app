<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('theme');
            $table->string('host_name');
            $table->string('celebrant_name');
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('venue');
            $table->text('address');
            $table->string('map_url', 2048)->nullable();
            $table->string('contact_name');
            $table->string('contact_phone', 25);
            $table->text('message')->nullable();
            $table->string('package_code')->nullable();
            $table->string('package_name')->nullable();
            $table->string('design_code')->nullable();
            $table->string('design_name')->nullable();
            $table->unsignedInteger('amount_cents')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
