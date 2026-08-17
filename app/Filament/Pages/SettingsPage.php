<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage as BaseSettingsPage;

class SettingsPage extends BaseSettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('site_name')
                    ->required(),
                Forms\Components\Textarea::make('site_description')
                    ->required(),
                Forms\Components\TextInput::make('contact_email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('contact_phone')
                    ->required(),
                Forms\Components\TextInput::make('facebook_link')
                    ->url(),
                Forms\Components\TextInput::make('zalo_link')
                    ->url(),
            ]);
    }
}
