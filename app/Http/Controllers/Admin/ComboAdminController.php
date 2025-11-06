<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\Course;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ComboAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Combo::query()->with(['courses.teacher', 'promotions']);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($builder) use ($keyword) {
                $builder->where('tenGoi', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%")
                    ->orWhere('maGoi', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('trangThai', $request->input('status'));
        }

        if ($request->filled('availability')) {
            $availability = $request->input('availability');
            $today = Carbon::today();

            if ($availability === 'active') {
                $query->available();
            } elseif ($availability === 'upcoming') {
                $query->where('trangThai', 'PUBLISHED')
                    ->whereNotNull('ngayBatDau')
                    ->where('ngayBatDau', '>', $today);
            } elseif ($availability === 'expired') {
                $query->where(function ($builder) use ($today) {
                    $builder->whereNull('ngayKetThuc')
                        ->where('trangThai', '!=', 'PUBLISHED')
                        ->orWhere(function ($inner) use ($today) {
                            $inner->whereNotNull('ngayKetThuc')
                                ->where('ngayKetThuc', '<', $today);
                        });
                });
            }
        }

        $combos = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $courses = Course::where('trangThai', 'PUBLISHED')
            ->orderBy('tenKH')
            ->get(['maKH', 'tenKH', 'hocPhi', 'slug']);

        $promotions = Promotion::whereIn('apDungCho', [Promotion::TARGET_COMBO, Promotion::TARGET_BOTH])
            ->orderByDesc('ngayBatDau')
            ->get(['maKM', 'tenKM', 'loaiUuDai', 'giaTriUuDai', 'ngayBatDau', 'ngayKetThuc', 'trangThai', 'apDungCho']);

        $stats = [
            'total' => Combo::count(),
            'published' => Combo::where('trangThai', 'PUBLISHED')->count(),
            'draft' => Combo::where('trangThai', 'DRAFT')->count(),
            'archived' => Combo::where('trangThai', 'ARCHIVED')->count(),
        ];

        return view('Admin.combos', compact('combos', 'courses', 'promotions', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCombo($request);

        $coursePayload = $this->prepareCoursePayload($validated['courses'] ?? []);

        if ($validated['gia'] > $coursePayload['total']) {
            throw ValidationException::withMessages([
                'gia' => 'Giá bán combo phải nhỏ hơn hoặc bằng tổng học phí.',
            ]);
        }

        $selectedPromotion = null;
        if (!empty($validated['promotion_id'])) {
            $selectedPromotion = Promotion::find($validated['promotion_id']);

            if (!$selectedPromotion) {
                throw ValidationException::withMessages([
                    'promotion_id' => 'Khuyến mãi không hợp lệ.',
                ]);
            }
        }

        $promotionPrice = $validated['promotion_price'] ?? null;
        if ($selectedPromotion && $selectedPromotion->loaiUuDai === Promotion::TYPE_GIFT) {
            $promotionPrice = null;
        }

        if ($promotionPrice !== null && $promotionPrice > $validated['gia']) {
            throw ValidationException::withMessages([
                'promotion_price' => 'Giá ưu đãi phải nhỏ hơn hoặc bằng giá combo.',
            ]);
        }

        $name = $validated['tenGoi'];
        $slugInput = $validated['slug'] ?? '';

        $combo = new Combo();
        $combo->tenGoi = $name;
        $combo->slug = $this->generateUniqueSlug(
            $slugInput !== '' ? $slugInput : $name
        );
        $combo->moTa = $validated['moTa'] ?? null;
        $combo->gia = $validated['gia'];
        $combo->giaGoc = $coursePayload['total'];
        $combo->ngayBatDau = $validated['ngayBatDau'] ?? null;
        $combo->ngayKetThuc = $validated['ngayKetThuc'] ?? null;
        $combo->trangThai = $validated['trangThai'];
        $combo->created_by = Auth::id();

        if ($request->hasFile('hinhanh')) {
            $combo->hinhanh = $this->storeComboImage($request);
        }

        $combo->save();

        $combo->courses()->sync($coursePayload['pivot']);

        $this->syncPromotion($combo, $selectedPromotion, $promotionPrice);

        return redirect()
            ->route('admin.combos.index')
            ->with('success', 'Đã tạo combo mới thành công.');
    }

    public function update(Request $request, Combo $combo): RedirectResponse
    {
        $validated = $this->validateCombo($request, $combo);

        $coursePayload = $this->prepareCoursePayload($validated['courses'] ?? []);

        if ($validated['gia'] > $coursePayload['total']) {
            throw ValidationException::withMessages([
                'gia' => 'Giá bán combo phải nhỏ hơn hoặc bằng tổng học phí.',
            ]);
        }

        $selectedPromotion = null;
        if (!empty($validated['promotion_id'])) {
            $selectedPromotion = Promotion::find($validated['promotion_id']);

            if (!$selectedPromotion) {
                throw ValidationException::withMessages([
                    'promotion_id' => 'Khuyến mãi không hợp lệ.',
                ]);
            }
        }

        $promotionPrice = $validated['promotion_price'] ?? null;
        if ($selectedPromotion && $selectedPromotion->loaiUuDai === Promotion::TYPE_GIFT) {
            $promotionPrice = null;
        }

        if ($promotionPrice !== null && $promotionPrice > $validated['gia']) {
            throw ValidationException::withMessages([
                'promotion_price' => 'Giá ưu đãi phải nhỏ hơn hoặc bằng giá combo.',
            ]);
        }

        $name = $validated['tenGoi'];
        $slugInput = $validated['slug'] ?? '';

        $combo->tenGoi = $name;
        $combo->slug = $this->generateUniqueSlug(
            $slugInput !== '' ? $slugInput : $name,
            $combo->maGoi
        );
        $combo->moTa = $validated['moTa'] ?? null;
        $combo->gia = $validated['gia'];
        $combo->giaGoc = $coursePayload['total'];
        $combo->ngayBatDau = $validated['ngayBatDau'] ?? null;
        $combo->ngayKetThuc = $validated['ngayKetThuc'] ?? null;
        $combo->trangThai = $validated['trangThai'];

        if ($request->hasFile('hinhanh')) {
            $combo->hinhanh = $this->storeComboImage($request, $combo);
        }

        $combo->save();

        $combo->courses()->sync($coursePayload['pivot']);

        $this->syncPromotion($combo, $selectedPromotion, $promotionPrice);

        return redirect()
            ->route('admin.combos.index', $request->query())
            ->with('success', 'Đã cập nhật combo thành công.');
    }

    public function destroy(Request $request, Combo $combo): RedirectResponse
    {
        if ($combo->hinhanh) {
            $path = public_path($combo->hinhanh);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        $combo->delete();

        return redirect()
            ->route('admin.combos.index', $request->query())
            ->with('success', 'Đã xoá combo.');
    }

    protected function validateCombo(Request $request, ?Combo $combo = null): array
    {
        $rules = [
            'tenGoi' => ['required', 'string', 'max:150'],
            'slug'   => ['nullable', 'string', 'max:160'],
            'moTa' => ['nullable', 'string', 'max:2000'],
            'gia' => ['required', 'numeric', 'min:0'],
            'ngayBatDau' => ['nullable', 'date'],
            'ngayKetThuc' => ['nullable', 'date', 'after_or_equal:ngayBatDau'],
            'trangThai' => ['required', 'in:DRAFT,PUBLISHED,ARCHIVED'],
            'courses' => ['required', 'array', 'min:2'],
            'courses.*' => ['integer', 'min:1'],
            'promotion_id' => ['nullable', 'integer', 'exists:KHUYEN_MAI,maKM'],
            'promotion_price' => ['nullable', 'numeric', 'min:0'],
        ];

        $messages = [
            'tenGoi.required' => 'Vui lòng nhập tên combo.',
            'gia.required' => 'Vui lòng nhập giá bán combo.',
            'gia.min' => 'Giá bán combo không được âm.',
            'slug.max' => 'Slug tối đa 160 ký tự.',
            'courses.required' => 'Hãy chọn tối thiểu 2 khóa học cho combo.',
            'courses.min' => 'Combo cần ít nhất 2 khóa học.',
            'courses.*.integer' => 'Thứ tự khóa học không hợp lệ.',
            'promotion_id.exists' => 'Khuyến mãi không hợp lệ.',
        ];

        if (!$combo) {
            $rules['hinhanh'] = ['nullable', 'image', 'max:3072'];
        } else {
            $rules['hinhanh'] = ['nullable', 'image', 'max:3072'];
        }

        $validated = $request->validate($rules, $messages);

        $validated['tenGoi'] = trim((string) $validated['tenGoi']);
        $validated['slug'] = trim((string) $request->input('slug', ''));

        $validated['courses'] = $request->input('courses', []);
        $validated['promotion_id'] = $request->input('promotion_id');
        $validated['promotion_price'] = $request->input('promotion_price');

        return $validated;
    }

    protected function prepareCoursePayload(array $coursesInput): array
    {
        if (empty($coursesInput)) {
            throw ValidationException::withMessages([
                'courses' => 'Vui lòng chọn tối thiểu 2 khóa học.',
            ]);
        }

        $courseIds = array_map('intval', array_keys($coursesInput));
        $courseIds = array_values(array_filter($courseIds, fn ($id) => $id > 0));
        $courseIds = array_unique($courseIds);

        if (count($courseIds) < 2) {
            throw ValidationException::withMessages([
                'courses' => 'Combo phải chứa tối thiểu 2 khóa học.',
            ]);
        }

        $courses = Course::whereIn('maKH', $courseIds)
            ->get(['maKH', 'tenKH', 'hocPhi']);

        if ($courses->count() !== count($courseIds)) {
            throw ValidationException::withMessages([
                'courses' => 'Khóa học không hợp lệ hoặc đã bị xoá.',
            ]);
        }

        $timestamp = Carbon::now();

        $pivot = [];
        foreach ($courseIds as $courseId) {
            $order = isset($coursesInput[$courseId])
                ? (int) $coursesInput[$courseId]
                : 1;

            if ($order < 1) {
                $order = 1;
            }

            $pivot[$courseId] = [
                'thuTu' => $order,
                'created_at' => $timestamp,
            ];
        }

        uasort($pivot, fn ($a, $b) => $a['thuTu'] <=> $b['thuTu']);

        $total = (int) round($courses->sum('hocPhi'));

        return [
            'pivot' => $pivot,
            'total' => $total,
        ];
    }

    protected function syncPromotion(Combo $combo, ?Promotion $promotion, $promotionPrice): void
    {
        if (!$promotion) {
            $combo->promotions()->sync([]);

            return;
        }

        if (!in_array($promotion->apDungCho, [Promotion::TARGET_COMBO, Promotion::TARGET_BOTH], true)) {
            throw ValidationException::withMessages([
                'promotion_id' => 'Khuyến mãi không áp dụng cho combo.',
            ]);
        }

        if ($promotion->loaiUuDai === Promotion::TYPE_GIFT) {
            $promotionPrice = null;
        }

        $payload = [
            $promotion->maKM => [
                'giaUuDai' => $promotionPrice !== null ? (int) round($promotionPrice) : null,
                'created_at' => Carbon::now(),
            ],
        ];

        $combo->promotions()->sync($payload);
    }

    protected function storeComboImage(Request $request, ?Combo $combo = null): string
    {
        $file = $request->file('hinhanh');

        $directory = public_path('Assets/Combos');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($combo && $combo->hinhanh) {
            $oldPath = public_path($combo->hinhanh);
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $namePart = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $fileName = time() . '_' . Str::slug($namePart) . '.' . $extension;

        $file->move($directory, $fileName);

        return 'Assets/Combos/' . $fileName;
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $base = $base !== '' ? $base : 'combo';

        $slug = $base;
        $index = 2;

        while (
            Combo::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('maGoi', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }
}
