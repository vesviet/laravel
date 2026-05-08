<?php

namespace App\Providers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\CartService::class);
    }

    public function boot(): void
    {
        // Force HTTPS to prevent Mixed Content errors behind proxies
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https' || str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer(['layouts.app', 'themes.woodmart.layouts.app'], function ($view) {
            $footerPages = Cache::rememberForever('footer_pages', function () {
                return Page::published()
                    ->ordered()
                    ->get(['id', 'title', 'slug', 'group'])
                    ->groupBy('group');
            });

            // SRE Requirement: Fallback and Cache parsing
            $mainMenuRaw = \App\Models\Setting::getCached('main_menu');
            $mainMenu = [];
            
            try {
                if (is_string($mainMenuRaw)) {
                    $mainMenu = json_decode($mainMenuRaw, true) ?? [];
                } elseif (is_array($mainMenuRaw)) {
                    $mainMenu = $mainMenuRaw;
                }
            } catch (\Exception $e) {
                // Fallback to empty array if parsing fails (SRE requirement)
                $mainMenu = [];
                \Illuminate\Support\Facades\Log::error('Failed to parse main_menu JSON: ' . $e->getMessage());
            }

            $view->with('footerPages', $footerPages)
                 ->with('mainMenu', $mainMenu);
        });
    }
}
