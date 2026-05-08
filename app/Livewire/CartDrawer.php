<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    public function mount()
    {
        $this->isOpen = false;
    }

    #[On('cart-updated')]
    public function refreshCart()
    {
        // Component re-renders automatically
    }

    #[On('open-cart')]
    public function openCart()
    {
        $this->isOpen = true;
    }

    public function closeCart()
    {
        $this->isOpen = false;
    }

    public function updateQuantity(int $productId, int $quantity)
    {
        app(CartService::class)->updateQuantity($productId, $quantity);
        $this->dispatch('cart-count-updated');
    }

    public function removeItem(int $productId)
    {
        app(CartService::class)->removeItem($productId);
        $this->dispatch('cart-count-updated');
    }

    public function render()
    {
        $cartService = app(CartService::class);

        return view('livewire.cart-drawer', [
            'items' => $cartService->getItems(),
            'total' => $cartService->getFormattedTotal(),
            'itemCount' => $cartService->getItemCount(),
            'isEmpty' => $cartService->isEmpty(),
        ]);
    }
}
