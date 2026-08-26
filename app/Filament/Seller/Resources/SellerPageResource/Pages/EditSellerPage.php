<?php

namespace App\Filament\Seller\Resources\SellerPageResource\Pages;

use App\Filament\Seller\Resources\SellerPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellerPage extends EditRecord
{
    protected static string $resource = SellerPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_live')
                ->label('Xem trang trực tiếp')
                ->url(fn (): string => 'https://' . auth()->user()->sellerProfile->subdomain . '.' . config('app.url'))
                ->openUrlInNewTab(),
        ];
    }
}
