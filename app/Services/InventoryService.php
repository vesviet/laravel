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
    public function deductStock(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
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
            foreach ($order->items as $item) {
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
