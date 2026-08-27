<?php

namespace App\Filament\Seller\Resources\SimpleProductResource\Pages;

use App\Filament\Seller\Resources\SimpleProductResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class CreateSimpleProduct extends CreateRecord
{
    protected static string $resource = SimpleProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $sellerProfile = auth()->user()?->sellerProfile;

        if (! $sellerProfile) {
            Notification::make()
                ->title('Bạn chưa có gian hàng')
                ->body('Vui lòng tạo Seller Profile trước khi thêm sản phẩm.')
                ->danger()
                ->send();
            throw new RuntimeException('User does not have an associated SellerProfile.');
        }

        $data['seller_id'] = $sellerProfile->id;
        $data['is_visible'] = $data['is_visible'] ?? true;
        $data['status'] = $data['status'] ?? 'published';
        $data['slug'] = $data['slug'] ?? \Illuminate\Support\Str::slug($data['name']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(\App\Actions\CreateSellerProductAction::class)->execute($data);
    }
}
