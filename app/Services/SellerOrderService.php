<?php

namespace App\Services;

use App\Exceptions\SellerActionException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Owns Seller-side order creation logic. Has NO transaction of its own
 * (ADR-S2). The caller (Action) is responsible for opening the tx
 * boundary and dispatching SellerOrderPlaced AFTER the commit.
 */
class SellerOrderService
{
    /**
     * Create an Order + OrderItem for a quick checkout on a Seller's one-page store.
     * MUST be called within an active tx boundary.
     *
     * @param  array  $data  Validated payload from StoreSellerQuickOrderRequest
     *
     * @throws SellerActionException
     */
    public function createQuickOrder(SellerProfile $seller, array $data): Order
    {
        $product = $this->lockAndFetchProduct($seller, (int) $data['product_id']);
        $this->assertStockAvailable($product, (int) $data['quantity']);

        $quantity = (int) $data['quantity'];
        $product->stock -= $quantity;
        $product->save();

        $subtotal = $product->price * $quantity;
        $shippingFee = $seller->shipping_type === 'flat_rate' ? (int) $seller->shipping_fee : 0;
        $totalAmount = $subtotal + $shippingFee;

        $order = Order::create([
            'seller_id' => $seller->id,
            'order_number' => 'ORD-'.strtoupper(Str::random(6)),
            'status' => 'pending',
            'payment_method' => $data['payment_method'] ?? 'cod',
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'address' => $data['address'] ?? 'N/A',
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'notes' => $data['notes'] ?? null,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'variant_name' => $data['variant_name'] ?? null,
            'price_at_purchase' => $product->price,
            'subtotal' => $subtotal,
            'quantity' => $quantity,
        ]);

        return $order;
    }

    private function lockAndFetchProduct(SellerProfile $seller, int $productId): Product
    {
        $query = Product::query()
            ->where('id', $productId)
            ->where('seller_id', $seller->id);

        // SQLite (in-memory test DB) doesn't support FOR UPDATE; skip the lock.
        if (DB::connection()->getDriverName() !== 'sqlite') {
            $query->lockForUpdate();
        }

        return $query->firstOrFail();
    }

    private function assertStockAvailable(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw SellerActionException::outOfStock();
        }
    }
}
