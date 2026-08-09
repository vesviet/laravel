<?php

use App\Services\CartService;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;

beforeEach(function () {
    $this->cartService = new CartService();
    Session::start();
});

it('can add item to cart', function () {
    $this->cartService->add(1, null, 2);
    
    $cart = $this->cartService->getCart();
    expect($cart)->toHaveKey('1_0')
        ->and($cart['1_0']['quantity'])->toBe(2);
});

it('can update cart item quantity', function () {
    $this->cartService->add(1, 2, 1);
    $this->cartService->update(1, 2, 5);

    $cart = $this->cartService->getCart();
    expect($cart['1_2']['quantity'])->toBe(5);
});

it('can remove item from cart', function () {
    $this->cartService->add(1, null, 1);
    $this->cartService->remove(1, null);

    $cart = $this->cartService->getCart();
    expect($cart)->toBeEmpty();
});

it('can clear cart', function () {
    $this->cartService->add(1, null, 1);
    $this->cartService->clear();

    $cart = $this->cartService->getCart();
    expect($cart)->toBeEmpty();
});

it('can calculate total', function () {
    $product1 = Product::create(['name' => 'P1', 'slug' => 'p1', 'price' => 100, 'stock' => 10]);
    $product2 = Product::create(['name' => 'P2', 'slug' => 'p2', 'price' => 200, 'stock' => 10]);
    $variant = ProductVariant::create(['product_id' => $product2->id, 'name' => 'V1', 'price' => 250, 'stock' => 5]);

    $this->cartService->add($product1->id, null, 2); // 200
    $this->cartService->add($product2->id, $variant->id, 1); // 250

    expect($this->cartService->calculateTotal())->toBe(450.0);
});
