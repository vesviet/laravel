<?php

namespace App\Livewire;

use App\Actions\ProcessCheckoutAction;
use App\Exceptions\CommerceException;
use App\Exceptions\EmptyCartException;
use App\Models\Province;
use App\Services\CartService;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CheckoutFlow extends Component
{
    public string $currentStep = "shipping";
    public array $steps = ["shipping", "payment", "review"];

    public array $shippingData = [
        "customer_name" => "",
        "phone" => "",
        "email" => "",
        "address" => "",
        "city" => "",
        "district" => "",
        "ward" => "",
        "notes" => "",
    ];

    public string $selectedPaymentMethod = "cod";
    public array $selectedAddress = [];
    public string $couponCode = "";
    public float $couponDiscount = 0.0;
    public ?string $couponApplied = null;
    public ?string $couponError = null;
    public array $cart = [];
    public float $subtotal = 0;
    public ?float $shippingFee = null;
    public bool $isProcessing = false;
    public ?string $errorMessage = null;

    #[Computed]
    public function breakdown()
    {
        $engine = app(PromotionEngine::class);
        $customer = Auth::guard("customer")->user();
        $email = $customer?->email ?? "";

        return $engine->calculateCartDiscounts(
            subtotal: $this->subtotal,
            cartItems: $this->cart,
            couponCode: $this->couponApplied,
            shippingFee: $this->shippingFee ?? 0,
            customer: $customer,
            email: $email
        );
    }

    #[Computed]
    public function provinces()
    {
        return Province::orderBy("name")->get();
    }

    #[Computed]
    public function customer()
    {
        return Auth::guard("customer")->user();
    }

    public function mount(): void
    {
        $this->loadCart();

        if (empty($this->cart)) {
            $this->redirectRoute("products.index");
        }

        $customer = Auth::guard("customer")->user();

        if ($customer) {
            $this->shippingData["customer_name"] = $customer->name;
            $this->shippingData["phone"] = $customer->phone;
            $this->shippingData["email"] = $customer->email;

            $defaultAddress = $customer->addresses()
                ->where("type", "shipping")
                ->where("is_default", true)
                ->first();

            if ($defaultAddress) {
                $this->selectedAddress = [
                    "id" => $defaultAddress->id,
                    "recipient_name" => $defaultAddress->recipient_name,
                    "phone" => $defaultAddress->phone,
                    "formatted_address" => $defaultAddress->formatted_address,
                    "address_line_1" => $defaultAddress->address_line_1,
                    "city" => $defaultAddress->city,
                    "district" => $defaultAddress->district,
                    "ward" => $defaultAddress->ward,
                ];

                $this->shippingData["address"] = $defaultAddress->address_line_1;
                $this->shippingData["city"] = $defaultAddress->city;
                $this->shippingData["district"] = $defaultAddress->district;
                $this->shippingData["ward"] = $defaultAddress->ward;
            }

            $sessionCoupon = session()->get("coupon");
            if ($sessionCoupon) {
                $this->couponCode = strtoupper(trim($sessionCoupon));
                $this->applyCoupon();
            }
        }

        $this->calculateShippingFee();
    }

    public function loadCart(): void
    {
        $cartService = app(CartService::class);
        $this->cart = $cartService->getCartItemsDetails();
        $this->subtotal = (float) $cartService->calculateTotal();
    }

    public function calculateShippingFee(): void
    {
        try {
            $totalWeight = 0;
            foreach ($this->cart as $item) {
                $productWeight = (int) ($item["product"]->weight ?? 500);
                $qty = (int) ($item["quantity"] ?? 1);
                $totalWeight += max(100, $productWeight) * $qty;
            }
            $totalWeight = max(500, $totalWeight);

            $goshipService = app(\App\Services\GoshipService::class);
            $rates = $goshipService->getShippingRates(
                [
                    "city" => $this->shippingData["city"] ?? "",
                    "district" => $this->shippingData["district"] ?? "",
                    "ward" => $this->shippingData["ward"] ?? "",
                ],
                ["weight" => $totalWeight]
            );

            $this->shippingFee = isset($rates[0]["total_amount"]) ? (float) $rates[0]["total_amount"] : 0;
        } catch (\Throwable $e) {
            Log::warning("Shipping fee calculation failed", ["error" => $e->getMessage()]);
            $this->shippingFee = 0;
        }

        $this->dispatch("shipping-calculated", shippingFee: $this->shippingFee);
    }

    public function applyCoupon(): void
    {
        $this->couponError = null;
        $code = strtoupper(trim($this->couponCode));

        if (empty($code)) {
            $this->couponError = "Vui long nhap ma giam gia.";
            return;
        }

        $engine = app(PromotionEngine::class);
        $cartService = app(CartService::class);
        $customer = Auth::guard("customer")->user();
        $email = $customer?->email ?? "";

        $breakdown = $engine->calculateCartDiscounts(
            subtotal: $this->subtotal,
            cartItems: $this->cart,
            couponCode: $code,
            shippingFee: $this->shippingFee ?? 0,
            customer: $customer,
            email: $email
        );

        if (! $breakdown->hasCouponApplied() && ! $breakdown->hasDiscount()) {
            $couponRule = \App\Models\PromotionRule::query()
                ->active()
                ->cartRules()
                ->byCode($code)
                ->first();

            if (! $couponRule) {
                $this->couponError = "Ma giam gia [{$code}] khong ton tai hoac da het han.";
            } elseif ($couponRule->min_order_amount > 0 && $this->subtotal < (float) $couponRule->min_order_amount) {
                $gap = (float) $couponRule->min_order_amount - $this->subtotal;
                $this->couponError = "Ma [{$code}] yeu cau don toi thieu " . number_format($couponRule->min_order_amount, 0, ",", ".") . "VND (Can them " . number_format($gap, 0, ",", ".") . "VND).";
            } else {
                $this->couponError = "Ma giam gia [{$code}] khong du dieu kien ap dung.";
            }

            $this->couponDiscount = 0;
            $this->couponApplied = null;
            session()->forget("coupon");
            return;
        }

        $this->couponDiscount = $breakdown->couponDiscount;
        $this->couponApplied = $code;
        session()->put("coupon", $code);
        $this->couponError = null;
        $this->dispatch("coupon-applied", discount: $this->couponDiscount);
    }

    public function removeCoupon(): void
    {
        $this->couponCode = "";
        $this->couponDiscount = 0;
        $this->couponApplied = null;
        $this->couponError = null;
        session()->forget("coupon");
        $this->dispatch("coupon-removed");
    }

    public function nextStep(): void
    {
        $currentIndex = array_search($this->currentStep, $this->steps);
        if ($currentIndex !== false && $currentIndex < count($this->steps) - 1) {
            if ($this->validateCurrentStep()) {
                $this->currentStep = $this->steps[$currentIndex + 1];
            }
        }
    }

    public function previousStep(): void
    {
        $currentIndex = array_search($this->currentStep, $this->steps);
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->currentStep = $this->steps[$currentIndex - 1];
        }
    }

    public function validateCurrentStep(): bool
    {
        $this->resetValidation();

        $rules = match ($this->currentStep) {
            "shipping" => [
                "shippingData.customer_name" => ["required", "string", "max:255"],
                "shippingData.phone" => ["required", "string", "max:20", "regex:/^(\+84|0)[0-9]{9,10}$/"],
                "shippingData.email" => ["nullable", "email", "max:255"],
                "shippingData.address" => ["required", "string", "max:500"],
                "shippingData.city" => ["required", "string", "max:100"],
                "shippingData.district" => ["required", "string", "max:100"],
                "shippingData.ward" => ["required", "string", "max:100"],
            ],
            "payment" => [
                "selectedPaymentMethod" => ["required", "in:cod,vnpay,momo,banking"],
            ],
            "review" => [],
        };

        $this->validate($rules);
        return true;
    }

    #[On("address-changed")]
    public function onAddressChanged(array $address): void
    {
        $this->selectedAddress = $address;

        $this->shippingData["address"] = $address["address_line_1"] ?? "";
        $this->shippingData["city"] = $address["city"] ?? "";
        $this->shippingData["district"] = $address["district"] ?? "";
        $this->shippingData["ward"] = $address["ward"] ?? "";
        $this->shippingData["phone"] = $address["phone"] ?? "";
        $this->shippingData["customer_name"] = $address["recipient_name"] ?? "";

        $this->calculateShippingFee();
    }

    public function submitOrder(): void
    {
        $this->validateCurrentStep();

        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            $customerData = array_merge(
                $this->shippingData,
                [
                    "payment_method" => $this->selectedPaymentMethod,
                ]
            );

            if (Auth::guard("customer")->check()) {
                $customerData["customer_id"] = Auth::guard("customer")->id();
            }

            $processCheckout = app(ProcessCheckoutAction::class);
            $order = $processCheckout->execute($customerData, $this->couponApplied);

            session()->forget("coupon");

            session()->flash("checkout_completed", $order->order_number);

            $this->redirectRoute("checkout.success", ["order_number" => $order->order_number]);
        } catch (CommerceException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Throwable $e) {
            Log::error("Checkout failed", [
                "exception" => get_class($e),
                "message" => $e->getMessage(),
            ]);
            $this->errorMessage = "Loi xu ly don hang. Vui long thu lai hoac lien he ho tro.";
        } finally {
            $this->isProcessing = false;
        }
    }

    public function store(array $customerData): void
    {
        $this->isProcessing = true;
        $this->errorMessage = null;

        try {
            if (Auth::guard("customer")->check()) {
                $customerData["customer_id"] = Auth::guard("customer")->id();
            }

            $couponCode = session()->get("coupon");

            $processCheckout = app(ProcessCheckoutAction::class);
            $order = $processCheckout->execute($customerData, $couponCode);

            session()->forget("coupon");

            session()->flash("checkout_completed", $order->order_number);

            $this->redirectRoute("checkout.success", ["order_number" => $order->order_number]);
        } catch (CommerceException $e) {
            $this->errorMessage = $e->getMessage();
            $this->addError("payment_method", $e->getMessage());
        } catch (\Throwable $e) {
            Log::error("Checkout failed", [
                "exception" => get_class($e),
                "message" => $e->getMessage(),
            ]);
            $this->errorMessage = "Loi xu ly don hang. Vui long thu lai hoac lien he ho tro.";
            $this->addError("payment_method", "Loi xu ly don hang. Vui long thu lai hoac lien he ho tro.");
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view("livewire.checkout-flow", [
            "provinces" => $this->provinces,
        ]);
    }
}
