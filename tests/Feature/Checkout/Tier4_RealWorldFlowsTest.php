<?php

use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Session;

it('guest checkout and order lookup flow', function () {
    $product = Product::create(['name' => 'Test Product', 'slug' => 'test-product', 'price' => 100, 'stock' => 10]);

    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'variant_id' => null,
            'quantity' => 1,
            'price' => 100,
            'name' => 'Test Product',
            'variant_name' => null,
        ]
    ]);

    // 1. Guest checks out
    $checkoutResponse = $this->post('/checkout', [
        'customer_name' => 'Guest User',
        'phone' => '0901234567',
        'address' => '123 Guest St',
        'payment_method' => 'cod',
    ]);

    $checkoutResponse->assertSessionHasNoErrors();
    $checkoutResponse->assertRedirect();
    $order = Order::latest('id')->first();
    expect($order->customer_name)->toBe('Guest User');

    // 2. Lookup order
    $lookupResponse = $this->post('/track-order', [
        'order_number' => $order->order_number,
        'phone' => '0901234567',
    ]);

    $lookupResponse->assertRedirect(route('track-order.index', ['order_number' => $order->order_number]));
    
    $this->get(route('track-order.index', ['order_number' => $order->order_number]))
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('pending');
});

it('applies combo discount and coupon in checkout', function () {
    $product = Product::create(['name' => 'Test Product 2', 'slug' => 'test-product-2', 'price' => 100, 'stock' => 10]);

    // Add 2 items for combo discount
    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'variant_id' => null,
            'quantity' => 2,
            'price' => 100,
            'name' => 'Test Product 2',
            'variant_name' => null,
        ]
    ]);
    Session::put('coupon', 'WELCOME10'); // Simulated coupon in session

    $response = $this->post('/checkout', [
        'customer_name' => 'Discount User',
        'phone' => '0901234567',
        'address' => '123 Discount St',
        'payment_method' => 'cod',
    ]);

    $response->assertRedirect();

    $order = Order::latest('id')->first();
    // Subtotal: 200
    // Combo: 5% of 200 = 10
    // Coupon: 10% of 200 = 20
    // Total discount: 30
    // Total amount: 170
    expect((float) $order->discount_amount)->toBe(30.0)
        ->and((float) $order->total_amount)->toBe(170.0);
});

it('customer account order history flow', function () {
    $customer = Customer::create([
        'name' => 'Logged In User',
        'email' => 'user@example.com',
        'password' => bcrypt('password'),
        'phone' => '0901234567',
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'customer_name' => 'Logged In User',
        'phone' => '0901234567',
        'address' => '123 Address',
        'order_number' => 'ORD-TEST',
        'subtotal' => 100,
        'total_amount' => 100,
        'payment_method' => 'cod',
    ]);

    $response = $this->actingAs($customer, 'customer')->get('/account/orders');

    $response->assertOk()
        ->assertSee($order->order_number);
});
