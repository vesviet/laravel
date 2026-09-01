<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Align orders.status ENUM with OrderStatus PHP enum values.
 *
 * Original migration used 'shipping' but OrderStatus::Shipped->value = 'shipped'.
 * MySQL strict mode (STRICT_ALL_TABLES) throws SQLSTATE[01000] Warning 1265
 * (Data truncated) when inserting 'shipped' into the old ENUM definition.
 *
 * This migration:
 * 1. Updates existing rows: 'shipping' → 'shipped'
 * 2. Alters the ENUM column to the aligned values
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Migrate existing data before altering the column
        DB::table('orders')->where('status', 'shipping')->update(['status' => 'shipped']);

        // Step 2: Alter ENUM to match OrderStatus PHP enum values exactly
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'delivered',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert shipped → shipping for rollback safety
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'shipping']);
        DB::table('orders')->where('status', 'processing')->update(['status' => 'confirmed']);

        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM(
            'pending',
            'confirmed',
            'shipping',
            'delivered',
            'cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};
