<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Đơn hàng';

    protected static ?string $modelLabel = 'Đơn hàng';

    protected static ?string $pluralModelLabel = 'Đơn hàng';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Thông tin khách hàng')->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->label('Họ tên')
                        ->required(),

                    Forms\Components\TextInput::make('phone')
                        ->label('Số điện thoại')
                        ->required(),

                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),

                    Forms\Components\Textarea::make('address')
                        ->label('Địa chỉ')
                        ->required()
                        ->rows(2),

                    Forms\Components\TextInput::make('city')
                        ->label('Tỉnh/Thành phố'),

                    Forms\Components\TextInput::make('district')
                        ->label('Quận/Huyện'),
                ])->columns(2),

                Forms\Components\Section::make('Ghi chú')->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Ghi chú')
                        ->rows(3),
                ]),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Trạng thái')->schema([
                    Forms\Components\TextInput::make('order_number')
                        ->label('Mã đơn')
                        ->disabled(),

                    Forms\Components\Select::make('status')
                        ->label('Trạng thái')
                        ->options(Order::getStatuses())
                        ->required(),

                    Forms\Components\Select::make('payment_status')
                        ->label('Thanh toán')
                        ->options(Order::getPaymentStatuses())
                        ->required(),

                    Forms\Components\Select::make('payment_method')
                        ->label('Phương thức')
                        ->options(Order::getPaymentMethods())
                        ->disabled(),

                    Forms\Components\TextInput::make('total_amount')
                        ->label('Tổng tiền (₫)')
                        ->disabled()
                        ->prefix('₫'),
                ]),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Mã đơn')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('SĐT')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'shipping' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => Order::getStatuses()[$state] ?? $state),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        'refunded' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => Order::getPaymentStatuses()[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Order::getStatuses()),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options(Order::getPaymentStatuses()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
