<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;

class WishlistButton extends Component
{
    public Product $product;
    public bool $isWishlisted = false;

    public function mount(Product $product)
    {
        $this->product = $product;
        
        if (Auth::guard('customer')->check()) {
            $this->isWishlisted = Wishlist::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $this->product->id)
                ->exists();
        }
    }

    public function toggleWishlist()
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('account.login');
        }

        $customerId = Auth::guard('customer')->id();

        if ($this->isWishlisted) {
            Wishlist::where('customer_id', $customerId)
                ->where('product_id', $this->product->id)
                ->delete();
            $this->isWishlisted = false;
        } else {
            Wishlist::create([
                'customer_id' => $customerId,
                'product_id' => $this->product->id,
            ]);
            $this->isWishlisted = true;
        }
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
