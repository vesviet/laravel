<?php

use App\Services\CartService;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;


beforeEach(function () {
    $this->cartService = new CartService();
    Session::flush();
});

test('cart is empty by default', function () {
    expect($this->cartService->isEmpty())->toBeTrue();
    expect($this->cartService->getItemCount())->toBe(0);
});

test('can add item to cart', function () {
    $this->cartService->addItem(1, 'Test Product', 100000, 2);
    
    expect($this->cartService->isEmpty())->toBeFalse();
    expect($this->cartService->getItemCount())->toBe(2);
    expect($this->cartService->getTotal())->toBe(200000.0);
});

test('can update item quantity', function () {
    $this->cartService->addItem(1, 'Test Product', 100000, 1);
    $this->cartService->updateQuantity(1, 5);
    
    expect($this->cartService->getItemCount())->toBe(5);
    expect($this->cartService->getTotal())->toBe(500000.0);
});

test('can remove item from cart', function () {
    $this->cartService->addItem(1, 'Test Product', 100000, 1);
    $this->cartService->removeItem(1);
    
    expect($this->cartService->isEmpty())->toBeTrue();
});

test('can clear cart', function () {
    $this->cartService->addItem(1, 'Test Product', 100000, 1);
    $this->cartService->clear();
    
    expect($this->cartService->isEmpty())->toBeTrue();
});
