<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Slice 1 — Add shop_slug to seller_profiles.
 *
 * shop_slug is the path-based storefront identifier: demo.tanhdev.com/shop/{shop_slug}
 * It is separate from `subdomain` which is fixed at registration and used by Spatie
 * SubdomainTenantFinder for tenant resolution.
 *
 * Migration strategy (safe for existing data):
 *   1. Add column as NULLABLE first (avoids constraint failure on existing rows)
 *   2. Backfill: SET shop_slug = subdomain for all existing sellers
 *   3. Alter to NOT NULL (data is now complete)
 *
 * ADR-SC1: shop_slug is separately managed by Admin only.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add as nullable (won't fail on existing rows)
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('shop_slug')->nullable()->unique()->after('subdomain')
                ->comment('Path-based storefront slug: /shop/{shop_slug}. Admin-managed. Separate from subdomain (Spatie tenant resolution).');
        });

        // Step 2: Backfill — default to subdomain value so existing sellers get a valid slug
        DB::statement('UPDATE seller_profiles SET shop_slug = subdomain WHERE shop_slug IS NULL');

        // Step 3: Apply NOT NULL constraint now that all rows have a value
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('shop_slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropUnique(['shop_slug']);
            $table->dropColumn('shop_slug');
        });
    }
};
