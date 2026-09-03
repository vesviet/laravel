<?php

namespace App\Http\Requests;

use App\Rules\StockAvailable;
use App\Rules\ValidPhoneVN;
use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Whether the nested (Livewire-shaped) validation surface applies.
     */
    protected bool $applyNestedRules = false;

    /**
     * Flat customer fields consumed by ProcessCheckoutAction / OrderService.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function flatRules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', new ValidPhoneVN],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod,vnpay,momo,banking,vietqr'],
        ];
    }

    /**
     * Nested rules enforced by the Livewire checkout UI — city, district and
     * ward become required because the multi-step form collects them.
     *
     * @return array<string, array<int, mixed>>
     */
    protected function nestedRules(): array
    {
        return [
            'shippingData.customer_name' => ['required', 'string', 'max:255'],
            'shippingData.phone' => ['required', 'string', new ValidPhoneVN],
            'shippingData.email' => ['nullable', 'email', 'max:255'],
            'shippingData.address' => ['required', 'string', 'max:500'],
            'shippingData.city' => ['required', 'string', 'max:100'],
            'shippingData.district' => ['required', 'string', 'max:100'],
            'shippingData.ward' => ['required', 'string', 'max:100'],
            'shippingData.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // A completely empty submission still surfaces the Livewire-shaped
        // shippingData.* errors used by the checkout UI contract.
        $this->applyNestedRules = is_array($this->input('shippingData'))
            || $this->request->count() === 0;

        // Resolve CartService from the container — FormRequest constructor cannot be
        // easily overridden with custom DI without breaking the framework's request lifecycle.
        $cartService = app(CartService::class);

        // Inject cart items into request data so we can validate stock and structure.
        $this->merge([
            'items' => $cartService->getCartItemsDetails(),
        ]);

        // Accept the Livewire-shaped nested payload (shippingData.*) by hoisting
        // it to the top level so both payload shapes validate identically.
        $shippingData = $this->input('shippingData');

        if (is_array($shippingData)) {
            $this->merge($shippingData);
        }
    }

    public function rules(): array
    {
        $rules = $this->flatRules();

        if ($this->applyNestedRules) {
            $rules += $this->nestedRules();
        }

        // Validate cart items
        $rules['items.*.product_id'] = ['required', 'integer', 'exists:products,id'];
        $rules['items.*.product_variant_id'] = ['nullable', 'integer', 'exists:product_variants,id'];
        $rules['items.*.quantity'] = ['required', 'integer', 'min:1', new StockAvailable];

        return $rules;
    }

    /**
     * Flat customer payload for ProcessCheckoutAction / OrderService.
     */
    public function customerData(): array
    {
        return collect($this->flatRules())
            ->keys()
            ->mapWithKeys(fn (string $field) => [
                $field => $this->input($field),
            ])
            ->filter(fn ($value) => $value !== null)
            ->all();
    }

    public function messages(): array
    {
        return [];
    }
}
