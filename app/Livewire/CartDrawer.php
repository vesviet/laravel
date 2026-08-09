<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public $cart = [];
    public $isOpen = false;

    public function mount()
    {
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
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

    public function updateQuantity($key, $qty)
    {
        if ($qty < 1) {
            return;
        }
        
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $qty;
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem($key)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
