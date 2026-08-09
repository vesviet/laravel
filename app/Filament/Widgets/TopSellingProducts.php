<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class TopSellingProducts extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OrderItem::query()
                    ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'))
                    ->whereHas('order', function ($query) {
                        $query->whereIn('status', ['completed', 'delivered'])
                            ->where('created_at', '>=', now()->subDays(30));
                    })
                    ->groupBy('product_id', 'product_name')
                    ->orderByDesc('total_sold')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Total Sold')
                    ->numeric(),
            ])
            ->heading('Top Selling Products (Last 30 Days)');
    }
}
