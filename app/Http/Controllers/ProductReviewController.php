<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\ProductReview;

class ProductReviewController extends Controller
{
    /**
     * Store a new product review.
     * Requires verified purchase: customer must have at least one 'delivered' order
     * containing this product (handled in StoreProductReviewRequest).
     */
    public function store(StoreProductReviewRequest $request, $productId)
    {
        $review = ProductReview::create([
            'product_id' => $productId,
            'customer_id' => auth('customer')->id(),
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
