<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount()
    {
        $this->count = app(CartService::class)->getItemCount();
    }

    #[On('cart-count-updated')]
    public function updateCount()
    {
        $this->count = app(CartService::class)->getItemCount();
    }

    public function openCart()
    {
        $this->dispatch('open-cart');
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}
