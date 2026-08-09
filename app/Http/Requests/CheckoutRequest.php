<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPhoneVN;
use App\Rules\StockAvailable;
use App\Services\CartService;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Inject cart items into request data so we can validate stock and structure
        $cartService = app(CartService::class);
        $cartItems = $cartService->getCartItemsDetails();
        
        $this->merge([
            'items' => $cartItems,
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', new StockAvailable],
        ];
    }
    
    public function messages()
    {
        return [
            'items.required' => 'Your cart is empty.',
            'items.min' => 'Your cart must have at least one item.',
        ];
    }
}
