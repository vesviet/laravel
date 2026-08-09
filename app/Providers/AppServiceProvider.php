<?php

namespace App\Providers;

use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Override Number::format to use a plain PHP fallback when the
     * PHP "intl" extension is not installed (common on shared cPanel hosting).
     */
    public function boot(): void
    {
        if (! extension_loaded('intl')) {
            // Macro overrides Number::format() with a plain number_format fallback.
            // This prevents RuntimeException on hosts without the intl extension.
            Number::macro('format', function (int|float $number, ?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string|false {
                $decimals = $precision ?? ($maxPrecision ?? 0);

                return number_format((float) $number, $decimals, '.', ',');
            });
        }
    }
}
