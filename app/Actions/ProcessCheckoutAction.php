<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Exceptions\EmptyCartException;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\GoshipService;
use App\Services\OrderService;
use App\Services\PromotionEngine;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProcessCheckoutAction
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
        protected PromotionEngine $promotionEngine,
        protected GoshipService $goshipService
    ) {}

    /**
     * Execute the full checkout pipeline:
     *
     * 1. Resolve cart items and calculate subtotal.
     * 2. Fetch shipping fee from Goship (outside transaction — external HTTP call).
     * 3. Open DB transaction:
     *    a. Lock coupon row (lockForUpdate) and re-validate usage limit — race-safe.
     *    b. Create order + order items via OrderService.
     *    c. Deduct stock via InventoryService (inside same transaction, lockForUpdate per item).
     *    d. Increment coupon usage.
     * 4. Clear session cart AFTER transaction commits.
     *
     * @param  array  $customerData  Validated customer/shipping fields from CheckoutRequest.
     * @param  string|null  $couponCode  Optional coupon code from session.
     *
     * @throws RuntimeException on cart empty, stock shortfall, or coupon exhausted.
     */
    public function execute(array $customerData, ?string $couponCode = null): Order
    {
        $cartItems = $this->cartService->getCartItemsDetails();

        if (empty($cartItems)) {
            throw new EmptyCartException('Giỏ hàng trống.');
        }

        $subtotal = $this->cartService->calculateTotal();

        // Calculate eligible subtotal: flash-sale items are excluded from coupon.
        $eligibleSubtotal = array_sum(
            array_map(
                fn ($item) => empty($item['is_flash_sale']) ? (int) $item['subtotal'] : 0,
                $cartItems
            )
        );

        // Resolve shipping fee BEFORE the transaction — external HTTP call cannot be transactional.
        $shippingFee = $this->resolveShippingFee($customerData);

        // DB transaction: all writes (order, items, stock, coupon) are atomic.
        $order = DB::transaction(function () use (
            $customerData, $cartItems, $subtotal, $eligibleSubtotal, $shippingFee, $couponCode
        ) {
            $discountAmount = 0;
            $coupon         = null;

            if ($couponCode) {
                // Lock the coupon row inside the transaction — prevents concurrent over-redemption.
                $coupon = Coupon::where('code', $couponCode)->lockForUpdate()->first();

                if ($coupon && $coupon->isApplicable($eligibleSubtotal)) {
                    $discountAmount = $coupon->calculateDiscount($eligibleSubtotal);
                } else {
                    // Coupon no longer valid (exhausted by concurrent request, expired, etc.)
                    $coupon = null;
                }
            }

            $order = $this->orderService->createOrder(
                $customerData,
                $cartItems,
                (int) $subtotal,
                (int) $discountAmount,
                (int) $shippingFee
            );

            if ($coupon instanceof Coupon) {
                $coupon->incrementUsage();
            }

            return $order;
        });

        // Clear session cart only after the DB transaction successfully commits.
        // Session is not part of the DB transaction — clearing before commit would lose the cart on rollback.
        $this->cartService->clear();

        // A1: Fire domain event after transaction commits.
        // SendOrderConfirmationEmail listener is queued (ShouldQueue) via 'database' driver.
        // This replaces inline Mail::send() in CheckoutController — checkout response is never blocked by SMTP.
        OrderPlaced::dispatch($order);

        return $order;
    }

    /**
     * Attempt to get a shipping fee from Goship.
     * Falls back to 0 if API is unavailable — do not block checkout on external service failure.
     */
    protected function resolveShippingFee(array $customerData): int
    {
        try {
            $rates = $this->goshipService->getShippingRates(
                [
                    'city'     => $customerData['city'] ?? '',
                    'district' => $customerData['district'] ?? '',
                    'ward'     => $customerData['ward'] ?? '',
                ],
                ['weight' => 1000] // S8: will use actual product weight when added
            );

            return isset($rates[0]['total_amount']) ? (int) $rates[0]['total_amount'] : 0;
        } catch (\Throwable) {
            return 0;
        }
    }
}
