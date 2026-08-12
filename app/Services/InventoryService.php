<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Deduct stock for a given order using lockForUpdate to prevent overselling.
     *
     * @throws Exception
     */
    public function deductStock(Order $order, array $cartItems = []): void
    {
        DB::transaction(function () use ($order) {
            // Sort items by product/variant ID to enforce consistent lock ordering and prevent deadlocks
            $items = $order->items->sortBy(function ($item) {
                return $item->product_id . '-' . ($item->product_variant_id ?? '0');
            });

            foreach ($items as $item) {
                // Deduct Flash Sale stock if applicable
                if ($item->is_flash_sale) {
                    $flashSaleItem = \App\Models\FlashSaleItem::where('product_id', $item->product_id)
                        ->whereHas('flashSale', function ($q) {
                            $q->where('status', 'active');
                        })->lockForUpdate()->first();

                    if ($flashSaleItem) {
                        if ($flashSaleItem->sold_quantity + $item->quantity > $flashSaleItem->quantity) {
                            throw new Exception("Flash sale stock exceeded for: {$item->product_name}");
                        }
                        $flashSaleItem->increment('sold_quantity', $item->quantity);
                    }
                }

                if ($item->product_variant_id) {
                    // Variant stock
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if (! $variant || $variant->stock < $item->quantity) {
                        throw new Exception("Insufficient stock for variant: {$item->product_name}");
                    }
                    $variant->decrement('stock', $item->quantity);
                } else {
                    // Simple product stock
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if (! $product || $product->stock < $item->quantity) {
                        throw new Exception("Insufficient stock for product: {$item->product_name}");
                    }
                    $product->decrement('stock', $item->quantity);
                }
            }
        });
    }

    /**
     * Restore stock for a given order (e.g. when cancelled)
     */
    public function restoreStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $items = $order->items->sortBy(function ($item) {
                return $item->product_id . '-' . ($item->product_variant_id ?? '0');
            });

            foreach ($items as $item) {
                if ($item->is_flash_sale) {
                    $flashSaleItem = \App\Models\FlashSaleItem::where('product_id', $item->product_id)
                        ->orderBy('id', 'desc')->lockForUpdate()->first();
                    if ($flashSaleItem) {
                        $flashSaleItem->decrement('sold_quantity', $item->quantity);
                    }
                }

                if ($item->product_variant_id) {
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }
        });
    }
}
