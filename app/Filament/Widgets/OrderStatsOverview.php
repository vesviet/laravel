<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $todayRevenue = Order::whereIn('status', ['completed', 'delivered'])
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $monthRevenue = Order::whereIn('status', ['completed', 'delivered'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $pendingCount = Order::where('status', 'pending')->count();

        $shippingCount = Order::where('status', 'shipping')->count();

        return [
            Stat::make('Doanh thu hôm nay', number_format($todayRevenue, 0, ',', '.') . '₫')
                ->description('Đơn completed/delivered')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Doanh thu tháng ' . now()->month, number_format($monthRevenue, 0, ',', '.') . '₫')
                ->description('Tháng ' . now()->format('m/Y'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Đơn chờ xử lý', $pendingCount)
                ->description('Trạng thái: pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Đang vận chuyển', $shippingCount)
                ->description('Trạng thái: shipping')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
        ];
    }
}
