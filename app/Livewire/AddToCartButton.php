<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Models\Product;
use Livewire\Component;

class AddToCartButton extends Component
{
    public int $productId;
    public string $productName;
    public float $productPrice;
    public ?string $productImage = null;
    public int $quantity = 1;
    public bool $showQuantity = false;
    public string $size = 'default'; // default, sm, lg

    public function addToCart()
    {
        app(CartService::class)->addItem(
            $this->productId,
            $this->productName,
            $this->productPrice,
            $this->quantity,
            $this->productImage
        );

        $this->dispatch('cart-updated');
        $this->dispatch('cart-count-updated');
        $this->dispatch('open-cart');
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
