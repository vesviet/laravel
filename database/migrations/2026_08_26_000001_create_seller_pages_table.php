<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('seller_profiles')->cascadeOnDelete();
            $table->json('theme_config')->nullable(); // colors, fonts, dark/light
            $table->json('blocks')->nullable(); // content blocks
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_pages');
    }
};
