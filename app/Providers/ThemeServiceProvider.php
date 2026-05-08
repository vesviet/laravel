<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Kiểm tra xem bảng settings đã tồn tại chưa (để tránh lỗi khi chạy php artisan migrate lần đầu)
        if (!app()->runningInConsole() || Schema::hasTable('settings')) {
            try {
                $activeTheme = Setting::getCached('active_theme', 'elomus');
                
                // Đăng ký thư mục views ưu tiên cho theme hiện tại
                View::prependLocation(resource_path('views/themes/' . $activeTheme));
                
                // Đăng ký thư mục components ưu tiên cho theme hiện tại
                Blade::anonymousComponentPath(resource_path('views/themes/' . $activeTheme . '/components'));

                // Share biến activeTheme để dùng chung trên toàn cục view (ví dụ để link CSS động)
                View::share('activeTheme', $activeTheme);
            } catch (\Exception $e) {
                // Fallback nếu có lỗi DB
                View::share('activeTheme', 'elomus');
            }
        } else {
            View::share('activeTheme', 'elomus');
        }
    }
}
