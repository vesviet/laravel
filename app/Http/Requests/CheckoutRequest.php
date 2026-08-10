<?php

namespace App\Http\Requests;

use App\Rules\StockAvailable;
use App\Rules\ValidPhoneVN;
use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Resolve CartService from the container — FormRequest constructor cannot be
        // easily overridden with custom DI without breaking the framework's request lifecycle.
        $cartService = app(CartService::class);

        // Inject cart items into request data so we can validate stock and structure.
        $this->merge([
            'items' => $cartService->getCartItemsDetails(),
        ]);
    }

    public function rules(): array
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
            'payment_method' => ['required', 'in:cod'],

            // Validate cart items
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', new StockAvailable],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
