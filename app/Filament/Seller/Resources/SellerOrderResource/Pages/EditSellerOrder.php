<?php

namespace App\Filament\Seller\Resources\SellerOrderResource\Pages;

use App\Filament\Seller\Resources\SellerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellerOrder extends EditRecord
{
    protected static string $resource = SellerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}
