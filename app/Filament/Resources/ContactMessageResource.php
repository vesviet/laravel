<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Tin nhắn liên hệ';

    protected static ?string $modelLabel = 'Tin nhắn';

    protected static ?string $pluralModelLabel = 'Tin nhắn liên hệ';

    protected static ?string $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::unread()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin khách hàng')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Họ tên')
                    ->disabled(),

                Forms\Components\TextInput::make('phone')
                    ->label('Số điện thoại')
                    ->disabled(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(),

                Forms\Components\Textarea::make('message')
                    ->label('Nội dung tin nhắn')
                    ->disabled()
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(3),

            Forms\Components\Section::make('Quản lý')->schema([
                Forms\Components\Toggle::make('is_read')
                    ->label('Đã đọc'),

                Forms\Components\Textarea::make('notes')
                    ->label('Ghi chú nội bộ')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->width('40px'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Họ tên')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('SĐT')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('message')
                    ->label('Nội dung')
                    ->limit(50)
                    ->tooltip(fn(ContactMessage $record) => $record->message),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Trạng thái')
                    ->trueLabel('Đã đọc')
                    ->falseLabel('Chưa đọc'),
            ])
            ->actions([
                Tables\Actions\Action::make('markRead')
                    ->label('Đã đọc')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn(ContactMessage $record) => $record->update(['is_read' => true]))
                    ->visible(fn(ContactMessage $record) => !$record->is_read)
                    ->requiresConfirmation(false),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
