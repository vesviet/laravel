<?php

namespace App\Livewire;

use App\Models\LandingPage;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LandingOrderForm extends Component
{
    public LandingPage $landingPage;

    // Form state
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|regex:/^(0[3-9][0-9]{8}|84[3-9][0-9]{8})$/')]
    public string $phone = '';

    #[Validate('required|string|max:500')]
    public string $address = '';

    #[Validate('nullable|string|max:500')]
    public string $note = '';

    #[Validate('nullable|string|max:100')]
    public string $selectedComboId = '';

    #[Validate('nullable|string|max:100')]
    public string $selectedVariantId = '';

    // Honeypot
    public string $website = ''; // must stay empty

    // Result state
    public bool $isSubmitting = false;
    public ?array $successData = null;
    public string $errorMsg = '';

    public function mount(LandingPage $landingPage): void
    {
        $this->landingPage = $landingPage;

        // Pre-select first combo if available
        $combos = $landingPage->comboRules();
        if (!empty($combos)) {
            $this->selectedComboId = $combos[0]['id'] ?? '';
        }
    }

    public function submitOrder(): void
    {
        // Honeypot check — bots fill this
        if (!empty($this->website)) {
            $this->errorMsg = 'Có lỗi xảy ra, vui lòng thử lại.';
            return;
        }

        $this->validate();

        if (!$this->landingPage->isInStock()) {
            $this->errorMsg = 'Sản phẩm hiện tạm hết hàng.';
            return;
        }

        $this->isSubmitting = true;
        $this->errorMsg = '';

        try {
            // Find selected combo
            $combo = collect($this->landingPage->comboRules())
                ->firstWhere('id', $this->selectedComboId);

            // Determine total amount
            $totalAmount = $combo
                ? (float) $combo['price']
                : (float) ($this->landingPage->product?->price ?? 0);

            DB::transaction(function () use ($combo, $totalAmount) {
                $order = Order::create([
                    'landing_page_id' => $this->landingPage->id,
                    'order_number'    => 'LP-' . strtoupper(substr(uniqid(), -8)),
                    'status'          => 'pending',
                    'payment_method'  => 'cod',
                    'customer_name'   => $this->name,
                    'phone'           => $this->phone,
                    'address'         => $this->address,
                    'notes'           => $this->note ?: null,
                    'subtotal'        => $totalAmount,
                    'total'           => $totalAmount,
                    'discount_amount' => 0,
                ]);

                // Create order item if product is linked
                if ($this->landingPage->product_id) {
                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $this->landingPage->product_id,
                        'product_variant_id' => null,
                        'product_name'       => $combo['name'] ?? $this->landingPage->product->name,
                        'variant_name'       => null,
                        'sku'                => $this->landingPage->product->sku ?? null,
                        'quantity'           => 1,
                        'price_at_purchase'  => $totalAmount,
                        'subtotal'           => $totalAmount,
                    ]);
                }

                $this->successData = [
                    'order_reference'   => $order->order_number,
                    'payment_method'    => 'cod',
                    'estimated_delivery' => '2-3 ngày làm việc',
                    'total_amount'      => $totalAmount,
                    'combo_name'        => $combo['name'] ?? null,
                ];
            });

            // Fire tracking events to the view via browser event
            $this->dispatch('order-placed', [
                'value'             => $this->successData['total_amount'],
                'currency'          => 'VND',
                'facebook_pixel_id' => $this->landingPage->facebook_pixel_id,
                'tiktok_pixel_id'   => $this->landingPage->tiktok_pixel_id,
            ]);

            // Reset form fields
            $this->name    = '';
            $this->phone   = '';
            $this->address = '';
            $this->note    = '';

        } catch (\Throwable $e) {
            $this->errorMsg = 'Lỗi hệ thống, vui lòng thử lại hoặc liên hệ hỗ trợ.';
            report($e);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.landing-order-form');
    }
}
