<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Location;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        // Tự động truyền danh sách Cơ sở vào file giao diện partials.navbar
        View::composer('partials.navbar', function ($view) {
            $view->with('locations', Location::all());
        });
        // 3. Chia sẻ biến $globalLocations cho TẤT CẢ các View
        // Lấy tất cả địa điểm đang hoạt động
        Paginator::useBootstrapFive();
        try {
            $locations = Location::all(); // Hoặc Location::where('active', 1)->get();
            View::share('globalLocations', $locations);
        } catch (\Exception $e) {
            // Để tránh lỗi khi chưa chạy migrate bảng locations
            View::share('globalLocations', []);
        }
        if (env('APP_ENV') !== 'local') {
            \URL::forceScheme('https');
        }
    }
}