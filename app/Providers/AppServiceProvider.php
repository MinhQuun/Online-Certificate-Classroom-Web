<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Enrollment;
use App\Models\StudentNotification;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            $categories = Cache::remember('student.nav.categories', now()->addMinutes(30), function () {
                try {
                    return Category::query()
                        ->with(['courses' => function ($courseQuery) {
                            $courseQuery->published()->orderBy('tenKH');
                        }])
                        ->orderBy('maDanhMuc')
                        ->get();
                } catch (\Throwable $exception) {
                    report($exception);
                    return collect();
                }
            });

            $notificationPreview = collect();
            $notificationUnread = 0;

            if (Auth::check() && Auth::user()?->student) {
                $user = Auth::user();

                try {
                    $cacheKey = 'student.nav.notifications.' . $user->getAuthIdentifier();
                    $cacheTtl = now()->addSeconds(60);

                    $notificationData = Cache::remember($cacheKey, $cacheTtl, function () use ($user) {
                        $notifier = app(StudentNotificationService::class);
                        $certificateService = app(CertificateService::class);

                        try {
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

                        return [
                            'preview' => StudentNotification::with(['course', 'combo'])
                                ->forUser($user->getAuthIdentifier())
                                ->latestFirst()
                                ->limit(5)
                                ->get(),
                            'unread' => StudentNotification::forUser($user->getAuthIdentifier())
                                ->unread()
                                ->count(),
                        ];
                    });

                    $notificationPreview = $notificationData['preview'] ?? collect();
                    $notificationUnread = $notificationData['unread'] ?? 0;
                } catch (\Throwable $exception) {
                    report($exception);
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
