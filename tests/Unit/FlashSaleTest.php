<?php

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PromotionEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('flash sale price overrides normal price when active', function () {
    $product = Product::create([
        'name' => 'Flash Product',
        'slug' => 'flash-product-'.time(),
        'price' => 100000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $flashSale = FlashSale::create([
        'name' => 'Tet Sale',
        'start_time' => Carbon::now()->addHour(),
        'end_time' => Carbon::now()->addHours(3),
        'status' => 'published',
    ]);

    FlashSaleItem::create([
        'flash_sale_id' => $flashSale->id,
        'product_id' => $product->id,
        'price' => 50000,
        'quantity' => 100,
        'sold_quantity' => 0,
    ]);

    $cartService = app(CartService::class);

    // Before flash sale
    $cartService->add($product->id, null, 1);
    expect($cartService->calculateTotal())->toBe(100000.0);
    $cartService->clear();

    // Time travel to flash sale
    Carbon::setTestNow(Carbon::now()->addHours(2));
    $cartService->add($product->id, null, 1);
    expect($cartService->calculateTotal())->toBe(50000.0);
    $cartService->clear();

    // Time travel to after flash sale
    Carbon::setTestNow(Carbon::now()->addHours(4));
    $cartService->add($product->id, null, 1);
    expect($cartService->calculateTotal())->toBe(100000.0);
    $cartService->clear();

    Carbon::setTestNow();
});

it('disables other combo and coupon promotions when flash sale is active', function () {
    $product = Product::create([
        'name' => 'Flash Product 2',
        'slug' => 'flash-product-2-'.time(),
        'price' => 100000,
        'stock' => 10,
        'status' => 'published',
    ]);

    $flashSale = FlashSale::create([
        'name' => 'Tet Sale',
        'start_time' => Carbon::now()->subHour(),
        'end_time' => Carbon::now()->addHours(3),
        'status' => 'published',
    ]);

    FlashSaleItem::create([
        'flash_sale_id' => $flashSale->id,
        'product_id' => $product->id,
        'price' => 50000,
        'quantity' => 100,
        'sold_quantity' => 0,
    ]);

    $engine = app(PromotionEngine::class);
    $cart = [
        ['product_id' => $product->id, 'quantity' => 2, 'price' => 50000, 'is_flash_sale' => true],
    ];

    $discount = $engine->calculateDiscount(100000, $cart);

    // Flash sale is exclusive, so no combo discount applied
    expect($discount)->toBe(0.0);
});
