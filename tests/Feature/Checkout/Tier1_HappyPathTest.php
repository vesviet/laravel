<?php

use App\Models\Product;
use Illuminate\Support\Facades\Session;

it('allows a guest to checkout successfully with COD', function () {
    $product = Product::create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'price' => 100,
        'stock' => 10,
        'status' => 'published',
    ]);

    // Cart session format matches CartService schema (product_variant_id, not variant_id).
    Session::put('cart', [
        "{$product->id}_0" => [
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
        ],
    ]);

    $response = $this->post('/checkout', [
        'customer_name' => 'John Doe',
        'phone' => '0901234567',
        'email' => 'john@example.com',
        'address' => '123 Main St',
        'payment_method' => 'cod',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'customer_name' => 'John Doe',
        'phone' => '0901234567',
        'total_amount' => 100,
        'status' => 'pending',
    ]);

    // Cart should be cleared after successful checkout.
    expect(Session::get('cart'))->toBeNull();
});
