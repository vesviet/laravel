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
        View::composer('layouts.app', function ($view) {
            $footerPages = Cache::rememberForever('footer_pages', function () {
                return Page::published()
                    ->ordered()
                    ->get(['id', 'title', 'slug', 'group'])
                    ->groupBy('group');
            });

            $view->with('footerPages', $footerPages);
        });
    }
}
