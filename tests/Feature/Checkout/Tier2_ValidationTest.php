<?php

use App\Models\Product;
use Illuminate\Support\Facades\Session;

beforeEach(function () {
    $this->product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 100,
        'stock' => 10,
        'status' => 'published'
    ]);

    Session::put('cart', [
        $this->product->id . '_0' => [
            'product_id' => $this->product->id,
            'product_variant_id' => null,
            'variant_id' => null,
            'quantity' => 1,
            'price' => 100,
            'name' => 'Test Product',
            'variant_name' => null,
        ]
    ]);
});

it('requires essential fields for checkout', function () {
    $response = $this->post('/checkout', []);

    $response->assertSessionHasErrors(['customer_name', 'phone', 'address']);
});

it('validates phone number format for Vietnam', function () {
    $response = $this->post('/checkout', [
        'customer_name' => 'John Doe',
        'phone' => '1234567890', // Invalid VN phone
        'address' => '123 Main St',
        'payment_method' => 'cod',
    ]);

    $response->assertSessionHasErrors(['phone']);
});

it('requires cart to have items to checkout', function () {
    Session::forget('cart');

    $response = $this->post('/checkout', [
        'customer_name' => 'John Doe',
        'phone' => '0901234567',
        'address' => '123 Main St',
    ]);

    $response->assertRedirect('/products');
});
