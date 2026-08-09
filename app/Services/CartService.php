<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function add(int $productId, ?int $variantId, int $quantity = 1): void
    {
        $cart = $this->getCart();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ];
        }

        $this->saveCart($cart);
    }

    public function update(int $productId, ?int $variantId, int $quantity): void
    {
        $cart = $this->getCart();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
            $this->saveCart($cart);
        }
    }

    public function remove(int $productId, ?int $variantId): void
    {
        $cart = $this->getCart();
        $key = $this->generateKey($productId, $variantId);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->saveCart($cart);
        }
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    public function calculateTotal(): float
    {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $isFlashSale = false;
            
            // Check Flash Sale
            $flashSaleItem = \App\Models\FlashSaleItem::where('product_id', $item['product_id'])
                ->whereHas('flashSale', function ($q) {
                    $q->where('status', 'active')
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now());
                })
                ->whereColumn('sold_quantity', '<', 'quantity')
                ->first();

            if ($flashSaleItem) {
                $price = $flashSaleItem->price;
            } elseif ($item['product_variant_id']) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant) {
                    $price = $variant->price;
                }
            } else {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price = $product->price;
                }
            }
            $total += $price * $item['quantity'];
        }

        return $total;
    }

    public function getCartItemsDetails(): array
    {
        $cart = $this->getCart();
        $items = [];

        foreach ($cart as $key => $item) {
            $price = 0;
            $name = '';
            $sku = '';
            $variantName = null;

            $isFlashSale = false;
            $flashSaleItem = \App\Models\FlashSaleItem::where('product_id', $item['product_id'])
                ->whereHas('flashSale', function ($q) {
                    $q->where('status', 'active')
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now());
                })
                ->whereColumn('sold_quantity', '<', 'quantity')
                ->first();

            if ($item['product_variant_id']) {
                $variant = ProductVariant::with('product')->find($item['product_variant_id']);
                if ($variant) {
                    $price = $variant->price;
                    $name = $variant->product->name ?? '';
                    $variantName = $variant->name;
                    $sku = $variant->sku;
                }
            } else {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price = $product->price;
                    $name = $product->name;
                    $sku = $product->sku;
                }
            }

            if ($flashSaleItem) {
                $price = $flashSaleItem->price;
                $isFlashSale = true;
            }

            $items[] = [
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'],
                'product_name' => $name,
                'variant_name' => $variantName,
                'sku' => $sku,
                'price' => $price,
                'quantity' => $item['quantity'],
                'subtotal' => $price * $item['quantity'],
                'is_flash_sale' => $isFlashSale,
            ];
        }

        return $items;
    }

    protected function generateKey(int $productId, ?int $variantId): string
    {
        return $productId . '_' . ($variantId ?? '0');
    }

    protected function saveCart(array $cart): void
    {
        Session::put($this->sessionKey, $cart);
    }
}
