<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Sản phẩm';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string $pluralModelLabel = 'Sản phẩm';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Thông tin sản phẩm')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Tên sản phẩm')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('Đường dẫn')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('sku')
                        ->label('Mã SKU')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),

                    Forms\Components\Select::make('category_id')
                        ->label('Danh mục')
                        ->options(fn() => \App\Models\Category::where('type', 'product')->get()->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    Forms\Components\Textarea::make('short_description')
                        ->label('Mô tả ngắn')
                        ->rows(2)
                        ->maxLength(500),

                    Forms\Components\RichEditor::make('description')
                        ->label('Mô tả chi tiết')
                        ->columnSpanFull(),
                ])->columns(2),

                Forms\Components\Section::make('Thông số kỹ thuật')->schema([
                    Forms\Components\KeyValue::make('features')
                        ->label('Thông số')
                        ->keyLabel('Tên thông số')
                        ->valueLabel('Giá trị')
                        ->addActionLabel('Thêm thông số')
                        ->reorderable(),
                ]),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Giá & Kho')->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Giá bán (₫)')
                        ->required()
                        ->numeric()
                        ->prefix('₫'),

                    Forms\Components\TextInput::make('original_price')
                        ->label('Giá gốc (₫)')
                        ->numeric()
                        ->prefix('₫')
                        ->helperText('Để trống nếu không giảm giá'),

                    Forms\Components\TextInput::make('stock')
                        ->label('Tồn kho')
                        ->numeric()
                        ->default(0),
                ]),

                Forms\Components\Section::make('Trạng thái')->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Hiển thị')
                        ->default(true),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Sản phẩm nổi bật'),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(0),
                ]),

                Forms\Components\Section::make('Hình ảnh')->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('main_image')
                        ->label('Ảnh chính')
                        ->collection('main_image')
                        ->image()
                        ->imageResizeMode('cover'),

                    Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Bộ sưu tập ảnh')
                        ->collection('gallery')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(10),
                ]),

                Forms\Components\Section::make('SEO')->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->maxLength(60),

                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->maxLength(160)
                        ->rows(2),
                ])->collapsed(),
            ])->columnSpan(['lg' => 1]),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('main_image')
                    ->label('Ảnh')
                    ->collection('main_image')
                    ->conversion('thumb')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Giá')
                    ->money('VND')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Kho')
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string =>
                        $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger')
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hiện')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->options(fn() => \App\Models\Category::where('type', 'product')->get()->pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Hiển thị'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Nổi bật'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
