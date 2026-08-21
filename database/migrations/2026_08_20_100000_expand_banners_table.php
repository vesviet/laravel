<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('position', 50)->default('hero_slider')->after('id');
            $table->string('eyebrow')->nullable()->after('title');
            $table->text('subtitle')->nullable()->after('eyebrow');
            $table->string('cta_text', 100)->nullable()->default('Khám Phá Ngay')->after('subtitle');
            $table->boolean('open_in_new_tab')->default(false)->after('link');
            $table->dateTime('starts_at')->nullable()->after('status');
            $table->dateTime('ends_at')->nullable()->after('starts_at');
            $table->unsignedBigInteger('clicks_count')->default(0)->after('sort_order');

            $table->index(['position', 'status', 'sort_order'], 'banners_position_status_sort_order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('banners_position_status_sort_order_index');
            $table->dropColumn([
                'position',
                'eyebrow',
                'subtitle',
                'cta_text',
                'open_in_new_tab',
                'starts_at',
                'ends_at',
                'clicks_count',
            ]);
        });
    }
};
