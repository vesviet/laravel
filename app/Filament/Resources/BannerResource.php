<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationGroup = 'Marketing';
    
    protected static ?string $modelLabel = 'Banner Trang Chủ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hình ảnh')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Background Image')
                            ->image()
                            ->imageEditor()
                            ->directory('banners')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Kích thước khuyến nghị: 1920x800 px.'),
                    ]),

                Forms\Components\Section::make('Nội dung hiển thị')
                    ->schema([
                        Forms\Components\TextInput::make('internal_name')
                            ->label('Tên quản lý nội bộ')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('eyebrow')
                            ->label('Dòng text nhỏ ở trên (Eyebrow)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('heading')
                            ->label('Tiêu đề chính')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sub_heading')
                            ->label('Chữ in đậm trong tiêu đề')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Mô tả chi tiết')
                            ->maxLength(1024)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Nút thao tác (Call to Action)')
                    ->schema([
                        Forms\Components\TextInput::make('button_text')
                            ->label('Chữ trên nút (VD: SHOP NOW)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('button_link')
                            ->label('Đường dẫn khi click')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Cài đặt')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Kích hoạt hiển thị')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Thứ tự hiển thị')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Hình ảnh')
                    ->square(),
                Tables\Columns\TextColumn::make('internal_name')
                    ->label('Tên nội bộ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('heading')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Trạng thái'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
