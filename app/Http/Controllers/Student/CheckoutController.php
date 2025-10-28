<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Enrollment;
use App\Models\ActivationCode;

use App\Support\Cart\StudentCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ActivationCodeMail;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Throwable;

class CheckoutController extends Controller
{
    private const SESSION_SELECTION = 'checkout.selection';
    private const SESSION_SUCCESS   = 'checkout.success';

    public function start(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items'   => ['required', 'array', 'min:1'],
            'items.*' => ['integer'],
        ], [
            'items.required' => 'Vui lòng chọn ít nhất 1 khóa học.',
            'items.min'      => 'Vui lòng chọn ít nhất 1 khóa học.',
        ]);

        $selected = $this->sanitizeSelection($validated['items']);

        if (empty($selected)) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Khóa học chọn không hợp lệ.');
        }

        session()->put(self::SESSION_SELECTION, $selected);

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
        $selection      = session(self::SESSION_SELECTION, []);
        $successPayload = session(self::SESSION_SUCCESS);

        if (empty($selection) && !$successPayload) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Giỏ hàng không có khóa học để thanh toán.');
        }

        $stage   = max(1, min(3, (int) $request->query('stage', 1)));
        $courses = collect();
        $total   = 0;

        if ($successPayload) {
            $stage   = 3;
            $courses = collect($successPayload['courses'] ?? []);
            $total   = (int) ($successPayload['total'] ?? 0);
            session()->forget(self::SESSION_SUCCESS);
            session()->forget(self::SESSION_SELECTION);
        } else {
            $courses = $this->loadCourses($selection);

            if ($courses->isEmpty()) {
                session()->forget(self::SESSION_SELECTION);

                return redirect()
                    ->route('student.cart.index')
                    ->with('info', 'Khóa học không hợp lệ hoặc đã bị xoá.');
            }

            $total = (int) $courses->sum('hocPhi');
        }

        return view('Student.checkout', [
            'courses'          => $courses,
            'total'            => $total,
            'stage'            => $stage,
            'hasSuccessPayload'=> (bool) $successPayload,
            'successPayload' => $successPayload,
        ]);
    }

    public function complete(Request $request): RedirectResponse
    {
        $selection = session(self::SESSION_SELECTION, []);
        $method = $this->normalizePaymentMethod($request->input('payment_method', 'qr'));

        if (empty($selection)) {
            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Giỏ hàng không có khóa học để thanh toán.');
        }

        $courses = $this->loadCourses($selection);

        if ($courses->isEmpty()) {
            session()->forget(self::SESSION_SELECTION);

            return redirect()
                ->route('student.cart.index')
                ->with('info', 'Khóa học không hợp lệ hoặc đã bị xoá.');
        }

        $total        = (int) $courses->sum('hocPhi');
        $purchasedIds = $courses->pluck('maKH')->all();
        $remaining    = array_values(array_diff(StudentCart::ids(), $purchasedIds));
        StudentCart::sync($remaining);

        try {
            $finalized = $this->finalizeOrder($courses, $method, $total);
        } catch (Throwable $exception) {
            Log::error('Checkout finalizeOrder failed', [
                'user_id'    => Auth::id(),
                'course_ids' => $courses->pluck('maKH')->all(),
                'message'    => $exception->getMessage(),
            ]);
            report($exception);

            return redirect()
                ->route('student.checkout.index')
                ->with('error', 'Hệ thống đang bận, vui lòng thử lại sau vài phút.');
        }

        if (!empty($finalized['activation_packages'])) {
            $this->dispatchActivationEmails(
                $finalized['user'],
                $finalized['student'],
                $finalized['activation_packages']
            );
        }

        session()->put(self::SESSION_SUCCESS, [
            'courses' => $courses->map(fn ($course) => [
                'maKH'           => $course->maKH,
                'tenKH'          => $course->tenKH,
                'slug'           => $course->slug,
                'hocPhi'         => $course->hocPhi,
                'cover_image_url'=> $course->cover_image_url,
                'end_date_label' => $course->end_date_label,
            ])->all(),
            'total' => $total,
            'payment_method' => $method,
            'invoice_id' => $finalized['invoice']->maHD ?? null,
            'pending_activation_courses' => $finalized['pending_activation_courses'],
            'already_active_courses' => $finalized['already_active_courses'],
        ]);

        return redirect()
            ->route('student.checkout.index', ['stage' => 3])
            ->with('success', 'Đơn hàng của bạn đã được ghi nhận. Vui lòng kiểm tra email để nhận mã kích hoạt cho từng khóa học.');
    }

    private function finalizeOrder(Collection $courses, string $method, int $total): array
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

        DB::transaction(function () use ($courses, $method, $total, $student, $now, &$invoice, &$activationPackages, &$pendingCourses, &$alreadyActiveCourses) {
            $invoice = Invoice::create([
                'maHV'     => $student->maHV,
                'maTT'     => $this->mapPaymentMethodToCode($method),
                'maND'     => null,
                'ngayLap'  => $now,
                'tongTien' => $total,
                'ghiChu'   => 'Thanh toan qua website - ' . strtoupper($method),
            ]);

            foreach ($courses as $course) {
                if (!$course) {
                    continue;
                }

                InvoiceItem::create([
                    'maHD'    => $invoice->maHD,
                    'maKH'    => $course->maKH,
                    'soLuong' => 1,
                    'donGia'  => (int) $course->hocPhi,
                ]);

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
                    'maHV'        => $student->maHV,
                    'maKH'        => $course->maKH,
                    'maHD'        => $invoice->maHD,
                    'code'        => $codeValue,
                    'trangThai'   => 'CREATED',
                    'generated_at'=> $now,
                ]);

                $activationPackages[] = [
                    'model'  => $activation,
                    'course' => $course,
                    'code'   => $codeValue,
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
                'sent_at'   => $timestamp,
                'updated_at'=> $timestamp,
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
            'qr'   => 'TT02',
            'bank' => 'TT01',
            'visa' => 'TT03',
        ];

        return $map[$method] ?? 'TT01';
    }

    private function sanitizeSelection(array $ids): array
    {
        $cartIds = StudentCart::ids();

        return array_values(array_unique(
            array_intersect(
                array_map('intval', $ids),
                $cartIds
            )
        ));
    }

    private function loadCourses(array $selection): Collection
    {
        if (empty($selection)) {
            return collect();
        }

        $courses = Course::published()
            ->whereIn('maKH', $selection)
            ->with(['teacher'])
            ->get()
            ->keyBy('maKH');

        return collect($selection)
            ->map(fn (int $id) => $courses->get($id))
            ->filter();
    }

    private function normalizePaymentMethod(string $method): string
    {
        $allowed = ['qr', 'bank', 'visa'];

        return in_array($method, $allowed, true) ? $method : 'qr';
    }
}
