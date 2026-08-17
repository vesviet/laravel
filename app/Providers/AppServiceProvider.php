<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\SendOrderConfirmationEmail;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
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

        // A1: Domain Event bindings
        // OrderPlaced fires after ProcessCheckoutAction or ProcessLandingOrderAction commits.
        // The listener is queued (ShouldQueue) — runs via 'database' queue worker (ADR-03).
        Event::listen(OrderPlaced::class, SendOrderConfirmationEmail::class);

        // Observer: invalidate homepage cache when products change (is_featured, status, price)
        Product::observe(ProductObserver::class);

        // Rate Limiters (S4)
        // Checkout form: max 10 submissions per minute per IP
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn () => back()
                    ->with('error', 'Quá nhiều yêu cầu. Vui lòng thử lại sau 1 phút.')
                    ->withInput());
        });

        // Landing order form: max 5 submissions per minute per IP (tighter — no auth)
        RateLimiter::for('landing-order', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(fn () => back()
                    ->with('error', 'Quá nhiều yêu cầu. Vui lòng thử lại sau 1 phút.')
                    ->withInput());
        });

        // Newsletter: max 10 submissions per minute per IP
        RateLimiter::for('newsletter', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(fn () => back()->withErrors([
                    'email' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
                ]));
        });

        // Expose settings to all views
        try {
            \Illuminate\Support\Facades\View::share('globalSettings', app(\App\Settings\GeneralSettings::class));
        } catch (\Exception $e) {
            // Settings might not be migrated yet
        }
    }
}
