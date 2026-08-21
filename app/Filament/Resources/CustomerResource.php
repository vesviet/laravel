<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Orders';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active')
                    ->required(),
                // [I-09] stripe_customer_id removed — Stripe package not yet integrated.
                // Add back when Stripe integration is scoped and ADR-B3 is approved.
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                // [I-03] Use orders_sum_total_amount via withSum() to avoid N+1.
                // The orders_sum_total_amount is loaded by getEloquentQuery() below,
                // preventing a separate query per customer row in the listing.
                Tables\Columns\TextColumn::make('orders_sum_total_amount')
                    ->label('Total Spent')
                    ->formatStateUsing(fn ($state) => $state
                        ? number_format((float) $state, 0, ',', '.') . '₫'
                        : '0₫'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('membership_tier')
                    ->label('Tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VIP Diamond' => 'warning',
                        'Thành Viên Thân Thiết' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * [I-03] Override getEloquentQuery to eager-load orders sum with a single JOIN.
     * This prevents N+1 queries when listing customers with total_spent column.
     * withSum() generates a single SQL subquery instead of one query per customer.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withSum('orders', 'total_amount');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
}
