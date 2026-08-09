<?php

use App\Services\InventoryService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->inventoryService = new InventoryService();
});

it('deducts stock for an order', function () {
    $product = Product::create(['name' => 'P1', 'slug' => \Illuminate\Support\Str::random(10), 'price' => 10, 'stock' => 10]);
    $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'V1', 'price' => 15, 'stock' => 5]);

    $order = Order::create(['customer_name' => 'Test', 'phone' => '123', 'address' => 'Addr', 'order_number' => \Illuminate\Support\Str::random(10), 'payment_method' => 'cod', 'subtotal' => 25, 'total_amount' => 25]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_variant_id' => null,
        'quantity' => 2,
        'price_at_purchase' => 10,
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
        'price_at_purchase' => 15,
    ]);

    $this->inventoryService->deductStock($order);

    expect($product->fresh()->stock)->toBe(8)
        ->and($variant->fresh()->stock)->toBe(4);
});

it('restores stock for an order', function () {
    $product = Product::create(['name' => 'P2', 'slug' => \Illuminate\Support\Str::random(10), 'price' => 10, 'stock' => 8]);
    $variant = ProductVariant::create(['product_id' => $product->id, 'name' => 'V1', 'price' => 15, 'stock' => 4]);

    $order = Order::create(['customer_name' => 'Test', 'phone' => '123', 'address' => 'Addr', 'order_number' => \Illuminate\Support\Str::random(10), 'payment_method' => 'cod', 'subtotal' => 25, 'total_amount' => 25]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_variant_id' => null,
        'quantity' => 2,
        'price_at_purchase' => 10,
    ]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
        'price_at_purchase' => 15,
    ]);

    $this->inventoryService->restoreStock($order);

    expect($product->fresh()->stock)->toBe(10)
        ->and($variant->fresh()->stock)->toBe(5);
});

it('throws exception when stock is insufficient', function () {
    $product = Product::create(['name' => 'P3', 'slug' => \Illuminate\Support\Str::random(10), 'price' => 10, 'stock' => 1]);

    $order = Order::create(['customer_name' => 'Test', 'phone' => '123', 'address' => 'Addr', 'order_number' => \Illuminate\Support\Str::random(10), 'payment_method' => 'cod', 'subtotal' => 20, 'total_amount' => 20]);
    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'product_variant_id' => null,
        'quantity' => 2,
        'price_at_purchase' => 10,
    ]);

    $this->inventoryService->deductStock($order);
})->throws(Exception::class);
