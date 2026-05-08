<?php

return [
    'default' => env('APP_LOCALE', 'vi'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'vi_VN'),

    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'maintenance' => [
        'driver' => 'file',
    ],

    'providers' => \Illuminate\Support\ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\Filament\AdminPanelProvider::class,
        App\Providers\ThemeServiceProvider::class,
    ])->toArray(),
];
