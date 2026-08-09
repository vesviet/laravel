<?php

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('coupon is applicable when active and not expired', function () {
    $coupon = new Coupon([
        'is_active' => true,
        'expires_at' => null,
        'usage_limit' => null,
        'used_count' => 0,
        'min_order_amount' => 0,
        'value' => 10,
        'type' => 'percentage',
    ]);

    expect($coupon->isApplicable(100.0))->toBeTrue();
});

it('coupon is not applicable when inactive', function () {
    $coupon = new Coupon([
        'is_active' => false,
        'expires_at' => null,
        'usage_limit' => null,
        'used_count' => 0,
        'min_order_amount' => 0,
    ]);

    expect($coupon->isApplicable(100.0))->toBeFalse();
});

it('coupon is not applicable when expired', function () {
    $coupon = new Coupon([
        'is_active' => true,
        'expires_at' => now()->subHour(),
        'usage_limit' => null,
        'used_count' => 0,
        'min_order_amount' => 0,
    ]);

    expect($coupon->isApplicable(100.0))->toBeFalse();
});

it('coupon is not applicable when usage limit reached', function () {
    $coupon = new Coupon([
        'is_active' => true,
        'expires_at' => null,
        'usage_limit' => 10,
        'used_count' => 10,
        'min_order_amount' => 0,
    ]);

    expect($coupon->isApplicable(100.0))->toBeFalse();
});

it('coupon is not applicable below minimum order', function () {
    $coupon = new Coupon([
        'is_active' => true,
        'expires_at' => null,
        'usage_limit' => null,
        'used_count' => 0,
        'min_order_amount' => 500,
    ]);

    expect($coupon->isApplicable(200.0))->toBeFalse();
    expect($coupon->isApplicable(500.0))->toBeTrue();
});

it('calculates percentage discount correctly', function () {
    $coupon = new Coupon(['type' => 'percentage', 'value' => 10]);

    expect($coupon->calculateDiscount(1000.0))->toBe(100.0);
});

it('calculates fixed discount correctly', function () {
    $coupon = new Coupon(['type' => 'fixed', 'value' => 50000]);

    expect($coupon->calculateDiscount(200000.0))->toBe(50000.0);
});

it('fixed discount is capped at subtotal', function () {
    $coupon = new Coupon(['type' => 'fixed', 'value' => 500]);

    // Discount should not exceed subtotal
    expect($coupon->calculateDiscount(100.0))->toBe(100.0);
});
