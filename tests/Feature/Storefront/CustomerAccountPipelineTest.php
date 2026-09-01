<?php

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;

beforeEach(function () {
    $this->customer = Customer::create([
        'name' => 'Nguyen Van Customer',
        'email' => 'customer_vip@example.com',
        'phone' => '0912999888',
        'password' => 'secret123',
        'status' => 'published',
    ]);

    $this->otherCustomer = Customer::create([
        'name' => 'Other Customer',
        'email' => 'other_customer@example.com',
        'phone' => '0933888777',
        'password' => 'secret123',
        'status' => 'published',
    ]);

    $this->category = Category::create([
        'name' => 'Home Decor',
        'slug' => 'home-decor',
    ]);

    $this->product = Product::create([
        'name' => 'Nordic Ceramic Vase',
        'slug' => 'nordic-ceramic-vase',
        'sku' => 'VASE-002',
        'price' => 2000000,
        'stock' => 10,
        'category_id' => $this->category->id,
        'status' => 'published',
    ]);
});

it('calculates customer lifetime spend and membership tier correctly', function () {
    expect($this->customer->total_spent)->toBe(0);
    expect($this->customer->membership_tier)->toBe('ThÃ nh ViÃªn Má»›i');

    // Create a delivered order worth 6,000,000 VND -> Membership Tier becomes 'ThÃ nh ViÃªn ThÃ¢n Thiáº¿t'
    Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-TIER-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Delivered,
        'payment_method' => 'cod',
        'subtotal' => 6000000,
        'total_amount' => 6000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    $this->customer->refresh();
    expect($this->customer->total_spent)->toBe(6000000);
    expect($this->customer->formatted_total_spent)->toBe('6.000.000â‚«');
    expect($this->customer->membership_tier)->toBe('ThÃ nh ViÃªn ThÃ¢n Thiáº¿t');
});

it('displays customer order history with status filtering tabs', function () {
    // Order 1: Pending
    Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-PENDING-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Pending,
        'payment_method' => 'cod',
        'subtotal' => 2000000,
        'total_amount' => 2000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    // Order 2: Delivered
    Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-DELIVERED-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Delivered,
        'payment_method' => 'cod',
        'subtotal' => 4000000,
        'total_amount' => 4000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    $this->actingAs($this->customer, 'customer');

    // All tabs
    $response = $this->get(route('account.orders'));
    $response->assertStatus(200);
    $response->assertSee('ORD-PENDING-001');
    $response->assertSee('ORD-DELIVERED-001');

    // Filter processing tab
    $responseProcessing = $this->get(route('account.orders', ['status' => 'processing']));
    $responseProcessing->assertStatus(200);
    $responseProcessing->assertSee('ORD-PENDING-001');
    $responseProcessing->assertDontSee('ORD-DELIVERED-001');

    // Filter delivered tab
    $responseDelivered = $this->get(route('account.orders', ['status' => 'delivered']));
    $responseDelivered->assertStatus(200);
    $responseDelivered->assertSee('ORD-DELIVERED-001');
    $responseDelivered->assertDontSee('ORD-PENDING-001');
});

it('allows customer to view order detail and rejects access to other customer order', function () {
    $order = Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-DETAIL-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Processing,
        'payment_method' => 'cod',
        'subtotal' => 2000000,
        'total_amount' => 2000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'sku' => $this->product->sku,
        'price_at_purchase' => 2000000,
        'quantity' => 1,
    ]);

    $this->actingAs($this->customer, 'customer');

    $response = $this->get(route('account.orders.show', $order->order_number));
    $response->assertStatus(200);
    $response->assertSee('ORD-DETAIL-001');
    $response->assertSee('Nordic Ceramic Vase');
    $response->assertSee('2.000.000â‚«');

    // Other customer attempts to view -> 404
    $this->actingAs($this->otherCustomer, 'customer');
    $responseOther = $this->get(route('account.orders.show', $order->order_number));
    $responseOther->assertStatus(404);
});

it('allows customer to cancel pending order and restores inventory', function () {
    $this->product->decrement('stock', 2);
    expect($this->product->fresh()->stock)->toBe(8);

    $order = Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-CUST-CANCEL-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Pending,
        'payment_method' => 'cod',
        'subtotal' => 4000000,
        'total_amount' => 4000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'sku' => $this->product->sku,
        'price_at_purchase' => 2000000,
        'quantity' => 2,
    ]);

    $this->actingAs($this->customer, 'customer');

    $response = $this->post(route('account.orders.cancel', $order->order_number));
    $response->assertSessionHas('success');

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
    // Inventory restored from 8 to 10
    expect($this->product->fresh()->stock)->toBe(10);
});

it('allows customer 1-click reorder to populate cart with active products', function () {
    $order = Order::create([
        'customer_id' => $this->customer->id,
        'order_number' => 'ORD-REORDER-001',
        'customer_name' => $this->customer->name,
        'phone' => $this->customer->phone,
        'email' => $this->customer->email,
        'address' => '123 Nguyen Trai',
        'status' => OrderStatus::Delivered,
        'payment_method' => 'cod',
        'subtotal' => 2000000,
        'total_amount' => 2000000,
        'discount_amount' => 0,
        'shipping_fee' => 0,
    ]);

    $order->items()->create([
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'sku' => $this->product->sku,
        'price_at_purchase' => 2000000,
        'quantity' => 2,
    ]);

    $this->actingAs($this->customer, 'customer');

    $cartService = app(CartService::class);
    $cartService->clear();

    $response = $this->post(route('account.orders.reorder', $order->order_number));
    $response->assertRedirect(route('checkout.index'));
    $response->assertSessionHas('success');

    $cartSummary = $cartService->getSummary();
    expect($cartSummary['is_empty'])->toBeFalse();
    expect($cartSummary['item_count'])->toBe(2);
});
