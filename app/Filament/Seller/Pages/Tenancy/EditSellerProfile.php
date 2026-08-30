<?php

namespace App\Filament\Seller\Pages\Tenancy;

use App\Actions\UpdateSellerProfileAction;
use App\Exceptions\SellerActionException;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Database\Eloquent\Model;

/**
 * EditSellerProfile — Filament tenant profile page for the Seller Panel.
 *
 * SF-05 fix: Previously, this page saved SellerProfile directly via Eloquent,
 * bypassing transaction boundaries, cache invalidation, and field whitelist enforcement.
 * Now handleRecordUpdate() routes through UpdateSellerProfileAction which:
 *   - Wraps the save in a DB::transaction()
 *   - Invalidates the storefront page cache
 *   - Filters only allowed fields (subdomain cannot be changed)
 */
class EditSellerProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Thông tin cửa hàng';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Thông tin chung')
                    ->schema([
                        TextInput::make('shop_name')
                            ->label('Tên Shop')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subdomain')
                            ->label('Subdomain (Không thể đổi)')
                            ->disabled()
                            ->helperText('Subdomain được tạo tự động khi đăng ký và không thể thay đổi.')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Số điện thoại liên hệ')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email hỗ trợ')
                            ->email()
                            ->maxLength(255),
                        Textarea::make('bio')
                            ->label('Giới thiệu cửa hàng')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Thông tin thanh toán (VietQR)')
                    ->description('Cung cấp để khách hàng có thể thanh toán qua QR Code.')
                    ->schema([
                        TextInput::make('bank_code')
                            ->label('Mã Ngân Hàng (VD: ICB, VCB)')
                            ->maxLength(255)
                            ->placeholder('VCB'),
                        TextInput::make('bank_account_no')
                            ->label('Số Tài Khoản')
                            ->maxLength(255),
                        TextInput::make('bank_account_name')
                            ->label('Tên Chủ Tài Khoản')
                            ->maxLength(255)
                            ->placeholder('NGUYEN VAN A'),
                    ])->columns(3),
            ]);
    }

    /**
     * Route the profile update through UpdateSellerProfileAction.
     *
     * SF-05: Replaces the default Eloquent::save() path in EditTenantProfile
     * with an Action call that wraps the save in a transaction, enforces the
     * allowed-fields whitelist, and clears the storefront cache.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return app(UpdateSellerProfileAction::class)->execute($record, $data);
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
