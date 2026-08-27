<?php

namespace App\Filament\Seller\Resources\SellerOrderResource\Pages;

use App\Filament\Seller\Resources\SellerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellerOrders extends ListRecords
{
    protected static string $resource = SellerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Xuất Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => null)
                ->visible(false),
        ];
    }
}
