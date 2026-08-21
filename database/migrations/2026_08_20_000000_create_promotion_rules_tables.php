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
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->enum('rule_type', ['catalog_rule', 'cart_rule'])->default('cart_rule');
            $table->enum('action_type', [
                'percentage',
                'fixed_amount',
                'buy_x_get_y',
                'tiered_quantity',
                'free_shipping',
            ]);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('max_discount_amount', 15, 2)->nullable();
            $table->decimal('min_order_amount', 15, 2)->default(0);
            $table->integer('min_quantity')->default(0);
            $table->json('conditions')->nullable();
            $table->string('target_customer_tier')->default('all');
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_limit_per_user')->default(1);
            $table->integer('used_count')->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('stop_further_rules')->default(false);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Composite indexes for query optimization
            $table->index(['is_active', 'rule_type', 'priority'], 'promo_rules_active_type_priority_idx');
            $table->index(['code', 'is_active'], 'promo_rules_code_active_idx');
            $table->index(['starts_at', 'ends_at'], 'promo_rules_dates_idx');
        });

        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_rule_id')
                ->constrained('promotion_rules')
                ->cascadeOnDelete();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->string('email');
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();

            // Composite indexes for fast per-customer / per-user / per-email usage validation
            $table->index(['promotion_rule_id', 'customer_id'], 'promo_usages_rule_customer_idx');
            $table->index(['promotion_rule_id', 'user_id'], 'promo_usages_rule_user_idx');
            $table->index(['promotion_rule_id', 'email'], 'promo_usages_rule_email_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotion_rules');
    }
};
