<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Events\SellerOrderPlaced;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Exception;

class ProcessSellerQuickOrderAction
{
    /**
     * Process quick order from Seller's One-Page Carrd.
     * Enforces ADR-S2 transactional locking to prevent overselling.
     *
     * @param SellerProfile $seller
     * @param array $data
     * @return Order
     * @throws RuntimeException
     */
    public function execute(SellerProfile $seller, array $data): Order
    {
        try {
            return DB::transaction(function () use ($seller, $data) {
                $query = Product::where('id', $data['product_id'])
                    ->where('seller_id', $seller->id);

                if (DB::connection()->getDriverName() !== 'sqlite') {
                    $query->lockForUpdate();
                }

                $product = $query->firstOrFail();

                $quantity = (int) $data['quantity'];
                
                // Check stock unless it's unlimited (or we determine unlimited via a flag, assuming > 0 check for now)
                // For this implementation, if stock is managed, check it.
                if ($product->stock < $quantity) {
                    throw new RuntimeException('Sản phẩm đã hết hàng hoặc không đủ số lượng.');
                }

                // Deduct stock
                $product->stock -= $quantity;
                $product->save();

                $subtotal = $product->price * $quantity;
                $shippingFee = $seller->shipping_type === 'flat_rate' ? $seller->shipping_fee : 0;
                $totalAmount = $subtotal + $shippingFee;

                $orderNumber = 'ORD-' . strtoupper(Str::random(6));

                // Create Order
                $order = Order::create([
                    'seller_id' => $seller->id,
                    'order_number' => $orderNumber,
                    'status' => 'pending',
                    'payment_method' => $data['payment_method'] ?? 'cod', // cod or vietqr
                    'customer_name' => $data['customer_name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? 'N/A',
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $totalAmount,
                    'notes' => $data['notes'] ?? null,
                ]);

                // Create Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_name' => $data['variant_name'] ?? null,
                    'price_at_purchase' => $product->price,
                    'subtotal' => $subtotal,
                    'quantity' => $quantity,
                ]);

                // Dispatch event to trigger Telegram queued notification
                event(new SellerOrderPlaced($order, $seller));

                return $order;
            });
        } catch (Exception $e) {
            throw new RuntimeException('Không thể xử lý đơn hàng: ' . $e->getMessage());
        }
    }
}
