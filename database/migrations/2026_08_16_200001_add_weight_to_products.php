<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * S8: Add weight field (grams) to products and product_variants.
     *
     * Weight is required for accurate Goship shipping fee calculation.
     * Default 0 = not set. GoshipService falls back to 1000g when weight = 0.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('weight')->default(0)->after('stock')
                ->comment('Weight in grams. 0 = not set (GoshipService uses fallback 1000g).');
        });

        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->unsignedInteger('weight')->default(0)->after('stock')
                    ->comment('Weight in grams. Overrides product weight if > 0.');
            });
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight');
        });

        if (Schema::hasTable('product_variants') && Schema::hasColumn('product_variants', 'weight')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('weight');
            });
        }
    }
};
