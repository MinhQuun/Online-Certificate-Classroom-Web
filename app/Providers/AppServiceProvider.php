<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Enrollment;
use App\Models\StudentNotification;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\StudentNotificationService;
use App\Services\CertificateService;

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

            $notificationPreview = collect();
            $notificationUnread = 0;

            if (Auth::check() && Auth::user()?->student) {
                try {
                    $notifier = app(StudentNotificationService::class);
                    $user = Auth::user();

                    // Tự động cấp chứng chỉ nếu đủ điều kiện khi học viên tải bất kỳ trang nào
                    try {
                        $certificateService = app(CertificateService::class);
                        $enrollments = Enrollment::query()
                            ->where('maHV', $user->student->maHV)
                            ->with(['course', 'student', 'course.certificateTemplate'])
                            ->get();

                        foreach ($enrollments as $enrollment) {
                            $certificateService->issueCourseCertificateIfEligible($enrollment);
                        }
                    } catch (\Throwable $exception) {
                        report($exception);
                    }

                    $notifier->syncActivePromotionsForUser($user);
                    $notifier->syncCertificatesForUser($user);

                    $notificationPreview = StudentNotification::with(['course', 'combo'])
                        ->forUser(Auth::id())
                        ->latestFirst()
                        ->limit(5)
                        ->get();

                    $notificationUnread = StudentNotification::forUser(Auth::id())
                        ->unread()
                        ->count();
                } catch (\Throwable $exception) {
                    $notificationPreview = collect();
                    $notificationUnread = 0;
                }
            }

            $view->with([
                'studentNavCategories' => $categories,
                'studentCartCount' => StudentCart::count() + StudentComboCart::count(),
                'studentNotificationPreview' => $notificationPreview,
                'studentNotificationUnread' => $notificationUnread,
            ]);
        });
    }
}
