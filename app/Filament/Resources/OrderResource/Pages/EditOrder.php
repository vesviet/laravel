<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Actions\CancelOrderAction;
use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel_order')
                ->label('Huỷ đơn & hoàn kho')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Xác nhận huỷ đơn hàng')
                ->modalDescription('Bạn có chắc chắn muốn huỷ đơn hàng này? Hệ thống sẽ tự động hoàn trả số lượng tồn kho sản phẩm vào kho hàng.')
                ->modalSubmitActionLabel('Xác nhận huỷ')
                ->visible(fn (): bool => ! ($this->record->status instanceof OrderStatus ? $this->record->status : OrderStatus::tryFrom($this->record->status))?->isTerminal())
                ->action(function () {
                    try {
                        app(CancelOrderAction::class)->execute($this->record);
                        $this->refreshFormData(['status']);
                        Notification::make()
                            ->title('Huỷ đơn hàng thành công')
                            ->body('Đơn hàng đã được huỷ và tồn kho đã được hoàn trả.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Không thể huỷ đơn hàng')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $newStatusValue = $this->data['status'] ?? null;
        if (! $newStatusValue) {
            return;
        }

        $newStatus = $newStatusValue instanceof OrderStatus ? $newStatusValue : OrderStatus::tryFrom($newStatusValue);
        $currentStatus = $this->record->status instanceof OrderStatus ? $this->record->status : OrderStatus::tryFrom($this->record->status);

        if ($currentStatus === $newStatus) {
            return;
        }

        // Check if transition is valid
        if ($currentStatus && ! $currentStatus->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Không thể chuyển trạng thái từ '{$currentStatus->label()}' sang '{$newStatus->label()}'.",
            ]);
        }

        // If transitioning to cancelled via form save, run CancelOrderAction
        if ($newStatus === OrderStatus::Cancelled) {
            app(CancelOrderAction::class)->execute($this->record);
            // CancelOrderAction already updated status and restored stock
        }
    }
}
