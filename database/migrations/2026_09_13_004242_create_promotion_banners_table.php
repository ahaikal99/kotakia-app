<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('image_path');
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->index();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_banners');
    }
};
