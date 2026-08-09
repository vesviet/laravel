<?php

use App\Services\PromotionEngine;

beforeEach(function () {
    $this->engine = new PromotionEngine();
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

it('applies coupon discount', function () {
    $subtotal = 1000.0;
    $cartItems = [
        ['quantity' => 1, 'subtotal' => 1000.0],
    ];

    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'WELCOME10');
    expect($discount)->toBe(100.0); // 10% of 1000 = 100
});

it('stacks combo and coupon discounts', function () {
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

it('limits discount to subtotal', function () {
    $subtotal = 0.0;
    $cartItems = [
        ['quantity' => 2, 'subtotal' => 0.0],
    ];
    $discount = $this->engine->calculateDiscount($subtotal, $cartItems, 'WELCOME10');
    expect($discount)->toBe(0.0);
});
