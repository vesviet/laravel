<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageResource\Pages;
use App\Models\LandingPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'CMS';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Page Information')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\RichEditor::make('content')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true),
                ]),
                Forms\Components\Section::make('SEO')->schema([
                    Forms\Components\TextInput::make('seo_title')->maxLength(255),
                    Forms\Components\Textarea::make('seo_description')->rows(3),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit' => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }
}
