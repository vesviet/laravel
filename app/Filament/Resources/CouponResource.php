<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationLabel = 'Coupons';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')->schema([
                    Forms\Components\TextInput::make('code')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true)
                        ->helperText('Uppercase code, e.g. WELCOME10')
                        ->dehydrateStateUsing(fn ($state) => strtoupper(trim($state))),
                    Forms\Components\Select::make('type')
                        ->options([
                            'percentage' => 'Percentage (%)',
                            'fixed' => 'Fixed Amount (VND)',
                        ])
                        ->required()
                        ->reactive(),
                    Forms\Components\TextInput::make('value')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->helperText(fn ($get) => $get('type') === 'percentage'
                            ? 'Enter 10 for 10% off'
                            : 'Enter amount in VND'),
                    Forms\Components\TextInput::make('min_order_amount')
                        ->numeric()
                        ->label('Minimum Order Amount (VND)')
                        ->default(0)
                        ->minValue(0),
                ])->columns(2),

                Forms\Components\Section::make('Usage & Validity')->schema([
                    Forms\Components\TextInput::make('usage_limit')
                        ->numeric()
                        ->nullable()
                        ->label('Usage Limit')
                        ->helperText('Leave empty for unlimited usage'),
                    Forms\Components\TextInput::make('used_count')
                        ->numeric()
                        ->disabled()
                        ->label('Times Used'),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->nullable()
                        ->label('Expires At'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge()->color(fn ($state) => match ($state) {
                    'percentage' => 'info',
                    'fixed' => 'success',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage'
                        ? "{$record->value}%"
                        : number_format((float) $record->value, 0, '.', ',').' VND'),
                Tables\Columns\TextColumn::make('used_count')->label('Used')->sortable(),
                Tables\Columns\TextColumn::make('usage_limit')->label('Limit')->default('∞'),
                Tables\Columns\TextColumn::make('expires_at')->dateTime()->sortable()->label('Expires'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
