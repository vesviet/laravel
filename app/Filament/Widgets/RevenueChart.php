<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue (Last 30 Days)';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $orders = Order::whereIn('status', ['completed', 'delivered'])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->format('Y-m-d');
            });

        $labels = [];
        $data = [];

        foreach ($orders as $date => $orderGroup) {
            $labels[] = $date;
            $data[] = $orderGroup->sum('total_amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
