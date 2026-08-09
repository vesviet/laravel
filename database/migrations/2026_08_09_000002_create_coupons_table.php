<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // e.g. 'WELCOME10'
            $table->string('type');                    // 'percentage' | 'fixed'
            $table->decimal('value', 10, 2);           // 10 for 10%, or 50000 for fixed 50k VND
            $table->decimal('min_order_amount', 15, 2)->default(0); // Minimum cart subtotal to apply
            $table->integer('usage_limit')->nullable();             // null = unlimited
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
