<?php

namespace App\Filament\Seller\Resources\SimpleProductResource\Pages;

use App\Filament\Seller\Resources\SimpleProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimpleProduct extends EditRecord
{
    protected static string $resource = SimpleProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
