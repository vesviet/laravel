<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A1: OrderCancelled domain event.
 *
 * Fired by CancelOrderAction after stock is restored and order status is updated.
 * Listeners can handle: customer notification email, analytics, etc.
 */
class OrderCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}
}
