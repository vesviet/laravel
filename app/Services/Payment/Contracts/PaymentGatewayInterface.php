<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Whether this payment method is processed synchronously / offline (e.g. COD, VietQR manual transfer).
     */
    public function isOffline(): bool;

    /**
     * Process or generate payment instructions / URLs for the created order.
     *
     * @return array{
     *     status: string,
     *     payment_method: string,
     *     qr_url?: string|null,
     *     redirect_url?: string|null,
     *     instructions?: string|null,
     *     metadata?: array
     * }
     */
    public function process(Order $order): array;

    /**
     * Verify payment webhook / IPN payload integrity.
     */
    public function verifyCallback(array $payload): bool;

    /**
     * Mark an order as paid with external transaction details.
     */
    public function markPaid(Order $order, string $transactionId, array $payload = []): void;
}
