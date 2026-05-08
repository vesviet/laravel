<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Danh mục';

    protected static ?string $modelLabel = 'Danh mục';

    protected static ?string $pluralModelLabel = 'Danh mục';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Thông tin danh mục')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tên danh mục')
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

                Forms\Components\Select::make('type')
                    ->label('Loại')
                    ->options([
                        'product' => 'Sản phẩm',
                        'article' => 'Bài viết',
                    ])
                    ->default('product')
                    ->required(),

                Forms\Components\Select::make('parent_id')
                    ->label('Danh mục cha')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->nullable(),

                Forms\Components\Textarea::make('description')
                    ->label('Mô tả')
                    ->rows(3),

                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->label('Hình ảnh')
                    ->collection('image')
                    ->image()
                    ->imageResizeMode('cover'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->default(0),
            ])->columns(2),

            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(60),

                Forms\Components\Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->maxLength(160)
                    ->rows(2),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'product' => 'success',
                        'article' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => $state === 'product' ? 'Sản phẩm' : 'Bài viết'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Danh mục cha')
                    ->default('—'),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Số SP')
                    ->counts('products'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại')
                    ->options([
                        'product' => 'Sản phẩm',
                        'article' => 'Bài viết',
                    ]),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
