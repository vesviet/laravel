<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A1: OrderPlaced domain event.
 *
 * Fired by ProcessCheckoutAction and ProcessLandingOrderAction after the
 * DB::transaction() commits successfully. Listeners should be queued
 * (implements ShouldQueue) to avoid blocking the checkout response.
 *
 * Replaces: inline Mail::send() in CheckoutController.
 */
class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}
}
