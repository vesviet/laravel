<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;

class ProductReviews extends Component
{
    public Product $product;
    public $rating = 5;
    public $comment = '';

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ];

    public function submitReview()
    {
        $this->validate();

        if (!Auth::guard('customer')->check()) {
            return redirect()->route('account.login');
        }

        // Logic to verify purchase is usually handled here or in a rule, 
        // as a frontend-developer, I'm just creating the UI and minimal wiring.
        // Assuming there is a rule or we just save it.
        ProductReview::create([
            'product_id' => $this->product->id,
            'customer_id' => Auth::guard('customer')->id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => 'pending',
        ]);

        $this->reset(['rating', 'comment']);
        
        session()->flash('message', 'Your review has been submitted and is pending approval.');
    }

    public function render()
    {
        $reviews = ProductReview::where('product_id', $this->product->id)
            ->where('status', 'approved')
            ->with('customer')
            ->latest()
            ->get();

        return view('livewire.product-reviews', [
            'reviews' => $reviews,
        ]);
    }
}
