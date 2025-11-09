<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Models\PaymentTransaction;
use App\Services\CheckoutOrderService;
use App\Services\VNPayService;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutOrderService $orderService,
        private readonly VNPayService $vnPayService
    ) {
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items'      => ['required_without_all:courses,combos', 'array'],
            'items.*'    => ['string'],
            'courses'    => ['required_without:items', 'array'],
            'courses.*'  => ['integer'],
            'combos'     => ['nullable', 'array'],
            'combos.*'   => ['integer'],
        ]);

        $selection = $this->resolveSelection($validated);

        if (empty($selection['courses']) && empty($selection['combos'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Các mục được chọn không còn trong giỏ hàng.',
            ], 422);
        }

        $courses = $this->loadCourses($selection['courses']);
        $combos = $this->loadCombos($selection['combos']);

        if ($courses->isEmpty() && $combos->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy dữ liệu cần thanh toán.',
            ], 422);
        }

        $courseTotal = (int) $courses->sum('hocPhi');
        $comboTotal = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy thông tin tạm tính thành công.',
            'data'    => [
                'courses'      => $this->transformCourses($courses),
                'combos'       => $this->transformCombos($combos),
                'course_total' => $courseTotal,
                'combo_total'  => $comboTotal,
                'total'        => $courseTotal + $comboTotal,
            ],
        ]);
    }

    public function complete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string'],
            'items'          => ['required_without_all:courses,combos', 'array'],
            'items.*'        => ['string'],
            'courses'        => ['required_without:items', 'array'],
            'courses.*'      => ['integer'],
            'combos'         => ['nullable', 'array'],
            'combos.*'       => ['integer'],
        ]);

        $selection = $this->resolveSelection($validated);

        if (empty($selection['courses']) && empty($selection['combos'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Các mục được chọn không còn trong giỏ hàng.',
            ], 422);
        }

        $courses = $this->loadCourses($selection['courses']);
        $combos = $this->loadCombos($selection['combos']);

        if ($courses->isEmpty() && $combos->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Không tìm thấy dữ liệu cần thanh toán.',
            ], 422);
        }

        $method = $this->normalizePaymentMethod($validated['payment_method']);

        $courseIdsPurchased = $courses->pluck('maKH')->all();
        $comboIdsPurchased = $combos->pluck('maGoi')->all();

        StudentCart::sync(array_values(array_diff(StudentCart::ids(), $courseIdsPurchased)));
        StudentComboCart::sync(array_values(array_diff(StudentComboCart::ids(), $comboIdsPurchased)));

        if ($method === 'vnpay') {
            try {
                return $this->handleVnpayCheckout($request, $courses, $combos);
            } catch (Throwable $exception) {
                Log::error('API Checkout VNPay init failed', [
                    'user_id' => Auth::id(),
                    'courses' => $courseIdsPurchased,
                    'combos'  => $comboIdsPurchased,
                    'message' => $exception->getMessage(),
                ]);
                report($exception);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Không thể khởi tạo thanh toán VNPay. Vui lòng thử lại.',
                ], 500);
            }
        }

        try {
            $finalized = $this->orderService->finalize(Auth::user(), $courses, $combos, $method);
        } catch (Throwable $exception) {
            Log::error('API Checkout finalize failed', [
                'user_id' => Auth::id(),
                'courses' => $courseIdsPurchased,
                'combos'  => $comboIdsPurchased,
                'message' => $exception->getMessage(),
            ]);
            report($exception);

            return response()->json([
                'status'  => 'error',
                'message' => 'Không thể hoàn tất đơn hàng. Vui lòng thử lại.',
            ], 500);
        }

        if (
            !empty($finalized['course_activation_packages']) ||
            !empty($finalized['combo_activation_packages'])
        ) {
            $this->orderService->dispatchActivationEmails(
                $finalized['user'],
                $finalized['student'],
                $finalized['course_activation_packages'] ?? [],
                $finalized['combo_activation_packages'] ?? []
            );
        }

        $courseTotal = (int) ($finalized['course_total'] ?? $courses->sum('hocPhi'));
        $comboTotal = (int) ($finalized['combo_total'] ?? $combos->sum(fn (Combo $combo) => $combo->sale_price));

        $payload = $this->createSuccessPayload(
            $courses,
            $combos,
            $courseTotal,
            $comboTotal,
            $method,
            $finalized
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Đơn hàng đã được ghi nhận thành công.',
            'data'    => $payload,
        ]);
    }

    protected function handleVnpayCheckout(Request $request, Collection $courses, Collection $combos): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->student) {
            throw new \RuntimeException('Người dùng cần đăng nhập để thanh toán.');
        }

        if ($combos->isNotEmpty()) {
            $combos->each->loadMissing('courses', 'promotions');
        }

        $courseTotal = (int) $courses->sum('hocPhi');
        $comboTotal = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);

        $snapshot = $this->buildTransactionSnapshot($courses, $combos, $courseTotal, $comboTotal);
        $txnRef = $this->generateTransactionReference();

        $transaction = PaymentTransaction::create([
            'maHV'      => $user->student->maHV,
            'soTien'    => $snapshot['total'],
            'txn_ref'   => $txnRef,
            'trangThai' => PaymentTransaction::STATUS_PENDING,
            'order_snapshot' => $snapshot,
            'client_ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        $paymentUrl = $this->vnPayService->buildPaymentUrl($transaction, [
            'order_info' => 'Thanh toán giỏ hàng #' . $txnRef,
            'ip_address' => $request->ip(),
        ]);

        $transaction->update(['payment_url' => $paymentUrl]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Khởi tạo giao dịch VNPay thành công.',
            'data'    => [
                'payment_url' => $paymentUrl,
                'transaction_reference' => $txnRef,
            ],
        ]);
    }

    protected function resolveSelection(array $payload): array
    {
        $courseIds = [];
        $comboIds = [];

        if (!empty($payload['items'])) {
            foreach ($payload['items'] as $value) {
                if (!is_string($value)) {
                    continue;
                }

                if (str_starts_with($value, 'combo:')) {
                    $comboIds[] = (int) substr($value, 6);
                } elseif (str_starts_with($value, 'course:')) {
                    $courseIds[] = (int) substr($value, 7);
                } elseif (is_numeric($value)) {
                    $courseIds[] = (int) $value;
                }
            }
        } else {
            $courseIds = array_map('intval', $payload['courses'] ?? []);
            $comboIds = array_map('intval', $payload['combos'] ?? []);
        }

        $courseIds = array_values(array_intersect(StudentCart::ids(), $courseIds));
        $comboIds = array_values(array_intersect(StudentComboCart::ids(), $comboIds));

        return [
            'courses' => $courseIds,
            'combos'  => $comboIds,
        ];
    }

    protected function loadCourses(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }

        $courses = Course::published()
            ->whereIn('maKH', $ids)
            ->with('teacher')
            ->get()
            ->keyBy('maKH');

        return collect($ids)
            ->map(fn (int $id) => $courses->get($id))
            ->filter();
    }

    protected function loadCombos(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }

        $combos = Combo::with(['courses', 'promotions'])
            ->whereIn('maGoi', $ids)
            ->get()
            ->keyBy('maGoi');

        return collect($ids)
            ->map(fn (int $id) => $combos->get($id))
            ->filter();
    }

    protected function transformCourses(Collection $courses): array
    {
        return $courses->map(function (Course $course) {
            return [
                'id'     => $course->maKH,
                'title'  => $course->tenKH,
                'slug'   => $course->slug,
                'price'  => (int) $course->hocPhi,
                'cover'  => $course->cover_image_url,
            ];
        })->values()->all();
    }

    protected function transformCombos(Collection $combos): array
    {
        return $combos->map(function (Combo $combo) {
            return [
                'id'             => $combo->maGoi,
                'title'          => $combo->tenGoi,
                'slug'           => $combo->slug,
                'price'          => (int) $combo->sale_price,
                'original_price' => (int) $combo->original_price,
                'cover'          => $combo->cover_image_url,
            ];
        })->values()->all();
    }

    protected function normalizePaymentMethod(string $method): string
    {
        $allowed = ['qr', 'bank', 'visa', 'vnpay'];

        return in_array($method, $allowed, true) ? $method : 'qr';
    }

    protected function buildTransactionSnapshot(Collection $courses, Collection $combos, int $courseTotal, int $comboTotal): array
    {
        return [
            'courses' => $courses->map(fn (Course $course) => [
                'maKH' => $course->maKH,
                'tenKH' => $course->tenKH,
                'slug' => $course->slug,
                'hocPhi' => (int) $course->hocPhi,
                'cover_image_url' => $course->cover_image_url,
                'end_date_label' => $course->end_date_label,
            ])->values()->all(),
            'combos' => $combos->map(fn (Combo $combo) => [
                'maGoi' => $combo->maGoi,
                'tenGoi' => $combo->tenGoi,
                'slug' => $combo->slug,
                'sale_price' => (int) $combo->sale_price,
                'original_price' => (int) $combo->original_price,
                'cover_image_url' => $combo->cover_image_url,
            ])->values()->all(),
            'course_total' => $courseTotal,
            'combo_total' => $comboTotal,
            'total' => $courseTotal + $comboTotal,
        ];
    }

    protected function generateTransactionReference(): string
    {
        return 'OCC' . now()->format('YmdHis') . Str::upper(Str::random(4));
    }

    protected function createSuccessPayload(
        Collection $courses,
        Collection $combos,
        int $courseTotal,
        int $comboTotal,
        string $method,
        array $finalized
    ): array {
        return [
            'courses' => $this->transformCourses($courses),
            'combos'  => $this->transformCombos($combos),
            'course_total' => $courseTotal,
            'combo_total'  => $comboTotal,
            'total'        => $courseTotal + $comboTotal,
            'payment_method' => $method,
            'invoice_id' => $finalized['invoice']->maHD ?? null,
            'pending_activation_courses' => $finalized['pending_activation_courses'] ?? [],
            'pending_activation_combos' => $finalized['pending_activation_combos'] ?? [],
            'already_active_courses' => $finalized['already_active_courses'] ?? [],
        ];
    }
}
