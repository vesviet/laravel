<?php

namespace App\Filament\Resources\PromotionRuleResource\Pages;

use App\Filament\Resources\PromotionRuleResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPromotionRule extends EditRecord
{
    protected static string $resource = PromotionRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa quy tắc'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($this->data['conditions']) && is_array($this->data['conditions'])) {
            $data['conditions'] = array_merge($this->data['conditions'], $data['conditions'] ?? []);
        }

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Đã cập nhật khuyến mãi')
            ->body('Thông tin quy tắc khuyến mãi đã được lưu thành công.');
    }
}
