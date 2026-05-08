<?php

use App\Models\Product;
use App\Models\Category;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can view checkout page', function () {
    $response = $this->get(route('checkout'));
    $response->assertStatus(200);
});

test('cannot checkout with empty cart', function () {
    $this->withoutMiddleware();
    $response = $this->post(route('checkout.store'), [
        'customer_name' => 'John Doe',
        'phone' => '0901234567',
        'address' => '123 Street',
        'payment_method' => 'cod',
    ]);

    $response->assertSessionHas('error', 'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
});

test('can checkout with items in cart', function () {
    $this->withoutMiddleware();
    // 1. Setup Data
    $category = Category::factory()->create(['type' => 'product']);
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'price' => 1000000,
        'stock' => 10,
        'is_active' => true,
    ]);

    // 2. Add to cart
    $cartService = app(CartService::class);
    $cartService->addItem($product->id, $product->name, $product->price, 1);

    // 3. Post Checkout
    $response = $this->post(route('checkout.store'), [
        'customer_name' => 'Tuan Anh',
        'phone' => '0901234567',
        'email' => 'tuan@example.com',
        'address' => 'HCM City',
        'payment_method' => 'cod',
    ]);

    // 4. Assertions
    $response->assertRedirect();
    $this->assertDatabaseHas('orders', [
        'customer_name' => 'Tuan Anh',
        'total_amount' => 1000000,
    ]);
    
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 9, // Decremented
    ]);

    expect($cartService->isEmpty())->toBeTrue();
});
