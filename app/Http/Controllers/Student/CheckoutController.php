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
use App\Mail\ActivationCodeMail;
use Illuminate\View\View;

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

        $this->activateEnrollments($courses);

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
        ]);

        return redirect()
            ->route('student.checkout.index', ['stage' => 3])
            ->with('success', 'Đơn hàng của bạn đã được ghi nhận. Vui lòng hoàn tất bước cuối.');
    }

    private function activateEnrollments(Collection $courses): void
    {
        $userId = Auth::id();

        if (!$userId || $courses->isEmpty()) {
            return;
        }

        $student = DB::table('HOCVIEN')->where('maND', $userId)->first();

        if (!$student) {
            return;
        }

        $now = Carbon::now();

        DB::transaction(function () use ($courses, $student, $now) {
            foreach ($courses as $course) {
                if (!$course) {
                    continue;
                }

                $expiresAt = null;

                if (!empty($course->thoiHanNgay)) {
                    $expiresAt = $now->copy()->addDays((int) $course->thoiHanNgay);
                }

                $exists = DB::table('HOCVIEN_KHOAHOC')
                    ->where('maHV', $student->maHV)
                    ->where('maKH', $course->maKH)
                    ->exists();

                $payload = [
                    'trangThai'    => 'ACTIVE',
                    'activated_at' => $now,
                    'expires_at'   => $expiresAt,
                    'updated_at'   => $now,
                ];

                if ($exists) {
                    DB::table('HOCVIEN_KHOAHOC')
                        ->where('maHV', $student->maHV)
                        ->where('maKH', $course->maKH)
                        ->update($payload);
                } else {
                    DB::table('HOCVIEN_KHOAHOC')->insert(array_merge($payload, [
                        'maHV'        => $student->maHV,
                        'maKH'        => $course->maKH,
                        'ngayNhapHoc' => $now->toDateString(),
                        'created_at'  => $now,
                    ]));
                }
            }
        });
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
