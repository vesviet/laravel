<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('waybill_id')->nullable()->after('status');
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned(); // 1 to 5
            $table->text('comment')->nullable();
            $table->string('status')->default('pending'); // pending, approved, hidden
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['customer_id', 'product_id']);
        });

        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('flash_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->integer('sold_quantity')->default(0);
            $table->timestamps();
            
            $table->unique(['flash_sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_sale_items');
        Schema::dropIfExists('flash_sales');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_reviews');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('waybill_id');
        });
    }
};
