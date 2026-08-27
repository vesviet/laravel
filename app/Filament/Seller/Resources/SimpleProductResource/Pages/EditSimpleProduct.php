<?php

namespace App\Filament\Seller\Resources\SimpleProductResource\Pages;

use App\Filament\Seller\Resources\SimpleProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use RuntimeException;

class EditSimpleProduct extends EditRecord
{
    protected static string $resource = SimpleProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $record = $this->getRecord();
        $sellerProfileId = auth()->user()?->sellerProfile?->id;

        if (! $sellerProfileId || $record->seller_id !== $sellerProfileId) {
            throw new RuntimeException('Bạn không có quyền chỉnh sửa sản phẩm này.');
        }
    }
}
