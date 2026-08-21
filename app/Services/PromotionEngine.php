<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PromotionRule;
use App\Services\Promotions\DTOs\PromotedPriceResult;
use App\Services\Promotions\DTOs\PromotionDiscountBreakdown;
use App\Services\Promotions\PromotionEngine as CorePromotionEngine;

class PromotionEngine
{
    protected CorePromotionEngine $coreEngine;

    /**
     * [I-01] Inject CorePromotionEngine as mandatory dependency.
     * Do NOT use nullable + app() fallback — that is a hidden service locator anti-pattern
     * that breaks constructor contract and test isolation.
     * Laravel's DI container resolves this automatically.
     */
    public function __construct(CorePromotionEngine $coreEngine)
    {
        $this->coreEngine = $coreEngine;
    }

    /**
     * Calculate total discount for the cart (Legacy contract).
     *
     * @param  float  $subtotal  Total cart value before discounts.
     * @param  array  $cartItems  Enriched items from CartService::getCartItemsDetails().
     * @param  string|null  $couponCode  Coupon code string (nullable).
     * @param  float  $shippingFee  Shipping fee amount (default 0.0).
     * @param  mixed  $user  Optional Customer or User model or array.
     * @return float Total discount amount (capped at subtotal + shippingFee).
     */
    public function calculateDiscount(
        float $subtotal,
        array $cartItems,
        ?string $couponCode = null,
        float $shippingFee = 0.0,
        mixed $user = null
    ): float {
        $customer = $user instanceof Customer ? $user : null;
        $email = $customer?->email ?? (is_array($user) ? ($user['email'] ?? '') : '');

        $breakdown = $this->coreEngine->calculateCartDiscounts(
            subtotal: $subtotal,
            cartItems: $cartItems,
            couponCode: $couponCode,
            shippingFee: $shippingFee,
            customer: $customer,
            email: $email
        );

        $totalDiscount = $breakdown->totalDiscount;

        // Backward compatibility fallback for legacy tests/coupons when no PromotionRule was applied
        if ($couponCode && empty($breakdown->appliedRules) && class_exists(Coupon::class)) {
            $cleanCode = strtoupper(trim($couponCode));
            $legacyCoupon = Coupon::where('code', $cleanCode)->first();
            if ($legacyCoupon) {
                $eligibleSubtotal = 0.0;
                $eligibleItemsCount = 0;
                foreach ($cartItems as $item) {
                    if (empty($item['is_flash_sale'])) {
                        $eligibleSubtotal += (float) ($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1)));
                        $eligibleItemsCount += (int) ($item['quantity'] ?? 1);
                    }
                }

                $itemDiscount = 0.0;
                $shippingDiscount = 0.0;

                // Legacy combo fallback if no automatic PromotionRules are active
                $hasAutoRules = PromotionRule::query()->cartRules()->automatic()->active()->exists();
                if (! $hasAutoRules && $eligibleItemsCount >= 2 && $eligibleSubtotal > 0) {
                    $itemDiscount += $eligibleSubtotal * 0.05;
                }

                if (in_array($legacyCoupon->type, ['free_shipping', 'shipping_discount'])) {
                    if ($legacyCoupon->isApplicable($subtotal)) {
                        $shippingDiscount += $legacyCoupon->calculateDiscount($eligibleSubtotal, $shippingFee);
                    }
                } else {
                    if ($eligibleSubtotal > 0 && $legacyCoupon->isApplicable($eligibleSubtotal)) {
                        $itemDiscount += $legacyCoupon->calculateDiscount($eligibleSubtotal, $shippingFee);
                    }
                }

                $itemDiscount = min($itemDiscount, $eligibleSubtotal);
                $shippingDiscount = min($shippingDiscount, $shippingFee);

                return min($itemDiscount + $shippingDiscount, $subtotal + $shippingFee);
            }
        }

        // Legacy combo fallback when no coupon provided and no PromotionRules active
        if (empty($breakdown->appliedRules) && empty($couponCode)) {
            $hasAutoRules = PromotionRule::query()->cartRules()->automatic()->active()->exists();
            if (! $hasAutoRules) {
                $eligibleSubtotal = 0.0;
                $eligibleItemsCount = 0;
                foreach ($cartItems as $item) {
                    if (empty($item['is_flash_sale'])) {
                        $eligibleSubtotal += (float) ($item['subtotal'] ?? (($item['price'] ?? 0) * ($item['quantity'] ?? 1)));
                        $eligibleItemsCount += (int) ($item['quantity'] ?? 1);
                    }
                }
                if ($eligibleItemsCount >= 2 && $eligibleSubtotal > 0) {
                    return min($eligibleSubtotal * 0.05, $subtotal);
                }
            }
        }

        return $totalDiscount;
    }

    /**
     * Return comprehensive itemized discount breakdown.
     */
    public function calculateCartDiscounts(
        float $subtotal,
        array $cartItems,
        ?string $couponCode = null,
        float $shippingFee = 0.0,
        ?Customer $customer = null,
        string $email = ''
    ): PromotionDiscountBreakdown {
        return $this->coreEngine->calculateCartDiscounts(
            subtotal: $subtotal,
            cartItems: $cartItems,
            couponCode: $couponCode,
            shippingFee: $shippingFee,
            customer: $customer,
            email: $email
        );
    }

    /**
     * Resolve Catalog Promoted Price & Badge for Product Card & Details.
     */
    public function resolveProductPromotedPrice(Product $product): ?PromotedPriceResult
    {
        return $this->coreEngine->resolveProductPromotedPrice($product);
    }

    /**
     * Legacy helper: Resolve Coupon model for backward compatibility.
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
