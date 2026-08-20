<?php

use App\Models\Coupon;
use App\Services\PromotionEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Note: Pest.php applies RefreshDatabase to all Feature tests.
// Unit tests need to explicitly use RefreshDatabase for DB-backed coupon tests.
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->engine = new PromotionEngine;
});

it('applies combo discount for two or more items', function () {
    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 500.0],
        ['quantity' => 1, 'subtotal' => 500.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems);
    expect($discount)->toBe(50.0); // 5% of 1000 = 50
});

it('applies DB-backed coupon percentage discount', function () {
    Coupon::create([
        'code' => 'WELCOME10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
    ]);

    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'WELCOME10');
    expect($discount)->toBe(100.0); // 10% of 1000 = 100
});

it('applies DB-backed coupon fixed discount', function () {
    Coupon::create([
        'code' => 'FIXED50K',
        'type' => 'fixed',
        'value' => 50000,
        'is_active' => true,
    ]);

    $subtotal = 200000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 200000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'FIXED50K');
    expect($discount)->toBe(50000.0);
});

it('stacks combo and coupon discounts', function () {
    Coupon::create([
        'code' => 'WELCOME10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
    ]);

    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 2, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'WELCOME10');
    expect($discount)->toBe(150.0); // 5% combo (50) + 10% coupon (100) = 150
});

it('does not apply combo discount for single item', function () {
    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems);
    expect($discount)->toBe(0.0);
});

it('ignores inactive coupon', function () {
    Coupon::create([
        'code' => 'INACTIVE',
        'type' => 'percentage',
        'value' => 50,
        'is_active' => false,
    ]);

    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'INACTIVE');
    expect($discount)->toBe(0.0);
});

it('ignores expired coupon', function () {
    Coupon::create([
        'code' => 'EXPIRED',
        'type' => 'percentage',
        'value' => 20,
        'is_active' => true,
        'expires_at' => now()->subDay(),
    ]);

    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'EXPIRED');
    expect($discount)->toBe(0.0);
});

it('respects usage limit', function () {
    Coupon::create([
        'code' => 'LIMITED',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
        'usage_limit' => 5,
        'used_count' => 5,
    ]);

    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'LIMITED');
    expect($discount)->toBe(0.0);
});

it('limits discount to subtotal', function () {
    $subtotal = 0.0;
    $cartItems = [
        ['quantity' => 2, 'subtotal' => 0.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, null);
    expect($discount)->toBe(0.0);
});

it('excludes flash sale items from percentage coupon discounts', function () {
    Coupon::create([
        'code' => 'DISCOUNT10',
        'type' => 'percentage',
        'value' => 10,
        'is_active' => true,
    ]);

    $subtotal = 800000.0;
    $cartItems = [
        ['price' => 500000, 'quantity' => 1, 'subtotal' => 500000.0, 'is_flash_sale' => true],
        ['price' => 300000, 'quantity' => 1, 'subtotal' => 300000.0, 'is_flash_sale' => false],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'DISCOUNT10');
    // Only 10% of 300,000 (regular item) = 30,000. Flash sale item (500k) is isolated!
    expect($discount)->toBe(30000.0);
});

it('allows free shipping coupon on flash sale orders', function () {
    Coupon::create([
        'code' => 'FREESHIP',
        'type' => 'free_shipping',
        'value' => 0, // 0 = 100% free shipping
        'is_active' => true,
    ]);

    $subtotal = 500000.0;
    $cartItems = [
        ['price' => 500000, 'quantity' => 1, 'subtotal' => 500000.0, 'is_flash_sale' => true],
    ];

    $shippingFee = 35000.0;
    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'FREESHIP', $shippingFee);
    expect($discount)->toBe(35000.0);
});
