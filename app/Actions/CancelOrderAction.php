<?php

namespace App\Actions;

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
     * @param Order $order
     * @return bool
     * @throws Exception
     */
    public function execute(Order $order): bool
    {
        if ($order->status === 'cancelled') {
            throw new Exception("Order is already cancelled.");
        }

        if (in_array($order->status, ['shipping', 'delivered'])) {
            throw new Exception("Cannot cancel an order that is shipping or delivered.");
        }

        return DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);
            
            // Restore inventory
            $this->inventoryService->restoreStock($order);

            return true;
        });
    }
}
