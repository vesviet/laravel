<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    /**
     * Store a new product review.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $customerId = auth('customer')->id();

        if (!$customerId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verified Purchase Logic: Customer must have at least one completed order containing this product
        $hasPurchased = Order::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($productId) {
                // If order item stores product_variant_id, we need to join or relate to product.
                // Assuming OrderItem belongsTo ProductVariant and ProductVariant belongsTo Product
                $query->whereHas('productVariant', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })->orWhere('product_id', $productId); // Fallback if order item directly has product_id
            })
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => 'You must purchase and receive this product before reviewing.'
            ], 403);
        }

        // Check if already reviewed (optional but good practice)
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
            'status' => 'pending', // Requires admin approval based on rules
        ]);

        return response()->json([
            'message' => 'Review submitted successfully and is pending approval.',
            'review' => $review
        ], 201);
    }
}
