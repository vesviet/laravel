<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change payment_method from enum('cod') to varchar(50).
     * This future-proofs the column for VNPay, Momo, and other payment providers
     * without requiring a destructive migration later.
     * Default remains 'cod' — zero business logic change.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 50)->default('cod')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to enum — note: any non-cod values would be lost
            $table->enum('payment_method', ['cod'])->default('cod')->change();
        });
    }
};
