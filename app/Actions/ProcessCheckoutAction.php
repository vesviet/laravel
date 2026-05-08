<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;

class ProcessCheckoutAction
{
    public function __construct(
        private CartService $cartService,
    ) {}

    /**
     * Process checkout: create order + items in a single transaction.
     * Per technical plan 8.1: All order creation MUST use DB::transaction().
     */
    public function execute(array $customerData): Order
    {
        $cartItems = $this->cartService->getItems();

        if (empty($cartItems)) {
            throw new \RuntimeException('Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi đặt hàng.');
        }

        return DB::transaction(function () use ($customerData, $cartItems) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $customerData['customer_name'],
                'phone' => $customerData['phone'],
                'email' => $customerData['email'] ?? null,
                'address' => $customerData['address'],
                'city' => $customerData['city'] ?? null,
                'district' => $customerData['district'] ?? null,
                'ward' => $customerData['ward'] ?? null,
                'payment_method' => $customerData['payment_method'] ?? 'cod',
                'notes' => $customerData['notes'] ?? null,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_UNPAID,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);

                $unitPrice = $product ? $product->price : $item['price'];
                $productName = $product ? $product->name : $item['name'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $productName,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                ]);

                $totalAmount += $unitPrice * $item['quantity'];

                // Decrease stock
                if ($product && $product->stock > 0) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $order->update(['total_amount' => $totalAmount]);

            $this->cartService->clear();

            return $order->fresh('items');
        });
    }
}
