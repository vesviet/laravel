<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\ProductReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationLabel = 'Đánh giá sản phẩm';

    protected static ?string $modelLabel = 'Đánh giá';

    protected static ?string $pluralModelLabel = 'Đánh giá sản phẩm';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin đánh giá')
                    ->schema([
                        Forms\Components\TextInput::make('product.name')
                            ->label('Sản phẩm')
                            ->disabled(),

                        Forms\Components\TextInput::make('customer.name')
                            ->label('Khách hàng')
                            ->disabled(),

                        Forms\Components\ViewField::make('rating')
                            ->label('Đánh giá')
                            ->view('filament.fields.star-rating'),

                        Forms\Components\Textarea::make('comment')
                            ->label('Nhận xét')
                            ->disabled()
                            ->rows(5)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->label('Trạng thái')
                            ->options([
                                'pending'  => 'Chờ duyệt',
                                'approved' => 'Đã duyệt',
                                'hidden'   => 'Đã ẩn',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->default('Ẩn danh'),

                Tables\Columns\ViewColumn::make('rating')
                    ->label('Sao')
                    ->view('filament.columns.star-rating'),

                Tables\Columns\TextColumn::make('comment')
                    ->label('Nhận xét')
                    ->limit(60)
                    ->default('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Trạng thái')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'hidden',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending'  => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'hidden'   => 'Đã ẩn',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày đăng')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending'  => 'Chờ duyệt',
                        'approved' => 'Đã duyệt',
                        'hidden'   => 'Đã ẩn',
                    ]),

                Tables\Filters\SelectFilter::make('product')
                    ->label('Sản phẩm')
                    ->relationship('product', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(ProductReview $record) => $record->status !== 'approved')
                    ->action(function (ProductReview $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Đã duyệt đánh giá')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('hide')
                    ->label('Ẩn')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->visible(fn(ProductReview $record) => $record->status !== 'hidden')
                    ->action(function (ProductReview $record) {
                        $record->update(['status' => 'hidden']);
                        Notification::make()
                            ->title('Đã ẩn đánh giá')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->label('Chi tiết'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_selected')
                    ->label('Duyệt đã chọn')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn($records) => $records->each->update(['status' => 'approved']))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('hide_selected')
                    ->label('Ẩn đã chọn')
                    ->icon('heroicon-o-eye-slash')
                    ->color('danger')
                    ->action(fn($records) => $records->each->update(['status' => 'hidden']))
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\DeleteBulkAction::make()->label('Xóa đã chọn'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReviews::route('/'),
            'edit'  => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }
}
