<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the customer_cart_items table for DB-backed cart persistence.
     *
     * - No created_at: not needed for cart lifecycle.
     * - updated_at only: idle-detection for ProcessAbandonedCartsCommand (idle > 1h).
     * - UNIQUE composite: prevents duplicates, makes upsert() idempotent under race conditions.
     * - cascade deletes: customer/product deleted -> cart row deleted automatically.
     */
    public function up(): void
    {
        Schema::create('customer_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(
                ['customer_id', 'product_id', 'product_variant_id'],
                'uq_customer_cart'
            );
            $table->index('customer_id');
            $table->index('updated_at'); // ProcessAbandonedCartsCommand idle-detection
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_cart_items');
    }
};
