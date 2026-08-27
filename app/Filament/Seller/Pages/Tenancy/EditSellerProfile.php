<?php

namespace App\Filament\Seller\Pages\Tenancy;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

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
                    ->schema([
                        TextInput::make('bank_code')
                            ->label('Mã Ngân Hàng (VD: ICB, VCB)')
                            ->maxLength(255),
                        TextInput::make('bank_account_no')
                            ->label('Số Tài Khoản')
                            ->maxLength(255),
                        TextInput::make('bank_account_name')
                            ->label('Tên Chủ Tài Khoản')
                            ->maxLength(255),
                    ])->columns(3),
            ]);
    }
}
