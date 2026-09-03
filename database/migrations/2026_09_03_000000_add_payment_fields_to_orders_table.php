<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add payment state machine fields to orders table:
     * - payment_status: unpaid, paid, partially_paid, failed, refunded, expired
     * - payment_transaction_id: external reference from provider/gateway
     * - paid_at: confirmation timestamp
     * - payment_expires_at: deadline for unpaid online orders
     * - payment_details: JSON payload/metadata from payment callback
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('unpaid')->after('payment_method');
            $table->string('payment_transaction_id', 100)->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('payment_transaction_id');
            $table->timestamp('payment_expires_at')->nullable()->after('paid_at');
            $table->json('payment_details')->nullable()->after('payment_expires_at');

            $table->index(['payment_status', 'payment_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'payment_expires_at']);
            $table->dropColumn([
                'payment_status',
                'payment_transaction_id',
                'paid_at',
                'payment_expires_at',
                'payment_details',
            ]);
        });
    }
};
