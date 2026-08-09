<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(CartService $cartService): void
    {
        $this->updateCount($cartService);
    }

    #[On('cart-updated')]
    public function updateCount(CartService $cartService): void
    {
        $cart = $cartService->getCart();
        $this->count = (int) array_sum(array_column($cart, 'quantity'));
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
