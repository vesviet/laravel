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
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('cart_token', 64)->unique();
            $table->json('items_json');
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamp('step_1_sent_at')->nullable()->index();
            $table->timestamp('step_2_sent_at')->nullable()->index();
            $table->string('incentive_coupon_code')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
    }
};
