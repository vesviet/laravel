<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create an order from validated data and cart items.
     * Note: Stock deduction is expected to be handled within a transaction here or orchestrated outside.
     * As per spec: Order creation pipeline (validate -> deduct stock -> create order record).
     */
    public function createOrder(array $customerData, array $cartItems, float $subtotal, float $discountAmount = 0, float $shippingFee = 0): Order
    {
        return DB::transaction(function () use ($customerData, $cartItems, $subtotal, $discountAmount, $shippingFee) {

            $totalAmount = $subtotal - $discountAmount + $shippingFee;

            $order = Order::create([
                'customer_id' => $customerData['customer_id'] ?? null,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_method' => $customerData['payment_method'] ?? 'cod',
                'customer_name' => $customerData['customer_name'],
                'phone' => $customerData['phone'],
                'email' => $customerData['email'] ?? null,
                'address' => $customerData['address'],
                'city' => $customerData['city'] ?? null,
                'district' => $customerData['district'] ?? null,
                'ward' => $customerData['ward'] ?? null,
                'notes' => $customerData['notes'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'price_at_purchase' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);
            }

            // Deduct stock after order and items are created
            // We pass the fresh order to inventory service
            $order->load('items');
            $this->inventoryService->deductStock($order);

            return $order;
        });
    }

    protected function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(5));

        return "ORD-{$date}-{$random}";
    }
}
