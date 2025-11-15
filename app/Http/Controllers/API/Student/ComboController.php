<?php

namespace App\Http\Controllers\API\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComboController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 30));

        $query = Combo::query()
            ->available()
            ->with(['promotions', 'courses' => function ($courseQuery) {
                $courseQuery->select(
                    'khoahoc.maKH',
                    'khoahoc.tenKH',
                    'khoahoc.hocPhi',
                    'khoahoc.slug',
                    'khoahoc.maND',
                    'khoahoc.maDanhMuc',

                    // *** BẮT BUỘC THÊM CÁC CỘT PIVOT ***
                    // Cột 'maGoi' để Laravel liên kết lại với Combo
                    'goi_khoa_hoc_chitiet.maGoi',
                    // Cột 'thuTu' từ withPivot và orderByPivot
                    'goi_khoa_hoc_chitiet.thuTu',
                    // Cột 'created_at' từ withPivot
                    'goi_khoa_hoc_chitiet.created_at'
                );
            }])
            ->withCount('courses')
            ->orderByDesc('maGoi');

        if ($search = $request->query('search')) {
            $normalized = Str::of($search)->lower()->toString();
            $query->where(function ($builder) use ($normalized) {
                $builder
                    ->whereRaw('LOWER(tenGoi) LIKE ?', ["%{$normalized}%"])
                    ->orWhereRaw('LOWER(moTa) LIKE ?', ["%{$normalized}%"]);
            });
        }

        $paginator = $query->paginate($perPage); // Lỗi xảy ra khi thực thi dòng này
        $items = $paginator->getCollection()
            ->map(fn (Combo $combo) => $this->transformCombo($combo))
            ->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy danh sách gói khóa học thành công.',
            'data'    => [
                'items' => $items,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'last_page'    => $paginator->lastPage(),
                ],
            ],
        ]);
    }

    public function show(int $comboId): JsonResponse
    {
        $combo = Combo::query()
            ->with([
                'courses' => function ($courseQuery) {
                    $courseQuery
                    ->select(
                        'khoahoc.maKH',
                        'khoahoc.tenKH',
                        'khoahoc.slug',
                        'khoahoc.hocPhi',
                        'khoahoc.maND',
                        'khoahoc.maDanhMuc',
                        'khoahoc.thoiHanNgay',

                        // *** BẮT BUỘC THÊM CÁC CỘT PIVOT ***
                        // Cột 'maGoi' để Laravel liên kết lại với Combo
                        'goi_khoa_hoc_chitiet.maGoi',
                        // Cột 'thuTu' từ withPivot và orderByPivot
                        'goi_khoa_hoc_chitiet.thuTu',
                        // Cột 'created_at' từ withPivot
                        'goi_khoa_hoc_chitiet.created_at'
                    )
                        ->with([
                            'teacher:maND,hoTen',
                            'category:maDanhMuc,tenDanhMuc',
                        ])
                        ->withCount('lessons');
                },
                'promotions',
            ])
            ->findOrFail($comboId);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lấy chi tiết gói khóa học thành công.',
            'data'    => $this->transformCombo($combo, true),
        ]);
    }

    protected function transformCombo(Combo $combo, bool $withCourses = false): array
    {
        return [
            'id'              => $combo->maGoi,
            'name'            => $combo->tenGoi,
            'slug'            => $combo->slug,
            'description'     => $combo->moTa,
            'cover_image'     => $combo->cover_image_url,
            'price'           => [
                'sale'     => $combo->sale_price,
                'original' => $combo->original_price,
                'saving'   => $combo->saving_amount,
                'saving_percent' => $combo->saving_percent,
                'currency' => 'VND',
            ],
            'status'          => $combo->trangThai,
            'active'          => (bool) $combo->is_active,
            'rating'          => [
                'average' => $combo->rating_avg,
                'count'   => $combo->rating_count,
            ],
            'available_from'  => optional($combo->ngayBatDau)->format('Y-m-d'),
            'available_until' => optional($combo->ngayKetThuc)->format('Y-m-d'),
            'courses_count'   => $combo->courses_count ?? $combo->courses->count(),
            'promotion'       => $combo->active_promotion ? [
                'id'        => $combo->active_promotion->maKM,
                'name'      => $combo->active_promotion->tenKM ?? null,
                'type'      => $combo->active_promotion->loaiUuDai,
                'value'     => $combo->active_promotion->giaTriUuDai,
                'expired_at'=> optional($combo->active_promotion->ngayKetThuc)->format('Y-m-d'),
            ] : null,
            'courses'         => $withCourses
                ? $combo->courses->map(function ($course) {
                    return [
                        'id'        => $course->maKH,
                        'title'     => $course->tenKH,
                        'slug'      => $course->slug,
                        'price'     => (int) $course->hocPhi,
                        'duration_days' => $course->thoiHanNgay,
                        'lessons_count' => $course->lessons_count ?? $course->lessons?->count(),
                        'teacher'   => $course->teacher ? [
                            'id'   => $course->teacher->maND,
                            'name' => $course->teacher->hoTen,
                        ] : null,
                        'category'  => $course->category ? [
                            'id'   => $course->category->maDanhMuc,
                            'name' => $course->category->tenDanhMuc,
                        ] : null,
                    ];
                })->values()
                : null,
        ];
    }
}
