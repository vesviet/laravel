<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    /** @var array Enriched cart items from CartService */
    public array $cartItems = [];

    public function mount(CartService $cartService): void
    {
        $this->loadCart($cartService);
    }

    #[On('cart-updated')]
    public function loadCart(CartService $cartService): void
    {
        $this->cartItems = $cartService->getCartItemsDetails();
    }

    #[On('open-cart')]
    public function openCart(): void
    {
        $this->isOpen = true;
    }

    public function closeCart(): void
    {
        $this->isOpen = false;
    }

    public function updateQuantity(int $productId, ?int $variantId, int $qty, CartService $cartService): void
    {
        if ($qty < 1) {
            return;
        }

        $cartService->update($productId, $variantId, $qty);
        $this->loadCart($cartService);
        $this->dispatch('cart-updated');
    }

    public function removeItem(int $productId, ?int $variantId, CartService $cartService): void
    {
        $cartService->remove($productId, $variantId);
        $this->loadCart($cartService);
        $this->dispatch('cart-updated');
    }

    #[Computed]
    public function subtotal(): float
    {
        return (float) array_sum(array_column($this->cartItems, 'subtotal'));
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
