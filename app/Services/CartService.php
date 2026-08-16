<?php

namespace App\Services;

use App\Models\FlashSaleItem;
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

    /**
     * Calculate cart total applying flash sale prices where applicable.
     * Batch-loads products, variants, and flash sale items to avoid N+1 queries.
     */
    public function calculateTotal(): float
    {
        $items = $this->getCartItemsDetails();

        return (float) array_sum(array_column($items, 'subtotal'));
    }

    /**
     * Return enriched cart items with price, name, flash sale state, and subtotal.
     * Uses batch queries to avoid N+1 — one query each for products, variants, and flash sale items.
     */
    public function getCartItemsDetails(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return [];
        }

        // Collect IDs for batch loading
        $productIds = array_unique(array_filter(array_column($cart, 'product_id')));
        $variantIds = array_unique(array_filter(array_column($cart, 'product_variant_id')));

        // Batch load products and variants
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = $variantIds
            ? ProductVariant::with('product')->whereIn('id', $variantIds)->get()->keyBy('id')
            : collect();

        // Batch load active flash sale items for all products in cart
        $flashSaleItems = $this->getActiveFlashSaleItems($productIds);

        $items = [];
        foreach ($cart as $key => $item) {
            $productId = $item['product_id'];
            $variantId = $item['product_variant_id'] ?? null;
            $quantity = $item['quantity'];

            $price = 0.0;
            $name = '';
            $sku = '';
            $variantName = null;
            $isFlashSale = false;
            $imagePath   = null;

            $slug = null;
            if ($variantId && $variants->has($variantId)) {
                $variant = $variants->get($variantId);
                $price = (float) $variant->price;
                $name = $variant->product->name ?? '';
                $variantName = $variant->name;
                $sku = $variant->sku ?? '';
                $imagePath = $variant->product->thumbnail ?? null;
                $slug = $variant->product->slug ?? null;
            } elseif ($products->has($productId)) {
                $product = $products->get($productId);
                $price = (float) $product->price;
                $name = $product->name;
                $sku = $product->sku ?? '';
                $imagePath = $product->thumbnail ?? null;
                $slug = $product->slug ?? null;
            }

            // Override price with flash sale price if applicable
            if (isset($flashSaleItems[$productId])) {
                $price = (float) $flashSaleItems[$productId]->price;
                $isFlashSale = true;
            }

            $items[] = [
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'product_name'       => $name,
                'variant_name'       => $variantName,
                'sku'                => $sku,
                'price'              => $price,
                'quantity'           => $quantity,
                'subtotal'           => $price * $quantity,
                'is_flash_sale'      => $isFlashSale,
                'image_path'         => $imagePath ?? null,
                'slug'               => $slug,
            ];
        }

        return $items;
    }

    /**
     * Batch-load active flash sale items for the given product IDs.
     * Returns a keyed collection by product_id.
     */
    private function getActiveFlashSaleItems(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $items = FlashSaleItem::whereIn('product_id', $productIds)
            ->whereHas('flashSale', function ($q) {
                $q->where('status', 'active')
                    ->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
            })
            ->whereColumn('sold_quantity', '<', 'quantity')
            ->get()
            ->keyBy('product_id');

        return $items->all();
    }

    protected function generateKey(int $productId, ?int $variantId): string
    {
        return $productId.'_'.($variantId ?? '0');
    }

    protected function saveCart(array $cart): void
    {
        Session::put($this->sessionKey, $cart);
    }
}
