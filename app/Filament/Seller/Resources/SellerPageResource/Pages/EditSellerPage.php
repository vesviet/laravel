<?php

namespace App\Filament\Seller\Resources\SellerPageResource\Pages;

use App\Actions\UpdateSellerPageAction;
use App\Exceptions\SellerActionException;
use App\Filament\Seller\Resources\SellerPageResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * EditSellerPage — routes all page saves through UpdateSellerPageAction.
 *
 * SF-04 fix: Previously, Filament saved SellerPage directly via Eloquent,
 * bypassing cache invalidation. Now handleRecordUpdate() calls
 * UpdateSellerPageAction which ensures the storefront cache is always cleared.
 */
class EditSellerPage extends EditRecord
{
    protected static string $resource = SellerPageResource::class;

    protected function getHeaderActions(): array
    {
        $seller = Filament::getTenant();
        $subdomain = $seller?->subdomain ?? '';
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';

        return [
            Actions\Action::make('view_live')
                ->label('Xem trang trực tiếp')
                ->url("https://{$subdomain}.{$appHost}")
                ->openUrlInNewTab()
                ->visible(fn () => $seller?->isActive()),
        ];
    }

    /**
     * Route the save through UpdateSellerPageAction to ensure cache invalidation.
     *
     * SF-04: This override replaces the default Eloquent::save() path in EditRecord
     * with an Action call that wraps the save in a transaction and clears the
     * storefront cache in its finally{} block.
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

        try {
            return app(UpdateSellerPageAction::class)->execute($seller, $data);
        } catch (SellerActionException $e) {
            Notification::make()
                ->title('Lưu trang thất bại')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return $record;
        }
    }
}
