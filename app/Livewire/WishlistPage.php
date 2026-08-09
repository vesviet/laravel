<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use Livewire\Attributes\Layout;

#[Layout('layouts.storefront')]
class WishlistPage extends Component
{
    public function removeFromWishlist($wishlistId)
    {
        Wishlist::where('id', $wishlistId)
            ->where('customer_id', Auth::guard('customer')->id())
            ->delete();
    }

    public function addToCart($productId)
    {
        // Emit an event to the AddToCartButton or Cart component
        // But since we just need the Add to Cart button, we can either use Livewire event or a method.
        // Assuming there is a cart component listening to 'addToCart'
        $this->dispatch('add-to-cart', productId: $productId);
    }

    public function render()
    {
        $wishlists = Wishlist::with('product')
            ->where('customer_id', Auth::guard('customer')->id())
            ->latest()
            ->get();

        return view('livewire.wishlist-page', [
            'wishlists' => $wishlists,
        ]);
    }
}
