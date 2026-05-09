<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    use Translatable;

    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Bài viết';

    protected static ?string $modelLabel = 'Bài viết';

    protected static ?string $pluralModelLabel = 'Bài viết';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make('Nội dung bài viết')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Tiêu đề')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('Đường dẫn')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('category_id')
                        ->label('Chuyên mục')
                        ->options(fn() => \App\Models\Category::where('type', 'article')->get()->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    Forms\Components\Textarea::make('excerpt')
                        ->label('Tóm tắt')
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

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Ngày xuất bản')
                        ->default(now()),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Thứ tự')
                        ->numeric()
                        ->default(0),
                ]),

                Forms\Components\Section::make('Ảnh đại diện')->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('featured_image')
                        ->label('Ảnh đại diện')
                        ->collection('featured_image')
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
                Tables\Columns\SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Ảnh')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Chuyên mục'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Xuất bản')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Ngày')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Chuyên mục')
                    ->options(fn() => \App\Models\Category::where('type', 'article')->get()->pluck('name', 'id')),

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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
