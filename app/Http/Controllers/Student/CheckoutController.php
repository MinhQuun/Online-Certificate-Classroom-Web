<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\ActivationCodeMail;
use App\Models\ActivationCode;
use App\Models\Combo;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\InvoiceComboItem;
use App\Models\InvoiceItem;
use App\Support\Cart\StudentCart;
use App\Support\Cart\StudentComboCart;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    private const SESSION_SELECTION = 'checkout.selection';
    private const SESSION_SUCCESS = 'checkout.success';

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

        try {
            $finalized = $this->finalizeOrder($courses, $combos, $method);
        } catch (Throwable $exception) {
            Log::error('Checkout finalizeOrder failed', [
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

        if (!empty($finalized['activation_packages'])) {
            $this->dispatchActivationEmails(
                $finalized['user'],
                $finalized['student'],
                $finalized['activation_packages']
            );
        }

        $courseTotal = (int) $courses->sum('hocPhi');
        $comboTotal = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);

        session()->put(self::SESSION_SUCCESS, [
            'courses' => $courses->map(fn ($course) => [
                'maKH' => $course->maKH,
                'tenKH' => $course->tenKH,
                'slug' => $course->slug,
                'hocPhi' => $course->hocPhi,
                'cover_image_url' => $course->cover_image_url,
                'end_date_label' => $course->end_date_label,
            ])->all(),
            'combos' => $combos->map(fn (Combo $combo) => [
                'maGoi' => $combo->maGoi,
                'tenGoi' => $combo->tenGoi,
                'slug' => $combo->slug,
                'sale_price' => $combo->sale_price,
                'original_price' => $combo->original_price,
                'cover_image_url' => $combo->cover_image_url,
            ])->all(),
            'course_total' => $courseTotal,
            'combo_total' => $comboTotal,
            'total' => $courseTotal + $comboTotal,
            'payment_method' => $method,
            'invoice_id' => $finalized['invoice']->maHD ?? null,
            'pending_activation_courses' => $finalized['pending_activation_courses'],
            'already_active_courses' => $finalized['already_active_courses'],
        ]);

        return redirect()
            ->route('student.checkout.index', ['stage' => 3])
            ->with('success', 'Đơn hàng đã được ghi nhận. Vui lòng kiểm tra email để nhận mã kích hoạt.');
    }

    private function finalizeOrder(Collection $courses, Collection $combos, string $method): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new \RuntimeException('User must be authenticated to finalize checkout.');
        }

        $student = $user->student;

        if (!$student) {
            throw new \RuntimeException('Không tìm thấy hồ sơ học viên.');
        }

        $now = Carbon::now();
        $invoice = null;
        $activationPackages = [];
        $pendingCourses = [];
        $alreadyActiveCourses = [];

        $coursePayloads = [];

        foreach ($courses as $course) {
            $coursePayloads[$course->maKH] = [
                'course' => $course,
                'combo_id' => null,
                'promotion_id' => null,
            ];
        }

        foreach ($combos as $combo) {
            $activePromotion = $combo->active_promotion;
            $promotionId = $activePromotion?->maKM;

            foreach ($combo->courses as $course) {
                $coursePayloads[$course->maKH] = [
                    'course' => $course,
                    'combo_id' => $combo->maGoi,
                    'promotion_id' => $promotionId,
                ];
            }
        }

        $totalCoursePrice = (int) $courses->sum('hocPhi');
        $totalComboPrice = (int) $combos->sum(fn (Combo $combo) => $combo->sale_price);

        DB::transaction(function () use (
            $student,
            $courses,
            $combos,
            $method,
            $coursePayloads,
            $totalCoursePrice,
            $totalComboPrice,
            $now,
            &$invoice,
            &$activationPackages,
            &$pendingCourses,
            &$alreadyActiveCourses
        ) {
            $invoiceData = [
                'maHV' => $student->maHV,
                'maTT' => $this->mapPaymentMethodToCode($method),
                'maND' => null,
                'ngayLap' => $now,
                'tongTien' => $totalCoursePrice + $totalComboPrice,
                'loai' => $combos->isNotEmpty() ? 'COMBO' : 'SINGLE_COURSE',
            ];

            if ($this->invoiceSupportsNotes()) {
                $invoiceData['ghiChu'] = 'Thanh toán qua website - ' . strtoupper($method);
            }

            $invoice = Invoice::create($invoiceData);

            foreach ($courses as $course) {
                InvoiceItem::create([
                    'maHD' => $invoice->maHD,
                    'maKH' => $course->maKH,
                    'soLuong' => 1,
                    'donGia' => (int) $course->hocPhi,
                ]);
            }

            foreach ($combos as $combo) {
                $promotion = $combo->active_promotion;

                InvoiceComboItem::create([
                    'maHD' => $invoice->maHD,
                    'maGoi' => $combo->maGoi,
                    'soLuong' => 1,
                    'donGia' => (int) $combo->sale_price,
                    'maKM' => $promotion?->maKM,
                ]);
            }

            foreach ($coursePayloads as $payload) {
                /** @var Course $course */
                $course = $payload['course'];

                $enrollment = Enrollment::firstOrNew([
                    'maHV' => $student->maHV,
                    'maKH' => $course->maKH,
                ]);

                if ($enrollment->exists && $enrollment->trangThai === 'ACTIVE') {
                    $alreadyActiveCourses[$course->maKH] = [
                        'maKH' => $course->maKH,
                        'tenKH' => $course->tenKH,
                    ];
                    continue;
                }

                if (!$enrollment->exists || empty($enrollment->ngayNhapHoc)) {
                    $enrollment->ngayNhapHoc = $now->toDateString();
                }

                if (!$enrollment->exists) {
                    $enrollment->progress_percent = 0;
                    $enrollment->video_progress_percent = 0;
                    $enrollment->avg_minitest_score = 0;
                    $enrollment->last_lesson_id = null;
                }

                $enrollment->trangThai = 'PENDING';
                $enrollment->activated_at = null;
                $enrollment->expires_at = null;
                $enrollment->maGoi = $payload['combo_id'];
                $enrollment->maKM = $payload['promotion_id'];
                $enrollment->updated_at = $now;
                $enrollment->save();

                ActivationCode::where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->whereIn('trangThai', ['CREATED', 'SENT'])
                    ->update([
                        'trangThai' => 'EXPIRED',
                        'expires_at' => $now,
                        'updated_at' => $now,
                    ]);

                $codeValue = $this->generateActivationCode($student->maHV, $course->maKH);

                $activation = ActivationCode::create([
                    'maHV' => $student->maHV,
                    'maKH' => $course->maKH,
                    'maHD' => $invoice->maHD,
                    'code' => $codeValue,
                    'trangThai' => 'CREATED',
                    'generated_at' => $now,
                ]);

                $activationPackages[] = [
                    'model' => $activation,
                    'course' => $course,
                    'code' => $codeValue,
                ];

                $pendingCourses[$course->maKH] = [
                    'maKH' => $course->maKH,
                    'tenKH' => $course->tenKH,
                ];
            }
        });

        return [
            'invoice' => $invoice,
            'student' => $student,
            'user' => $user,
            'activation_packages' => $activationPackages,
            'pending_activation_courses' => array_values($pendingCourses),
            'already_active_courses' => array_values($alreadyActiveCourses),
        ];
    }

    private function dispatchActivationEmails(\App\Models\User $user, \App\Models\Student $student, array $packages): void
    {
        if (empty($packages)) {
            return;
        }

        $courseCodes = [];
        foreach ($packages as $package) {
            $course = $package['course'];
            $courseCodes[] = [
                'course_name' => $course->tenKH,
                'code' => $package['code'],
                'course_slug' => $course->slug,
            ];
        }

        try {
            if (!empty($user->email)) {
                Mail::to($user->email)->send(
                    new ActivationCodeMail($student->hoTen ?? $user->hoTen ?? $user->name, $courseCodes)
                );
            }
        } catch (Throwable $mailException) {
            Log::error('Activation email dispatch failed', [
                'user_id' => $user->maND ?? null,
                'message' => $mailException->getMessage(),
            ]);
        }

        $ids = array_filter(array_map(fn ($package) => $package['model']->id ?? null, $packages));

        if (!empty($ids)) {
            $timestamp = Carbon::now();
            ActivationCode::whereIn('id', $ids)->update([
                'trangThai' => 'SENT',
                'sent_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    private function generateActivationCode(int $studentId, int $courseId): string
    {
        do {
            $code = sprintf(
                'OCC-%04d-%04d-%s',
                $courseId % 10000,
                $studentId % 10000,
                Str::upper(Str::random(4))
            );
        } while (ActivationCode::where('code', $code)->exists());

        return $code;
    }

    private function mapPaymentMethodToCode(string $method): ?string
    {
        $map = [
            'qr' => 'TT02',
            'bank' => 'TT01',
            'visa' => 'TT03',
        ];

        return $map[$method] ?? 'TT01';
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
        $allowed = ['qr', 'bank', 'visa'];

        return in_array($method, $allowed, true) ? $method : 'qr';
    }

    private function invoiceSupportsNotes(): bool
    {
        static $supportsNotes;

        if ($supportsNotes !== null) {
            return $supportsNotes;
        }

        try {
            $supportsNotes = Schema::hasColumn('HOADON', 'ghiChu');
        } catch (Throwable $e) {
            $supportsNotes = false;
        }

        return $supportsNotes;
    }
}

