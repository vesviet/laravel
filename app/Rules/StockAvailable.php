<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;

class StockAvailable implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Attribute will be something like "items.0.quantity"
        // We can extract the index.
        preg_match('/items\.(\d+)\.quantity/', $attribute, $matches);
        if (isset($matches[1])) {
            $index = $matches[1];

            $productId = Arr::get($this->data, "items.{$index}.product_id");
            $variantId = Arr::get($this->data, "items.{$index}.product_variant_id");
            $quantity = (int) $value;

            if ($variantId) {
                $variant = ProductVariant::find($variantId);
                if ($variant && $variant->stock < $quantity) {
                    $fail('The requested quantity for this variant is not available in stock.');
                }
            } elseif ($productId) {
                $product = Product::find($productId);
                if ($product && $product->stock < $quantity) {
                    $fail('The requested quantity for this product is not available in stock.');
                }
            }
        }
    }
}
