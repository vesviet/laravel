<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageResource\Pages;
use App\Models\LandingPage;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?string $navigationLabel = 'Landing Pages';

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ── Page Info ────────────────────────────────────────────────────
            Forms\Components\Section::make('Thông tin trang')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Tiêu đề')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText(fn ($state) => $state
                            ? 'URL: ' . url('/' . $state)
                            : 'Truy cập tại: /{slug}'
                        )
                        ->live(onBlur: true),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Đang hoạt động')
                        ->default(true),
                ])
                ->columns(2),

            // ── SEO ───────────────────────────────────────────────────────────
            Forms\Components\Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('seo_title')
                        ->label('SEO Title')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('seo_description')
                        ->label('SEO Description')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->columns(1)
                ->collapsed(),

            // ── Product Link ─────────────────────────────────────────────────
            Forms\Components\Section::make('Sản phẩm liên kết')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Sản phẩm')
                        ->searchable()
                        ->getSearchResultsUsing(
                            fn (string $search): array => Product::where('name', 'like', "%{$search}%")
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->getOptionLabelUsing(
                            fn ($value): ?string => Product::find($value)?->name
                        )
                        ->nullable()
                        ->placeholder('-- Không liên kết sản phẩm --')
                        ->helperText('Gõ để tìm sản phẩm. Giá và hình ảnh sẽ được lấy tự động.'),
                ]),

            // ── Combo Rules ──────────────────────────────────────────────────
            Forms\Components\Section::make('Gói Combo')
                ->schema([
                    Forms\Components\Repeater::make('combo_rules_json')
                        ->label('Danh sách combo')
                        ->schema([
                            Forms\Components\Hidden::make('id')
                                ->default(fn () => (string) Str::uuid()),

                            Forms\Components\TextInput::make('name')
                                ->label('Tên combo')
                                ->required()
                                ->placeholder('VD: Mua 1 tặng 1, Combo 3 cái...')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('price')
                                ->label('Giá (VNĐ)')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->suffix('₫')
                                ->placeholder('299000'),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Thêm combo')
                        ->reorderable()
                        ->helperText('Hiển thị dạng radio buttons cho khách chọn.'),
                ])
                ->collapsed(),

            // ── Features ─────────────────────────────────────────────────────
            Forms\Components\Section::make('Điểm nổi bật')
                ->schema([
                    Forms\Components\Repeater::make('features_json')
                        ->label('Danh sách điểm nổi bật')
                        ->schema([
                            Forms\Components\TextInput::make('text')
                                ->label('')
                                ->placeholder('VD: Chất liệu cotton 100%, thoáng mát...')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->addActionLabel('+ Thêm điểm nổi bật')
                        ->reorderable()
                        ->collapsed(false)
                        ->helperText('Hiển thị dạng bullet points ✓ bên dưới hình sản phẩm.'),
                ])
                ->collapsed(),

            // ── Header ───────────────────────────────────────────────────────
            Forms\Components\Section::make('Header')
                ->schema([
                    Forms\Components\TextInput::make('header_logo_url')
                        ->label('URL Logo')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://...'),

                    Forms\Components\TextInput::make('header_cta_text')
                        ->label('Nút CTA')
                        ->maxLength(100)
                        ->placeholder('Đặt hàng ngay'),
                ])
                ->columns(2)
                ->collapsed(),

            // ── Urgency / FOMO ───────────────────────────────────────────────
            Forms\Components\Section::make('Tạo khan hiếm (Urgency)')
                ->schema([
                    Forms\Components\DateTimePicker::make('urgency_end_time')
                        ->label('Thời gian kết thúc ưu đãi')
                        ->nullable()
                        ->helperText('Đếm ngược realtime đến thời điểm này. Để trống nếu không dùng.'),

                    Forms\Components\TextInput::make('urgency_fake_views')
                        ->label('Số lượt xem (hiển thị)')
                        ->numeric()
                        ->nullable()
                        ->minValue(0)
                        ->placeholder('0'),
                ])
                ->columns(2)
                ->collapsed(),

            // ── Tracking Pixels ──────────────────────────────────────────────
            Forms\Components\Section::make('Tracking Pixels')
                ->schema([
                    Forms\Components\TextInput::make('facebook_pixel_id')
                        ->label('Facebook Pixel ID')
                        ->maxLength(100)
                        ->placeholder('123456789012345'),

                    Forms\Components\TextInput::make('tiktok_pixel_id')
                        ->label('TikTok Pixel ID')
                        ->maxLength(100)
                        ->placeholder('CXXXXXXXXXXXXXXX'),
                ])
                ->columns(2)
                ->collapsed(),

            // ── Footer ───────────────────────────────────────────────────────
            Forms\Components\Section::make('Footer (Markdown)')
                ->schema([
                    Forms\Components\MarkdownEditor::make('footer_content')
                        ->label('Nội dung footer')
                        ->columnSpanFull()
                        ->helperText('Hỗ trợ Markdown. Ví dụ: chính sách đổi trả, bảo hành, liên hệ...'),
                ])
                ->collapsed(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Đơn hàng')
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tạo lúc')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Trạng thái')
                    ->trueLabel('Đang hoạt động')
                    ->falseLabel('Tắt'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Xem trang')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (LandingPage $record) => route('landing.show', $record->slug))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit'   => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
