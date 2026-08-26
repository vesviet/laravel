<?php

namespace App\Filament\Seller\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Actions\RegisterSellerAction;

class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Tài khoản')
                        ->description('Thông tin đăng nhập')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                    Wizard\Step::make('Cửa hàng')
                        ->description('Thiết lập gian hàng')
                        ->schema([
                            TextInput::make('shop_name')
                                ->label('Tên Shop')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Số điện thoại liên hệ')
                                ->tel()
                                ->required()
                                ->maxLength(20),
                            Select::make('industry')
                                ->label('Ngành hàng')
                                ->options([
                                    'fashion' => 'Thời trang & Phụ kiện',
                                    'food' => 'Ẩm thực & Đồ uống',
                                    'cosmetics' => 'Mỹ phẩm & Làm đẹp',
                                    'general' => 'Khác',
                                ])
                                ->required(),
                        ]),
                ])->submitAction(new \Illuminate\Support\HtmlString('<button type="submit" class="filament-button">Hoàn tất đăng ký</button>')),
            ])->statePath('data');
    }

    protected function handleRegistration(array $data): \Illuminate\Database\Eloquent\Model
    {
        // First register the user using the base class method
        $user = parent::handleRegistration($data);

        // Then execute the RegisterSellerAction
        $action = new RegisterSellerAction();
        $action->execute([
            'user_id' => $user->id,
            'shop_name' => $data['shop_name'],
            'phone' => $data['phone'],
            'industry' => $data['industry'],
        ]);

        return $user;
    }
}
