<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Product Information')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),
                        Forms\Components\MarkdownEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Section::make('Pricing & Inventory')->schema([
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('₫'),
                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])->columns(2),

                    Forms\Components\Section::make('Hình Ảnh Sản Phẩm (Thumbnail & Gallery)')->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Ảnh đại diện (Thumbnail / Main Image)')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->imageEditor()
                            ->openable()
                            ->downloadable()
                            ->previewable(true)
                            ->maxSize(10240)
                            ->columnSpanFull()
                            ->helperText('Ảnh đại diện chính của sản phẩm.'),

                        Forms\Components\FileUpload::make('attributes_json.gallery')
                            ->label('Bộ sưu tập ảnh (Gallery Images)')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->visibility('public')
                            ->imageEditor()
                            ->openable()
                            ->downloadable()
                            ->previewable(true)
                            ->panelLayout('grid')
                            ->maxSize(10240)
                            ->columnSpanFull()
                            ->helperText('Tải lên nhiều góc ảnh để hiển thị dải thumbnail trên trang chi tiết sản phẩm.'),
                    ]),

                    Forms\Components\Section::make('Product Variants')->schema([
                        Forms\Components\Repeater::make('variants')
                            ->relationship('variants')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('sku')->label('SKU')->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('price')->numeric()->required()->prefix('₫'),
                                Forms\Components\TextInput::make('stock')->numeric()->required()->default(0),
                                Forms\Components\Toggle::make('is_active')->default(true),
                                Forms\Components\KeyValue::make('attributes_json')
                                    ->label('Attributes')
                                    ->formatStateUsing(function ($state) {
                                        if (!is_array($state)) return $state;
                                        return array_map(fn ($val) => is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string) $val, $state);
                                    })
                                    ->mutateDehydratedStateUsing(function ($state) {
                                        if (!is_array($state)) return $state;
                                        return array_map(function ($val) {
                                            if (is_string($val)) {
                                                $decoded = json_decode($val, true);
                                                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                                                    return $decoded;
                                                }
                                            }
                                            return $val;
                                        }, $state);
                                    }),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Status & Category')->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'published' => 'Published (Đã xuất bản)',
                                'draft'     => 'Draft (Bản nháp)',
                                'archived'  => 'Archived (Lưu trữ)',
                            ])
                            ->required()
                            ->default('published'),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Sản phẩm nổi bật')
                            ->helperText('Hiển thị trên trang chủ')
                            ->default(false),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                    Forms\Components\Section::make('SEO')->schema([
                        Forms\Components\TextInput::make('seo_title')->maxLength(255),
                        Forms\Components\Textarea::make('seo_description')->rows(3),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Image'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('sku')->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.') . '₫')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'draft' => 'warning',
                    'published' => 'success',
                    'archived' => 'danger',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
