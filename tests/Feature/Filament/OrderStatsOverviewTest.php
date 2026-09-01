<?php

namespace Tests\Feature\Filament;

use App\Enums\OrderStatus;
use App\Filament\Widgets\OrderStatsOverview;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;

uses(RefreshDatabase::class);

/**
 * Helper: invoke protected getStats() via ReflectionMethod.
 * getStats() is protected in StatsOverviewWidget — Livewire __call()
 * intercepts public calls before PHP resolves the method visibility,
 * so direct $widget->getStats() throws BadMethodCallException.
 */
function callGetStats(OrderStatsOverview $widget): array
{
    $ref = new ReflectionMethod($widget, 'getStats');
    $ref->setAccessible(true);
    return $ref->invoke($widget);
}

function makeOrder2(string $orderNumber, OrderStatus $status, int $amount): Order
{
    return Order::create([
        'order_number'    => $orderNumber,
        'status'          => $status,
        'total_amount'    => $amount,
        'subtotal'        => $amount,
        'discount_amount' => 0,
        'shipping_fee'    => 0,
        'customer_name'   => 'Test',
        'phone'           => '0900000000',
        'address'         => 'Test Addr',
        'payment_method'  => 'cod',
        'created_at'      => now(),
    ]);
}

it('counts only delivered orders in today revenue', function () {
    makeOrder2('ORD-D-001', OrderStatus::Delivered, 1_000_000);
    makeOrder2('ORD-S-001', OrderStatus::Shipped,   500_000);   // should NOT count
    makeOrder2('ORD-P-001', OrderStatus::Pending,   200_000);   // should NOT count

    $widget = new OrderStatsOverview();
    $stats  = callGetStats($widget);

    expect($stats[0]->getValue())->toBe('1.000.000₫');
});

it('counts pending orders correctly', function () {
    makeOrder2('ORD-P-002', OrderStatus::Pending,   100_000);
    makeOrder2('ORD-P-003', OrderStatus::Pending,   100_000);
    makeOrder2('ORD-C-001', OrderStatus::Confirmed, 100_000);

    $widget = new OrderStatsOverview();
    $stats  = callGetStats($widget);

    expect((int) $stats[2]->getValue())->toBe(2);
});

it('counts shipped orders using correct enum value not stale shipping string', function () {
    makeOrder2('ORD-SH-001', OrderStatus::Shipped, 300_000);

    $widget = new OrderStatsOverview();
    $stats  = callGetStats($widget);

    // Regression guard: was always 0 due to querying status = 'shipping'
    expect((int) $stats[3]->getValue())->toBe(1);
});

it('does not count phantom completed status that does not exist in enum', function () {
    DB::table('orders')->insert([
        'status'          => 'completed',
        'total_amount'    => 9_999_999,
        'subtotal'        => 9_999_999,
        'discount_amount' => 0,
        'shipping_fee'    => 0,
        'customer_name'   => 'Ghost',
        'phone'           => '0900000099',
        'address'         => 'Ghost Addr',
        'payment_method'  => 'cod',
        'order_number'    => 'ORD-GHOST-001',
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);

    $widget = new OrderStatsOverview();
    $stats  = callGetStats($widget);

    expect($stats[0]->getValue())->toBe('0₫');
});