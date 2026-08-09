<?php

namespace App\Actions;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PromotionEngine;
use App\Models\Order;
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
     * Process checkout flow.
     *
     * @param array $customerData
     * @param string|null $couponCode
     * @return Order
     * @throws Exception
     */
    public function execute(array $customerData, ?string $couponCode = null): Order
    {
        $cartItems = $this->cartService->getCartItemsDetails();
        
        if (empty($cartItems)) {
            throw new Exception("Cart is empty.");
        }

        $subtotal = $this->cartService->calculateTotal();
        $discountAmount = $this->promotionEngine->calculateDiscount($subtotal, $cartItems, $couponCode);
        
        // Define shipping fee logic if any, defaulting to 0 for now
        $shippingFee = 0;

        return DB::transaction(function () use ($customerData, $cartItems, $subtotal, $discountAmount, $shippingFee) {
            // Create order and deduct stock inside the same transaction
            $order = $this->orderService->createOrder($customerData, $cartItems, $subtotal, $discountAmount, $shippingFee);
            
            // Clear cart upon successful order
            $this->cartService->clear();

            return $order;
        });
    }
}
