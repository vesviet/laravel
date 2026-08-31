<?php

namespace App\Filament\Seller\Resources\SellerOrderResource\Pages;

use App\Actions\UpdateSellerOrderStatusAction;
use App\Enums\OrderStatus;
use App\Exceptions\SellerActionException;
use App\Filament\Seller\Resources\SellerOrderResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * EditSellerOrder — routes all status saves through UpdateSellerOrderStatusAction.
 *
 * P1-01 fix: Previously, Filament's default EditRecord saved the Order model directly
 * via Eloquent. This means:
 *   - State machine was enforced only in the UI dropdown, not server-side
 *   - No event was dispatched after status change (Telegram/email gap)
 *
 * Now handleRecordUpdate() calls UpdateSellerOrderStatusAction which:
 *   - Validates the transition via OrderStatus::canTransitionTo() (server-side)
 *   - Wraps the save in a transaction (ADR-S2)
 *   - Dispatches SellerOrderStatusUpdated after commit
 *
 * ADR-S3 Trust Zone: Standard — requires PR review.
 */
class EditSellerOrder extends EditRecord
{
    protected static string $resource = SellerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    /**
     * Route the order save through UpdateSellerOrderStatusAction.
     *
     * P1-01: Enforces state machine server-side and fires event after commit.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $seller = Filament::getTenant();

        if (! $seller) {
            Notification::make()
                ->title('Lỗi tenant')
                ->body('Không xác định được gian hàng. Vui lòng tải lại trang.')
                ->danger()
                ->send();

            return $record;
        }

        // Resolve the requested new status from the form data.
        $newStatus = OrderStatus::tryFrom($data['status'] ?? '');

        if ($newStatus === null) {
            Notification::make()
                ->title('Trạng thái không hợp lệ')
                ->body('Trạng thái đơn hàng được yêu cầu không tồn tại.')
                ->danger()
                ->send();

            return $record;
        }

        try {
            return app(UpdateSellerOrderStatusAction::class)->execute(
                $seller,
                $record,
                $newStatus,
                $data['notes'] ?? null,
            );
        } catch (SellerActionException $e) {
            Notification::make()
                ->title('Cập nhật thất bại')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $record;
        }
    }
}
