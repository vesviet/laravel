<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Session;

it('prevents overselling using DB locks during concurrent-like transactions', function () {
    $product = Product::create([
        'name' => 'Limited Product',
        'slug' => 'limited-product',
        'price' => 100,
        'stock' => 1,
        'status' => 'published',
    ]);

    $order1 = Order::create(['customer_name' => 'User 1', 'phone' => '123', 'address' => 'HCM', 'order_number' => 'ORD-1', 'subtotal' => 100, 'total_amount' => 100]);
    $order1->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price_at_purchase' => 100,
        'quantity' => 1,
    ]);

    $order2 = Order::create(['customer_name' => 'User 2', 'phone' => '1234', 'address' => 'HN', 'order_number' => 'ORD-2', 'subtotal' => 100, 'total_amount' => 100]);
    $order2->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price_at_purchase' => 100,
        'quantity' => 1,
    ]);

    $inventoryService = new InventoryService;

    // Simulate successful order 1
    $inventoryService->deductStock($order1);

    expect($product->fresh()->stock)->toBe(0);

    // Simulate order 2 trying to deduct stock — should throw Exception
    $inventoryService->deductStock($order2);
})->throws(Exception::class, 'Insufficient stock for product');

it('fails checkout if variant is out of stock', function () {
    $product = Product::create([
        'name' => 'P1',
        'slug' => 'p1-'.uniqid(),
        'price' => 10,
        'stock' => 10,
        'status' => 'published',
    ]);
    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'name' => 'V1',
        'price' => 15,
        'stock' => 0, // Out of stock
    ]);

    // Cart uses CartService schema (product_variant_id).
    Session::put('cart', [
        "{$product->id}_{$variant->id}" => [
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ],
    ]);

    $response = $this->post('/checkout', [
        'customer_name' => 'John',
        'phone' => '0901234567',
        'address' => 'HCM',
        'payment_method' => 'cod',
    ]);

    // CheckoutRequest StockAvailable rule fires → validation fails → redirect back with validation errors.
    $response->assertSessionHasErrors();
});

it('fails checkout if cart is expired (session cleared)', function () {
    // No cart in session — controller redirects to products before validation.
    $response = $this->post('/checkout', [
        'customer_name' => 'John',
        'phone' => '0901234567',
        'address' => 'HCM',
        'payment_method' => 'cod',
    ]);

    $response->assertRedirect('/products');
});
