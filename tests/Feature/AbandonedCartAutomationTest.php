<?php

use App\Enums\OrderStatus;
use App\Mail\AbandonedCartIncentiveMail;
use App\Mail\AbandonedCartReminderMail;
use App\Models\AbandonedCart;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

it('dispatches step 1 reminder email for carts older than 1 hour', function () {
    $cart = AbandonedCart::create([
        'email' => 'customer@example.com',
        'cart_token' => 'token_123',
        'items_json' => [
            ['name' => 'Minimalist Table', 'price' => 4500000, 'quantity' => 1],
        ],
        'subtotal' => 4500000,
    ]);

    // Force created_at to 2 hours ago
    $cart->created_at = now()->subHours(2);
    $cart->saveQuietly();

    $this->artisan('carts:process-abandoned')->assertSuccessful();

    Mail::assertQueued(AbandonedCartReminderMail::class, function ($mail) use ($cart) {
        return $mail->abandonedCart->id === $cart->id && $mail->hasTo('customer@example.com');
    });

    $cart->refresh();
    expect($cart->step_1_sent_at)->not->toBeNull();
    expect($cart->step_2_sent_at)->toBeNull();
});

it('dispatches step 2 incentive coupon email 24 hours after step 1', function () {
    $cart = AbandonedCart::create([
        'email' => 'shopper@example.com',
        'cart_token' => 'token_456',
        'items_json' => [
            ['name' => 'Design Lamp', 'price' => 1500000, 'quantity' => 2],
        ],
        'subtotal' => 3000000,
        'step_1_sent_at' => now()->subHours(24),
    ]);

    $cart->created_at = now()->subHours(26);
    $cart->saveQuietly();

    $this->artisan('carts:process-abandoned')->assertSuccessful();

    Mail::assertQueued(AbandonedCartIncentiveMail::class, function ($mail) use ($cart) {
        return $mail->abandonedCart->id === $cart->id && $mail->hasTo('shopper@example.com');
    });

    $cart->refresh();
    expect($cart->step_2_sent_at)->not->toBeNull();
    expect($cart->incentive_coupon_code)->not->toBeNull();

    // Verify coupon was generated in DB
    $coupon = Coupon::where('code', $cart->incentive_coupon_code)->first();
    expect($coupon)->not->toBeNull();
    expect($coupon->type)->toBe('percentage');
    expect($coupon->value)->toBe(5);
    expect($coupon->is_active)->toBeTrue();
});

it('skips cart and marks as recovered if customer already placed an order', function () {
    $cart = AbandonedCart::create([
        'email' => 'converted@example.com',
        'cart_token' => 'token_789',
        'items_json' => [
            ['name' => 'Armchair', 'price' => 2000000, 'quantity' => 1],
        ],
        'subtotal' => 2000000,
    ]);

    $cart->created_at = now()->subHours(3);
    $cart->saveQuietly();

    Order::create([
        'order_number' => 'ORD-1001',
        'customer_name' => 'Converted Customer',
        'phone' => '0901234567',
        'address' => '123 Le Loi',
        'city' => 'HCM',
        'email' => 'converted@example.com',
        'payment_method' => 'cod',
        'status' => OrderStatus::Pending,
        'subtotal' => 2000000,
        'total_amount' => 2000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
        'created_at' => now()->subHour(),
    ]);

    $this->artisan('carts:process-abandoned')->assertSuccessful();

    Mail::assertNothingQueued();

    $cart->refresh();
    expect($cart->recovered_at)->not->toBeNull();
});
