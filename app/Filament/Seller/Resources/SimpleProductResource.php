<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\SimpleProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SimpleProductResource extends Resource
{
    protected static ?string $model = Product::class;
    
    protected static ?string $modelLabel = 'Sản phẩm';
    protected static ?string $pluralModelLabel = 'Sản phẩm';
    
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Thông tin cơ bản')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Đường dẫn (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true, modifyRuleUsing: function ($rule) {
                                return $rule->where('seller_id', auth()->user()->sellerProfile->id);
                            }),
                            
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Ảnh sản phẩm')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('1200')
                            ->directory('sellers/' . auth()->user()->sellerProfile->id . '/products')
                            ->visibility('public'),
                            
                        Forms\Components\RichEditor::make('description')
                            ->label('Mô tả sản phẩm')
                            ->hintAction(
                                Forms\Components\Actions\Action::make('ai_copywriter')
                                    ->label('✨ AI Viết hộ')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function (Forms\Set $set, $state, Forms\Get $get) {
                                        $productName = $get('name');
                                        if (!$productName) {
                                            return;
                                        }
                                        
                                        $service = app(\App\Services\AiCopywriterService::class);
                                        $content = $service->generateProductDescription($productName);
                                        $set('description', $content);
                                    })
                            ),
                    ]),
                    
                Forms\Components\Section::make('Giá & Kho')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Giá bán (VND)')
                            ->numeric()
                            ->required(),
                            
                        Forms\Components\TextInput::make('compare_at_price')
                            ->label('Giá gốc gạch ngang (VND)')
                            ->numeric(),
                            
                        Forms\Components\TextInput::make('stock')
                            ->label('Số lượng có sẵn')
                            ->numeric()
                            ->default(100)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Hình ảnh')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Kho')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Hiển thị'),
            ])
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
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
        // Since we are using TenantAware/TenantSellerScope, the global scope applies automatically.
        // Or if we strictly use Filament's query, we can keep the default. 
        // For safety, rely on the global scope or apply it here if needed.
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
            'index' => Pages\ListSimpleProducts::route('/'),
            'create' => Pages\CreateSimpleProduct::route('/create'),
            'edit' => Pages\EditSimpleProduct::route('/{record}/edit'),
        ];
    }
}
