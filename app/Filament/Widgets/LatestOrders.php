<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Order::latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable(),
                Tables\Columns\TextColumn::make('customer.name'),
                Tables\Columns\TextColumn::make('total_amount')->money('VND'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'shipping' => 'info',
                        'delivered' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ]);
    }
}
