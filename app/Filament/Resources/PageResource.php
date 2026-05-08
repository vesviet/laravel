<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'Trang nội dung';

    protected static ?string $modelLabel = 'Trang';

    protected static ?string $pluralModelLabel = 'Trang nội dung';

    protected static ?string $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Nội dung trang')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Tiêu đề')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('Đường dẫn (URL)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->prefix('/page/'),

                    Forms\Components\Select::make('template')
                        ->label('Template')
                        ->options([
                            'default' => 'Mặc định',
                            'contact' => 'Liên hệ (có form)',
                            'about' => 'Giới thiệu',
                            'full-width' => 'Toàn trang',
                            'faq' => 'Câu hỏi thường gặp',
                        ])
                        ->default('default')
                        ->required(),

                    Forms\Components\Select::make('group')
                        ->label('Nhóm hiển thị')
                        ->options([
                            'general' => 'Thông tin chung',
                            'policy' => 'Chính sách',
                            'support' => 'Hỗ trợ',
                        ])
                        ->default('general')
                        ->required(),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('Mô tả ngắn')
                        ->rows(2)
                        ->maxLength(300),

                    Forms\Components\RichEditor::make('content')
                        ->label('Nội dung')
                        ->columnSpanFull(),
                ])->columns(2),
            ])->columnSpan(['lg' => 2]),

            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Xuất bản')->schema([
                    Forms\Components\Toggle::make('is_published')
                        ->label('Xuất bản')
                        ->default(false),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(0),
                ]),

                Forms\Components\Section::make('Ảnh banner')->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('banner')
                        ->label('Ảnh banner')
                        ->collection('banner')
                        ->image(),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->prefix('/page/')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('template')
                    ->label('Template')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'contact' => 'info',
                        'about' => 'success',
                        'faq' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('group')
                    ->label('Nhóm')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'policy' => 'danger',
                        'support' => 'warning',
                        default => 'primary',
                    }),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Xuất bản')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Nhóm')
                    ->options([
                        'general' => 'Thông tin chung',
                        'policy' => 'Chính sách',
                        'support' => 'Hỗ trợ',
                    ]),

                Tables\Filters\SelectFilter::make('template')
                    ->label('Template')
                    ->options([
                        'default' => 'Mặc định',
                        'contact' => 'Liên hệ',
                        'about' => 'Giới thiệu',
                        'faq' => 'FAQ',
                    ]),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Xuất bản'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
