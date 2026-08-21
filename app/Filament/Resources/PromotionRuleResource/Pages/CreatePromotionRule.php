<?php

namespace App\Filament\Resources\PromotionRuleResource\Pages;

use App\Filament\Resources\PromotionRuleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePromotionRule extends CreateRecord
{
    protected static string $resource = PromotionRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($this->data['conditions']) && is_array($this->data['conditions'])) {
            $data['conditions'] = array_merge($this->data['conditions'], $data['conditions'] ?? []);
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Đã tạo chương trình khuyến mãi')
            ->body('Quy tắc khuyến mãi mới đã được lưu và cập nhật trên hệ thống.');
    }
}
