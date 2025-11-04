<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Promotion;
use App\Models\User;
use App\Support\RoleResolver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()->with(['category','teacher','promotions']);

        // Search theo tên/mã
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($x) use ($q) {
                $x->where('tenKH', 'like', "%$q%")
                    ->orWhere('maKH', 'like', "%$q%");
            });
        }

        // Lọc danh mục
        if ($request->filled('category_id')) {
            $query->where('maDanhMuc', $request->category_id);
        }

        // Lọc trạng thái
        if ($request->filled('status')) {
            $query->where('trangThai', $request->status);
        }

        $courses = $query->orderByDesc('created_at')
                            ->paginate(10)
                            ->withQueryString();

        // Danh sách giảng viên theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teachers = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->orderBy('hoTen')
            ->get(['maND','hoTen']);

        $categories = Category::orderBy('tenDanhMuc')->get(['maDanhMuc','tenDanhMuc']);

        $promotions = Promotion::whereIn('apDungCho', [Promotion::TARGET_COURSE, Promotion::TARGET_BOTH])
            ->orderByDesc('ngayBatDau')
            ->get(['maKM','tenKM','loaiUuDai','giaTriUuDai','ngayBatDau','ngayKetThuc','trangThai','apDungCho']);

        return view('admin.courses', compact('courses','teachers','categories','promotions'));
    }

    public function store(Request $request)
    {
        // Lấy danh sách giảng viên hợp lệ theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teacherIds = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->pluck('maND')
            ->toArray();

        $request->validate([
            'tenKH'       => 'required|string|max:150',
            'slug'        => 'nullable|string|max:160',
            'maDanhMuc'   => 'required|exists:danhmuc,maDanhMuc',
            'maND'        => ['required','exists:nguoidung,maND','in:'.implode(',', $teacherIds)],
            'hocPhi'      => 'required|numeric|min:0',
            'moTa'        => 'nullable|string|max:2000',
            'ngayBatDau'  => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh'     => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
            'promotion_id' => ['nullable','integer','exists:KHUYEN_MAI,maKM'],
            'promotion_price' => ['nullable','numeric','min:0'],
        ]);

        $tuition = (float) $request->input('hocPhi');
        [$promotionId, $promotionPrice] = $this->resolvePromotionInputs($request, $tuition);

        $data = $request->except('hinhanh', 'slug', 'promotion_id', 'promotion_price');
        $data['tenKH'] = trim((string) $data['tenKH']);
        $slugInput = trim((string) $request->input('slug', ''));
        $data['slug'] = $this->generateUniqueSlug(
            $slugInput !== '' ? $slugInput : $data['tenKH']
        );
        $data['trangThai'] = 'DRAFT';

        // Upload ảnh -> public/Assets (viết hoa A để khớp accessor)
        if ($request->hasFile('hinhanh')) {
            $file = $request->file('hinhanh');
            $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                        .'.'.$file->getClientOriginalExtension();
            $file->move(public_path('Assets'), $fileName);
            $data['hinhanh'] = 'Assets/'.$fileName; // lưu đường dẫn tương đối
        }

        $course = Course::create($data);
        $this->syncPromotion($course, $promotionId, $promotionPrice);

        // Tạo thư mục trên R2 theo slug tên khoá (không bắt buộc)
        try {
            $directoryName = Str::slug($course->tenKH);
            Storage::disk('r2')->makeDirectory($directoryName);
        } catch (\Exception $e) {
            \Log::error('Lỗi tạo thư mục trên R2: '.$e->getMessage());
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Thêm khóa học thành công');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Lấy danh sách giảng viên hợp lệ theo roles
        $teacherRoleId = RoleResolver::findRoleId(['giang-vien','teacher']);
        $teacherIds = User::whereHas('roles', function ($q) use ($teacherRoleId) {
                $q->where('QUYEN.maQuyen', $teacherRoleId);
            })
            ->pluck('maND')
            ->toArray();

        $request->validate([
            'tenKH'       => 'required|string|max:150',
            'slug'        => 'nullable|string|max:160',
            'maDanhMuc'   => 'required|exists:danhmuc,maDanhMuc',
            'maND'        => ['required','exists:nguoidung,maND','in:'.implode(',', $teacherIds)],
            'hocPhi'      => 'required|numeric|min:0',
            'moTa'        => 'nullable|string|max:2000',
            'ngayBatDau'  => 'nullable|date',
            'ngayKetThuc' => 'nullable|date|after:ngayBatDau',
            'hinhanh'     => 'nullable|image|max:2048',
            'thoiHanNgay' => 'required|integer|min:1',
            'promotion_id' => ['nullable','integer','exists:KHUYEN_MAI,maKM'],
            'promotion_price' => ['nullable','numeric','min:0'],
            'trangThai'   => 'required|in:DRAFT,PUBLISHED,ARCHIVED',
        ]);
        $tuition = (float) $request->input('hocPhi');
        [$promotionId, $promotionPrice] = $this->resolvePromotionInputs($request, $tuition);

        $data = $request->except('hinhanh', 'slug', 'promotion_id', 'promotion_price');
        $data['tenKH'] = trim((string) $data['tenKH']);

        $slugInput = trim((string) $request->input('slug', ''));
        $data['slug'] = $this->generateUniqueSlug(
            $slugInput !== '' ? $slugInput : $data['tenKH'],
            $course->maKH
        );

        // Thay ảnh (nếu có)
        if ($request->hasFile('hinhanh')) {
            if ($course->hinhanh) {
                $old = public_path($course->hinhanh);
                if (file_exists($old)) {
                    @unlink($old);
                }
            }

            $file = $request->file('hinhanh');
            $fileName = time().'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('Assets'), $fileName);
            $data['hinhanh'] = 'Assets/'.$fileName;
        }

        // Cập nhật phần còn lại
        $course->fill($data);

        // Gán rõ ràng trạng thái (đề phòng bị lọc mất ở $fillable sau này)
        $course->trangThai = $request->input('trangThai', $course->trangThai);

        $course->save();
        $this->syncPromotion($course, $promotionId, $promotionPrice);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Cập nhật khóa học thành công');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // Xóa ảnh local
        if ($course->hinhanh) {
            $imagePath = public_path($course->hinhanh);
            if (file_exists($imagePath)) @unlink($imagePath);
        }

        // Xóa thư mục R2 (nếu có)
        try {
            $directoryName = Str::slug($course->tenKH);
            Storage::disk('r2')->deleteDirectory($directoryName);
        } catch (\Exception $e) {
            \Log::error('Lỗi xóa thư mục trên R2: '.$e->getMessage());
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Xóa khóa học thành công');
    }

    protected function resolvePromotionInputs(Request $request, float $tuition): array
    {
        $promotionId = $request->input('promotion_id');
        $rawPrice = $request->input('promotion_price');

        $promotionPrice = null;
        if ($rawPrice !== null && $rawPrice !== '') {
            $promotionPrice = (float) $rawPrice;
        }

        if (!$promotionId && $promotionPrice !== null) {
            throw ValidationException::withMessages([
                'promotion_id' => 'Vui lòng chọn khuyến mãi khi nhập giá ưu đãi.',
            ]);
        }

        if ($promotionPrice !== null && $promotionPrice > $tuition) {
            throw ValidationException::withMessages([
                'promotion_price' => 'Giá ưu đãi phải nhỏ hơn hoặc bằng học phí gốc.',
            ]);
        }

        return [$promotionId ? (int) $promotionId : null, $promotionPrice];
    }

    protected function syncPromotion(Course $course, $promotionId, $promotionPrice): void
    {
        if (!$promotionId) {
            $course->promotions()->sync([]);

            return;
        }

        $promotion = Promotion::find($promotionId);

        if (!$promotion) {
            throw ValidationException::withMessages([
                'promotion_id' => 'Khuyến mãi không hợp lệ.',
            ]);
        }

        if (!in_array($promotion->apDungCho, [Promotion::TARGET_COURSE, Promotion::TARGET_BOTH], true)) {
            throw ValidationException::withMessages([
                'promotion_id' => 'Khuyến mãi không áp dụng cho khóa học.',
            ]);
        }

        $payload = [
            $promotion->maKM => [
                'giaUuDai' => $promotionPrice !== null ? (int) round($promotionPrice) : null,
                'created_at' => Carbon::now(),
            ],
        ];

        $course->promotions()->sync($payload);
    }

    protected function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'khoa-hoc';
        }

        $slug = $base;
        $index = 2;

        while (
            Course::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('maKH', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }
}

