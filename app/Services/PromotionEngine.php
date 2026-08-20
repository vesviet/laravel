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
     *  2. Coupon discount — DB-backed coupon lookup:
     *     - Percentage / Fixed: ONLY applied on eligible subtotal (excluding flash sale items).
     *     - Free Shipping / Shipping Discount: Applied on shipping fee based on total cart subtotal.
     *
     * @param  float  $subtotal  Total cart value before discounts.
     * @param  array  $cartItems  Enriched items from CartService::getCartItemsDetails().
     * @param  string|null  $couponCode  Coupon code string from session (nullable).
     * @param  float  $shippingFee  Shipping fee amount (default 0.0).
     * @return float Total discount amount (capped at subtotal + shippingFee).
     */
    public function calculateDiscount(float $subtotal, array $cartItems, ?string $couponCode = null, float $shippingFee = 0.0): float
    {
        $itemDiscount = 0.0;
        $shippingDiscount = 0.0;

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
        if ($eligibleItemsCount >= 2 && $eligibleSubtotal > 0) {
            $itemDiscount += $eligibleSubtotal * 0.05;
        }

        // Coupon rule: DB-backed lookup with applicability checks.
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();

            if ($coupon) {
                if (in_array($coupon->type, ['free_shipping', 'shipping_discount'])) {
                    // Freeship coupon checks applicability against full subtotal
                    if ($coupon->isApplicable($subtotal)) {
                        $shippingDiscount += $coupon->calculateDiscount($eligibleSubtotal, $shippingFee);
                    }
                } else {
                    // Percentage / Fixed coupons ONLY apply on eligible subtotal
                    if ($eligibleSubtotal > 0 && $coupon->isApplicable($eligibleSubtotal)) {
                        $itemDiscount += $coupon->calculateDiscount($eligibleSubtotal, $shippingFee);
                    }
                }
            }
        }

        // Item discount cannot exceed eligible subtotal; shipping discount cannot exceed shipping fee
        $itemDiscount = min($itemDiscount, $eligibleSubtotal);
        $shippingDiscount = min($shippingDiscount, $shippingFee);

        return $itemDiscount + $shippingDiscount;
    }

    /**
     * Resolve a valid Coupon model by code, or return null.
     * Used by ProcessCheckoutAction to increment usage count after order creation.
     */
    public function resolveCoupon(?string $couponCode, float $subtotal, float $eligibleSubtotal = 0.0): ?Coupon
    {
        if (! $couponCode) {
            return null;
        }

        $coupon = Coupon::where('code', strtoupper(trim($couponCode)))->first();

        if (! $coupon) {
            return null;
        }

        if (in_array($coupon->type, ['free_shipping', 'shipping_discount'])) {
            return $coupon->isApplicable($subtotal) ? $coupon : null;
        }

        return ($eligibleSubtotal > 0 && $coupon->isApplicable($eligibleSubtotal)) ? $coupon : null;
    }
}
