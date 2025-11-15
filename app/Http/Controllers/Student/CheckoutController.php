<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Models\PaymentTransaction;
use App\Services\CheckoutOrderService;
use App\Services\VNPayService;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public const SESSION_SELECTION = 'checkout.selection';
    public const SESSION_SUCCESS = 'checkout.success';

    public function __construct(
        private readonly CheckoutOrderService $orderService,
        private readonly VNPayService $vnPayService
    ) {
    }

    public function start(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['string'],
        ], [
            'items.required' => 'Vui lòng chọn ít nhất một mục để thanh toán.',
            'items.min' => 'Vui lòng chọn ít nhất một mục để thanh toán.',
        ]);

        $selection = $this->sanitizeSelection($validated['items']);

        if (empty($selection['courses']) && empty($selection['combos'])) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Các mục đã chọn không còn tồn tại trong giỏ hàng.');
        }

        session()->put(self::SESSION_SELECTION, $selection);

        if (!Auth::check()) {
            $target = route('student.checkout.index');

            return redirect()
                ->route('login', ['redirect' => $target])
                ->with('info', 'Vui lòng đăng nhập hoặc đăng ký để tiếp tục thanh toán.');
        }

        return redirect()->route('student.checkout.index');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $selection = session(self::SESSION_SELECTION, ['courses' => [], 'combos' => []]);
        $successPayload = session(self::SESSION_SUCCESS);

        if (is_array($selection) && array_is_list($selection)) {
            $selection = [
                'courses' => array_map('intval', $selection),
                'combos' => [],
            ];
            session()->put(self::SESSION_SELECTION, $selection);
        }

        if ((empty($selection['courses']) && empty($selection['combos'])) && !$successPayload) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Giỏ hàng không có mục nào để thanh toán.');
        }

        $stage = max(1, min(3, (int) $request->query('stage', 1)));
        $courses = collect();
        $combos = collect();
        $courseTotal = 0;
        $comboTotal = 0;

        if ($successPayload) {
            $stage = 3;
            $courses = collect($successPayload['courses'] ?? []);
            $combos = collect($successPayload['combos'] ?? []);
            $courseTotal = (int) ($successPayload['course_total'] ?? 0);
            $comboTotal = (int) ($successPayload['combo_total'] ?? 0);
            session()->forget(self::SESSION_SUCCESS);
            session()->forget(self::SESSION_SELECTION);
        } else {
            $courseIds = $selection['courses'] ?? [];
            $comboIds = $selection['combos'] ?? [];

            $courses = $this->loadCourses($courseIds);
            $combos = $this->loadCombos($comboIds);

            if ($courses->isEmpty() && $combos->isEmpty()) {
                session()->forget(self::SESSION_SELECTION);

                return redirect()
                    ->route('student.cart.index')
                    ->with('info', 'Các mục đã chọn không còn hợp lệ hoặc đã bị xoá.');
            }

            $courseTotal = (int) $courses->sum('hocPhi');
            $comboTotal = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);
        }

        return view('Student.checkout', [
            'courses' => $courses,
            'combos' => $combos,
            'courseTotal' => $courseTotal,
            'comboTotal' => $comboTotal,
            'total' => $courseTotal + $comboTotal,
            'stage' => $stage,
            'hasSuccessPayload' => (bool) $successPayload,
            'successPayload' => $successPayload,
        ]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $selection = session(self::SESSION_SELECTION, ['courses' => [], 'combos' => []]);
        $method = $this->normalizePaymentMethod($request->input('payment_method', 'qr'));

        if (is_array($selection) && array_is_list($selection)) {
            $selection = [
                'courses' => array_map('intval', $selection),
                'combos' => [],
            ];
        }

        if (empty($selection['courses']) && empty($selection['combos'])) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Giỏ hàng không có mục nào để thanh toán.');
        }

        $courses = $this->loadCourses($selection['courses'] ?? []);
        $combos = $this->loadCombos($selection['combos'] ?? []);

        if ($courses->isEmpty() && $combos->isEmpty()) {
            session()->forget(self::SESSION_SELECTION);

            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Các mục đã chọn không còn hợp lệ hoặc đã bị xoá.');
        }

        $courseIdsPurchased = $courses->pluck('maKH')->all();
        $comboIdsPurchased = $combos->pluck('maGoi')->all();

        StudentCart::sync(array_values(array_diff(StudentCart::ids(), $courseIdsPurchased)));
        StudentComboCart::sync(array_values(array_diff(StudentComboCart::ids(), $comboIdsPurchased)));

        if ($method === 'vnpay') {
            try {
                return $this->handleVnpayCheckout($request, $courses, $combos);
            } catch (Throwable $exception) {
                Log::error('Checkout VNPay init failed', [
                    'user_id' => Auth::id(),
                    'courses' => $courseIdsPurchased,
                    'combos' => $comboIdsPurchased,
                    'message' => $exception->getMessage(),
                ]);
                report($exception);

                return redirect()
                    ->route('student.checkout.index')
                    ->with('error', 'Hệ thống đang bận, vui lòng thử lại sau ít phút.');
            }
        }

        try {
            $finalized = $this->orderService->finalize(Auth::user(), $courses, $combos, $method);
        } catch (Throwable $exception) {
            Log::error('Checkout finalize failed', [
                'user_id' => Auth::id(),
                'courses' => $courseIdsPurchased,
                'combos' => $comboIdsPurchased,
                'message' => $exception->getMessage(),
            ]);
            report($exception);

            return redirect()
                ->route('student.checkout.index')
                ->with('error', 'Hệ thống đang bận, vui lòng thử lại sau ít phút.');
        }

        $courseTotal = (int) ($finalized['course_total'] ?? $courses->sum('hocPhi'));
        $comboTotal = (int) ($finalized['combo_total'] ?? $combos->sum(fn (Combo $combo) => $combo->sale_price));

        $successPayload = self::createSuccessPayload(
            $courses,
            $combos,
            $courseTotal,
            $comboTotal,
            $method,
            $finalized
        );

        session()->put(self::SESSION_SUCCESS, $successPayload);
        session()->forget(self::SESSION_SELECTION);

        return redirect()
            ->route('student.checkout.index', ['stage' => 3])
            ->with('success', 'Đơn hàng đã được ghi nhận. Vui lòng kiểm tra email để nhận mã kích hoạt.');
    }
    private function handleVnpayCheckout(Request $request, Collection $courses, Collection $combos): RedirectResponse
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
            'maHV' => $user->student->maHV,
            'soTien' => $snapshot['total'],
            'txn_ref' => $txnRef,
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

        session()->forget(self::SESSION_SELECTION);

        return redirect()->away($paymentUrl);
    }

    private function buildTransactionSnapshot(Collection $courses, Collection $combos, int $courseTotal, int $comboTotal): array
    {
        if ($combos->isNotEmpty()) {
            $combos->each->loadMissing('courses', 'promotions');
        }

        return [
            'courses' => $courses->map(fn (Course $course) => [
                'maKH' => $course->maKH,
                'tenKH' => $course->tenKH,
                'slug' => $course->slug,
                'hocPhi' => (int) $course->hocPhi,
                'cover_image_url' => $course->cover_image_url,
                'end_date_label' => $course->end_date_label,
            ])->values()->all(),
            'combos' => $combos->map(function (Combo $combo) {
                return [
                    'maGoi' => $combo->maGoi,
                    'tenGoi' => $combo->tenGoi,
                    'slug' => $combo->slug,
                    'sale_price' => (int) $combo->sale_price,
                    'original_price' => (int) $combo->original_price,
                    'cover_image_url' => $combo->cover_image_url,
                    'course_ids' => $combo->courses->pluck('maKH')->all(),
                    'promotion_id' => $combo->active_promotion?->maKM,
                ];
            })->values()->all(),
            'course_total' => $courseTotal,
            'combo_total' => $comboTotal,
            'total' => $courseTotal + $comboTotal,
            'payment_method' => 'vnpay',
        ];
    }

    private function generateTransactionReference(): string
    {
        do {
            $reference = Str::upper(Str::random(12));
        } while (PaymentTransaction::where('txn_ref', $reference)->exists());

        return $reference;
    }

    public static function createSuccessPayload(
        Collection $courses,
        Collection $combos,
        int $courseTotal,
        int $comboTotal,
        string $method,
        array $finalized
    ): array {
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
            'payment_method' => $method,
            'invoice_id' => $finalized['invoice']->maHD ?? null,
            'already_active_courses' => $finalized['already_active_courses'] ?? [],
        ];
    }
    private function sanitizeSelection(array $items): array
    {
        $courseIds = [];
        $comboIds = [];

        foreach ($items as $value) {
            if (!is_string($value)) {
                continue;
            }

            if (str_starts_with($value, 'combo:')) {
                $comboIds[] = (int) substr($value, 6);
                continue;
            }

            if (str_starts_with($value, 'course:')) {
                $courseIds[] = (int) substr($value, 7);
                continue;
            }

            if (is_numeric($value)) {
                $courseIds[] = (int) $value;
            }
        }

        $courseIds = array_values(array_intersect(StudentCart::ids(), $courseIds));
        $comboIds = array_values(array_intersect(StudentComboCart::ids(), $comboIds));

        return [
            'courses' => $courseIds,
            'combos' => $comboIds,
        ];
    }

    private function loadCourses(array $ids): Collection
    {
        if (empty($ids)) {
            return collect();
        }

        $courses = Course::published()
            ->whereIn('maKH', $ids)
            ->with(['teacher'])
            ->get()
            ->keyBy('maKH');

        return collect($ids)
            ->map(fn (int $id) => $courses->get($id))
            ->filter();
    }

    private function loadCombos(array $ids): Collection
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

    private function normalizePaymentMethod(string $method): string
    {
        $allowed = ['qr', 'bank', 'visa', 'vnpay'];

        return in_array($method, $allowed, true) ? $method : 'qr';
    }

}
