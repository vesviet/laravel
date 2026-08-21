<?php

namespace App\Services;

use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Promotions\DTOs\PromotedPriceResult;
use App\Services\Promotions\PromotionEngine;
use Illuminate\Support\Facades\Session;


class CartService
{
    protected string $sessionKey = 'cart';

    /**
     * [I-02] Inject PromotionEngine via constructor — do not call app() per loop iteration.
     * The engine's getActiveCatalogRules() is cache-backed, but re-constructing via app()
     * on every product is wasteful and bypasses constructor-level DI contracts.
     */
    public function __construct(
        protected PromotionEngine $promotionEngine
    ) {}


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
            $categoryId = null;
            $originalPrice = 0.0;
            $promotedResult = null;

            if ($variantId && $variants->has($variantId)) {
                $variant = $variants->get($variantId);
                $price = (float) $variant->price;
                $originalPrice = $price;
                $name = $variant->product->name ?? '';
                $variantName = $variant->name;
                $sku = $variant->sku ?? '';
                $imagePath = $variant->product->primary_image_url ?? $variant->product->thumbnail ?? null;
                $slug = $variant->product->slug ?? null;
                $categoryId = $variant->product->category_id ?? null;
            } elseif ($products->has($productId)) {
                $product = $products->get($productId);
                $price = (float) $product->price;
                $originalPrice = $price;
                $name = $product->name;
                $sku = $product->sku ?? '';
                $imagePath = $product->primary_image_url ?? $product->thumbnail ?? null;
                $slug = $product->slug ?? null;
                $categoryId = $product->category_id ?? null;

                // [I-02] Use injected engine instance — catalog rules are already cache-backed
                // in getActiveCatalogRules(); no N+1 here since the cache is populated once.
                $promotedResult = $this->promotionEngine->resolveProductPromotedPrice($product);
                if ($promotedResult !== null) {
                    $price = (float) $promotedResult->promotedPrice;
                    $originalPrice = (float) $promotedResult->originalPrice;
                }
            }

            // Override price with flash sale price if applicable (Flash sale takes precedence)
            if (isset($flashSaleItems[$productId])) {
                $price = (float) $flashSaleItems[$productId]->price;
                $isFlashSale = true;
            }

            $items[] = [
                'product_id'          => $productId,
                'product_variant_id'  => $variantId,
                'category_id'         => $categoryId,
                'product_name'        => $name,
                'variant_name'        => $variantName,
                'sku'                 => $sku,
                'price'               => $price,
                'original_price'      => $originalPrice,
                'quantity'            => $quantity,
                'subtotal'            => $price * $quantity,
                'is_flash_sale'       => $isFlashSale,
                'is_catalog_promoted' => ($promotedResult !== null && ! $isFlashSale),
                'promoted_result'     => $promotedResult?->toArray(),
                'image_path'          => $imagePath ?? null,
                'slug'                => $slug,
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

    /**
     * Validate real-time stock availability for all items in the current cart.
     * Returns an array of issues if any, or empty array if all items are in stock.
     */
    public function validateStock(): array
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return [];
        }

        $productIds = array_column($cart, 'product_id');
        $variantIds = array_filter(array_column($cart, 'product_variant_id'));

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $variants = !empty($variantIds) ? ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id') : collect();

        $issues = [];

        foreach ($cart as $key => $item) {
            $productId = $item['product_id'];
            $variantId = $item['product_variant_id'] ?? null;
            $quantity = $item['quantity'];

            if ($variantId && $variants->has($variantId)) {
                $variant = $variants->get($variantId);
                if ($variant->stock < $quantity) {
                    $issues[] = [
                        'name' => ($variant->product->name ?? 'Sản phẩm') . ' (' . $variant->name . ')',
                        'requested' => $quantity,
                        'available' => $variant->stock,
                    ];
                }
            } elseif ($products->has($productId)) {
                $product = $products->get($productId);
                if ($product->stock < $quantity) {
                    $issues[] = [
                        'name' => $product->name,
                        'requested' => $quantity,
                        'available' => $product->stock,
                    ];
                }
            }
        }

        return $issues;
    }

    /**
     * Get a comprehensive structured summary of the cart.
     */
    public function getSummary(): array
    {
        $items = $this->getCartItemsDetails();
        $subtotal = $this->calculateTotal();
        $totalItems = array_sum(array_column($items, 'quantity'));

        return [
            'items' => $items,
            'item_count' => $totalItems,
            'subtotal' => $subtotal,
            'formatted_subtotal' => number_format($subtotal, 0, ',', '.') . '₫',
            'is_empty' => empty($items),
        ];
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
