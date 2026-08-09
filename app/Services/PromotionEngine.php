<?php

namespace App\Services;

use App\Models\Coupon;

class PromotionEngine
{
    /**
     * Calculate total discount for the cart.
     *
     * Rules applied in order:
     *  1. Combo discount — buy 2+ non-flash-sale items: 5% off eligible subtotal.
     *  2. Coupon discount — DB-backed coupon lookup (percentage or fixed).
     *
     * @param  float  $subtotal  Total cart value before discounts.
     * @param  array  $cartItems  Enriched items from CartService::getCartItemsDetails().
     * @param  string|null  $couponCode  Coupon code string from session (nullable).
     * @return float Total discount amount (capped at subtotal).
     */
    public function calculateDiscount(float $subtotal, array $cartItems, ?string $couponCode = null): float
    {
        $discount = 0.0;

        // Calculate eligible subtotal: exclude flash sale items from promotions.
        $eligibleSubtotal = 0.0;
        $eligibleItemsCount = 0;
        foreach ($cartItems as $item) {
            if (empty($item['is_flash_sale'])) {
                $eligibleSubtotal += (float) ($item['subtotal'] ?? ($item['price'] * $item['quantity']));
                $eligibleItemsCount += (int) $item['quantity'];
            }
        }

        // Combo rule: buy 2 or more eligible items → 5% off eligible subtotal.
        if ($eligibleItemsCount >= 2) {
            $discount += $eligibleSubtotal * 0.05;
        }

        // Coupon rule: DB-backed lookup with applicability checks.
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();

            if ($coupon && $coupon->isApplicable($eligibleSubtotal)) {
                $discount += $coupon->calculateDiscount($eligibleSubtotal);
            }
        }

        // Discount is capped at full subtotal to prevent negative totals.
        return min($discount, $subtotal);
    }

    /**
     * Resolve a valid Coupon model by code, or return null.
     * Used by ProcessCheckoutAction to increment usage count after order creation.
     */
    public function resolveCoupon(?string $couponCode, float $eligibleSubtotal): ?Coupon
    {
        if (! $couponCode) {
            return null;
        }

        $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();

        return ($coupon && $coupon->isApplicable($eligibleSubtotal)) ? $coupon : null;
    }
}
