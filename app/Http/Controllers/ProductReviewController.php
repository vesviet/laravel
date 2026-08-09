<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Store a new product review.
     * Requires verified purchase: customer must have at least one 'delivered' order
     * containing this product.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $customerId = auth('customer')->id();

        if (! $customerId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verified Purchase: customer must have a 'delivered' order containing this product.
        // 'delivered' is the final success state in the order enum
        // ['pending','confirmed','shipping','delivered','cancelled'].
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
            return response()->json([
                'message' => 'You must purchase and receive this product before reviewing.',
            ], 403);
        }

        // Prevent duplicate reviews per product per customer.
        $existingReview = ProductReview::where('product_id', $productId)
            ->where('customer_id', $customerId)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this product.'], 403);
        }

        $review = ProductReview::create([
            'product_id' => $productId,
            'customer_id' => $customerId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending', // Requires admin approval.
        ]);

        return response()->json([
            'message' => 'Review submitted successfully and is pending approval.',
            'review' => $review,
        ], 201);
    }
}
