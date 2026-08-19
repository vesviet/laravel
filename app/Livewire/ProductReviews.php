<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductReviews extends Component
{
    public Product $product;

    public $rating = 5;

    public $comment = '';

    protected $rules = [
        'rating'  => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'rating.required' => 'Vui lòng chọn số sao đánh giá.',
        'rating.min'      => 'Đánh giá tối thiểu là 1 sao.',
        'rating.max'      => 'Đánh giá tối đa là 5 sao.',
        'comment.max'     => 'Nhận xét không được vượt quá 1000 ký tự.',
    ];

    public function submitReview()
    {
        $this->validate();

        if (! Auth::guard('customer')->check()) {
            return redirect()->route('account.login');
        }

        $customerId = Auth::guard('customer')->id();
        $productId  = $this->product->id;

        // 1. Verified Purchase: customer must have at least one 'delivered' order containing this product.
        $hasPurchased = Order::where('customer_id', $customerId)
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId)
                    ->orWhereHas('productVariant', function ($q) use ($productId) {
                        $q->where('product_id', $productId);
                    });
            })
            ->exists();

        if (! $hasPurchased) {
            $this->addError('purchase_required', 'Bạn cần mua và nhận hàng thành công sản phẩm này trước khi gửi đánh giá.');
            return;
        }

        // 2. Prevent duplicate reviews per product per customer.
        $existingReview = ProductReview::where('product_id', $productId)
            ->where('customer_id', $customerId)
            ->exists();

        if ($existingReview) {
            $this->addError('duplicate_review', 'Bạn đã gửi đánh giá cho sản phẩm này rồi.');
            return;
        }

        ProductReview::create([
            'product_id'  => $productId,
            'customer_id' => $customerId,
            'rating'      => $this->rating,
            'comment'     => $this->comment,
            'status'      => 'pending', // Requires admin approval.
        ]);

        $this->reset(['rating', 'comment']);

        session()->flash('message', 'Đánh giá của bạn đã được gửi thành công và đang chờ ban quản trị kiểm duyệt.');
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
