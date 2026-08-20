<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Services\PromotionEngine;
use Livewire\Component;

class CouponInput extends Component
{
    public string $couponCode = '';

    public float $discount = 0.0;

    public ?string $couponApplied = null;

    public ?string $errorMessage = null;

    public float $subtotal = 0.0;

    public function mount(float $subtotal): void
    {
        $this->subtotal = $subtotal;

        // Restore any previously applied coupon from session
        $sessionCoupon = session()->get('coupon');
        if ($sessionCoupon) {
            $this->couponCode = $sessionCoupon;
            $this->recomputeDiscount();
        }
    }

    public function applyCoupon(CartService $cartService, PromotionEngine $promotionEngine): void
    {
        $this->errorMessage = null;

        $code = strtoupper(trim($this->couponCode));

        if (empty($code)) {
            $this->errorMessage = 'Vui lòng nhập mã giảm giá.';
            return;
        }

        // Resolve coupon — uses PromotionEngine which validates applicability
        $cartItems = $cartService->getCartItemsDetails();
        $eligibleSubtotal = collect($cartItems)
            ->filter(fn($item) => empty($item['is_flash_sale']))
            ->sum(fn($item) => $item['price'] * $item['quantity']);

        $coupon = $promotionEngine->resolveCoupon($code, $this->subtotal, $eligibleSubtotal);

        if (! $coupon) {
            $this->errorMessage = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.';
            $this->discount = 0.0;
            $this->couponApplied = null;
            session()->forget('coupon');
            return;
        }

        // Apply: cap discount so total never goes below 0
        $rawDiscount = $coupon->calculateDiscount($eligibleSubtotal);
        $this->discount = min($rawDiscount, $this->subtotal);
        $this->couponApplied = $code;

        session()->put('coupon', $code);

        $this->dispatch('coupon-applied', discount: $this->discount);
    }

    public function removeCoupon(): void
    {
        $this->couponCode = '';
        $this->discount = 0.0;
        $this->couponApplied = null;
        $this->errorMessage = null;
        session()->forget('coupon');

        $this->dispatch('coupon-removed');
    }

    private function recomputeDiscount(): void
    {
        // Re-resolve on mount to refresh discount value
        $promotionEngine = app(PromotionEngine::class);
        $cartService = app(CartService::class);

        $cartItems = $cartService->getCartItemsDetails();
        $eligibleSubtotal = collect($cartItems)
            ->filter(fn($item) => empty($item['is_flash_sale']))
            ->sum(fn($item) => $item['price'] * $item['quantity']);

        $coupon = $promotionEngine->resolveCoupon($this->couponCode, $this->subtotal, $eligibleSubtotal);

        if ($coupon) {
            $rawDiscount = $coupon->calculateDiscount($eligibleSubtotal);
            $this->discount = min($rawDiscount, $this->subtotal);
            $this->couponApplied = $this->couponCode;
        } else {
            // Coupon expired since last visit — silently clear
            session()->forget('coupon');
            $this->couponCode = '';
        }
    }

    public function render()
    {
        return view('livewire.coupon-input');
    }
}
