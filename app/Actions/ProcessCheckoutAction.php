<?php

namespace App\Actions;

use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PromotionEngine;
use Exception;
use Illuminate\Support\Facades\DB;

class ProcessCheckoutAction
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
        protected PromotionEngine $promotionEngine
    ) {}

    /**
     * Execute the full checkout pipeline:
     * 1. Resolve cart items and pricing.
     * 2. Calculate promotions and resolve coupon.
     * 3. Create order + deduct stock + increment coupon usage within a single DB transaction.
     * 4. Clear session cart AFTER the transaction commits (session is not transactional).
     *
     * @param  array  $customerData  Validated customer/shipping fields.
     * @param  string|null  $couponCode  Optional coupon code from session.
     *
     * @throws Exception
     */
    public function execute(array $customerData, ?string $couponCode = null): Order
    {
        $cartItems = $this->cartService->getCartItemsDetails();

        if (empty($cartItems)) {
            throw new Exception('Cart is empty.');
        }

        $subtotal = $this->cartService->calculateTotal();

        // Calculate eligible subtotal for coupon applicability check.
        $eligibleSubtotal = array_sum(
            array_map(
                fn ($item) => empty($item['is_flash_sale']) ? (float) $item['subtotal'] : 0.0,
                $cartItems
            )
        );

        $discountAmount = $this->promotionEngine->calculateDiscount($subtotal, $cartItems, $couponCode);
        $coupon = $this->promotionEngine->resolveCoupon($couponCode, $eligibleSubtotal);
        $shippingFee = 0;
        try {
            $goshipService = app(\App\Services\GoshipService::class);
            $rates = $goshipService->getShippingRates([
                'city' => $customerData['city'] ?? '',
                'district' => $customerData['district'] ?? '',
                'ward' => $customerData['ward'] ?? '',
            ], ['weight' => 1000]);
            
            if (!empty($rates) && isset($rates[0]['total_amount'])) {
                $shippingFee = (float) $rates[0]['total_amount'];
            }
        } catch (Exception $e) {
            // fallback to 0
        }
        // DB transaction: order creation + stock deduction + coupon usage tracking.
        // Session cart clear happens AFTER commit — session is not part of the DB transaction.
        $order = DB::transaction(function () use ($customerData, $cartItems, $subtotal, $discountAmount, $shippingFee, $coupon) {
            $order = $this->orderService->createOrder(
                $customerData,
                $cartItems,
                $subtotal,
                $discountAmount,
                $shippingFee
            );

            // Increment coupon usage count within the same transaction.
            if ($coupon instanceof Coupon) {
                $coupon->incrementUsage();
            }

            return $order;
        });

        // Clear cart from session only after the DB transaction successfully commits.
        $this->cartService->clear();

        return $order;
    }
}
