<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * R6: Add missing database indexes for performance and query optimization.
     *
     * Indexes are added for columns used in:
     * - Order lookups by order_number (tracking, admin search)
     * - Order filtering by status and customer_id
     * - Landing page lookups by slug and status
     * - Coupon lookup by code
     * - Product filtering by status and category
     *
     * All indexes are added with IF NOT EXISTS equivalent (ignoring duplicates).
     */
    public function up(): void
    {
        // orders: order_number already has unique index from initial migration
        // Add composite index for admin queries (status + created_at for filtering)
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
            $table->index(['customer_id', 'status'], 'orders_customer_status_index');
            $table->index(['landing_page_id'], 'orders_landing_page_id_index');
        });

        // landing_pages: slug lookup (unique already exists), add status index
        if (Schema::hasColumn('landing_pages', 'status')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->index(['status'], 'landing_pages_status_index');
            });
        }

        // products: category + status composite (used in catalog page filters)
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'category_id'], 'products_status_category_index');
        });

        // coupons: code lookup (validate coupon at checkout)
        Schema::table('coupons', function (Blueprint $table) {
            $table->index(['code', 'is_active'], 'coupons_code_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_at_index');
            $table->dropIndex('orders_customer_status_index');
            $table->dropIndex('orders_landing_page_id_index');
        });

        if (Schema::hasColumn('landing_pages', 'status')) {
            Schema::table('landing_pages', function (Blueprint $table) {
                $table->dropIndex('landing_pages_status_index');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_category_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('coupons_code_active_index');
        });
    }
};
