<?php

namespace App\Services;

class PromotionEngine
{
    /**
     * Calculate discount amount based on subtotal, cart items, combo rules, and coupons.
     * This is a simplified engine for MVP.
     */
    public function calculateDiscount(float $subtotal, array $cartItems, ?string $couponCode = null): float
    {
        $discount = 0.0;

        // Calculate eligible subtotal and items for promotion
        $eligibleSubtotal = 0;
        $eligibleItemsCount = 0;
        foreach ($cartItems as $item) {
            if (empty($item['is_flash_sale'])) {
                $eligibleSubtotal += $item['subtotal'] ?? ($item['price'] * $item['quantity']);
                $eligibleItemsCount += $item['quantity'];
            }
        }

        // Example: Apply combo rule
        // e.g., buy 2 or more items, get 5% off
        if ($eligibleItemsCount >= 2) {
            $comboDiscount = $eligibleSubtotal * 0.05; // 5% discount
            $discount += $comboDiscount;
        }

        // Example: Apply coupon code
        if ($couponCode === 'WELCOME10') {
            $couponDiscount = $eligibleSubtotal * 0.10; // 10% discount
            $discount += $couponDiscount;
        }

        // Ensure discount does not exceed subtotal
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return $discount;
    }
}
