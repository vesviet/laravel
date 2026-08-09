<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class AddToCartButton extends Component
{
    public Product $product;
    public $quantity = 1;
    public $variantId = null;

    public function mount(Product $product)
    {
        $this->product = $product;
        if ($this->product->variants->count() > 0) {
            $this->variantId = $this->product->variants->first()->id;
        }
    }

    public function increment()
    {
        $this->quantity++;
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        // Simple session-based cart
        $cart = session()->get('cart', []);
        
        $key = $this->product->id . ($this->variantId ? '_' . $this->variantId : '');
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $this->quantity;
        } else {
            $variant = $this->variantId ? $this->product->variants->where('id', $this->variantId)->first() : null;
            
            $cart[$key] = [
                'product_id' => $this->product->id,
                'variant_id' => $this->variantId,
                'name' => $this->product->name,
                'variant_name' => $variant ? $variant->name : null,
                'price' => $variant ? $variant->price : $this->product->price,
                'quantity' => $this->quantity,
                'image_path' => $this->product->image_path,
            ];
        }
        
        session()->put('cart', $cart);
        
        $this->dispatch('cart-updated');
        $this->dispatch('open-cart');
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
