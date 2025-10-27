<?php

namespace App\Providers;

use App\Models\Category;
use App\Support\Cart\StudentCart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Support/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.student', function ($view) {
            try {
                $categories = Category::query()
                    ->with(['courses' => function ($courseQuery) {
                        $courseQuery->published()->orderBy('tenKH');
                    }])
                    ->orderBy('maDanhMuc')
                    ->get();
            } catch (\Throwable $exception) {
                $categories = collect();
            }

            $view->with([
                'studentNavCategories' => $categories,
                'studentCartCount' => StudentCart::count(),
            ]);
        });
    }
}
