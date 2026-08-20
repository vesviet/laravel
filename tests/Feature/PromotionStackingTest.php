<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Services\PromotionEngine;

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Lighting',
        'slug' => 'lighting',
        'is_active' => true,
    ]);

    $this->productA = Product::create([
        'name' => 'Flash Sale Lamp',
        'slug' => 'flash-sale-lamp',
        'price' => 500000,
        'stock' => 10,
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);

    $this->productB = Product::create([
        'name' => 'Regular Chair',
        'slug' => 'regular-chair',
        'price' => 300000,
        'stock' => 10,
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);

    $this->flashSale = FlashSale::create([
        'name' => 'Super Flash Sale',
        'start_time' => now()->subHour(),
        'end_time' => now()->addHours(2),
        'is_active' => true,
    ]);

    FlashSaleItem::create([
        'flash_sale_id' => $this->flashSale->id,
        'product_id' => $this->productA->id,
        'price' => 450000,
        'quantity' => 10,
        'sold_quantity' => 0,
    ]);
});

it('strictly excludes flash sale items from percentage coupons', function () {
    Coupon::create([
        'code' => 'DISCOUNT10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
    ]);

    $cartItems = [
        [
            'id' => $this->productA->id,
            'name' => $this->productA->name,
            'price' => 450000,
            'quantity' => 1,
            'subtotal' => 450000,
            'is_flash_sale' => true,
        ],
        [
            'id' => $this->productB->id,
            'name' => $this->productB->name,
            'price' => 300000,
            'quantity' => 1,
            'subtotal' => 300000,
            'is_flash_sale' => false,
        ],
    ];

    $subtotal = 750000.0;
    $engine = app(PromotionEngine::class);

    $discount = $engine->calculateDiscount($subtotal, $cartItems, 'DISCOUNT10', 30000);

    // 10% of 300,000 (Product B) = 30,000. Product A (Flash sale) is protected from discount.
    expect($discount)->toBe(30000.0);
});

it('allows freeship coupon to discount shipping fee on flash sale orders', function () {
    Coupon::create([
        'code' => 'FREESHIP',
        'type' => 'free_shipping',
        'value' => 0,
        'is_active' => true,
    ]);

    $cartItems = [
        [
            'id' => $this->productA->id,
            'name' => $this->productA->name,
            'price' => 450000,
            'quantity' => 1,
            'subtotal' => 450000,
            'is_flash_sale' => true,
        ],
    ];

    $subtotal = 450000.0;
    $shippingFee = 35000.0;
    $engine = app(PromotionEngine::class);

    $discount = $engine->calculateDiscount($subtotal, $cartItems, 'FREESHIP', $shippingFee);

    expect($discount)->toBe(35000.0);
});
