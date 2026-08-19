<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderCancelled;
use App\Models\Order;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Cancel an order and restore stock.
     *
     * @throws Exception
     */
    public function execute(Order $order): bool
    {
        $currentStatus = $order->status instanceof OrderStatus ? $order->status : OrderStatus::tryFrom($order->status);

        if ($currentStatus === OrderStatus::Cancelled) {
            throw new Exception('Đơn hàng đã ở trạng thái đã huỷ.');
        }

        if (in_array($currentStatus, [OrderStatus::Shipped, OrderStatus::Delivered])) {
            throw new Exception('Không thể huỷ đơn hàng đang giao hoặc đã giao.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::Cancelled]);

            $order->loadMissing('items');

            // Restore inventory
            $this->inventoryService->restoreStock($order);
        });

        OrderCancelled::dispatch($order);

        return true;
    }
}
