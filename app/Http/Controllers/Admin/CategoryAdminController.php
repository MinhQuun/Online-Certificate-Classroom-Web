<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $categoriesQuery = Category::query()
            ->withCount([
                'courses',
                'courses as published_courses_count' => function ($query) {
                    $query->published();
                },
            ])
            ->orderBy('tenDanhMuc');

        if ($q !== '') {
            $categoriesQuery->where(function ($builder) use ($q) {
                $builder->where('tenDanhMuc', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%");
            });
        }

        $categories = $categoriesQuery
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'totalCategories'  => Category::count(),
            'totalCourses'     => Course::count(),
            'publishedCourses' => Course::published()->count(),
        ];

        return view('admin.categories', compact('categories', 'q', 'summary'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'tenDanhMuc' => ['required', 'string', 'max:100'],
                'slug'       => ['nullable', 'string', 'max:120'],
                'icon'       => ['nullable', 'string', 'max:100'],
            ],
            [
                'tenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
                'tenDanhMuc.max'      => 'Tên danh mục tối đa 100 ký tự.',
                'slug.max'            => 'Slug tối đa 120 ký tự.',
                'icon.max'            => 'Icon tối đa 100 ký tự.',
            ]
        );

        $name = trim($validated['tenDanhMuc']);
        $slugInput = $validated['slug'] ?? '';

        $slug = $this->uniqueSlug($slugInput !== '' ? $slugInput : $name, null);

        $category = Category::create([
            'tenDanhMuc' => $name,
            'slug'       => $slug,
            'icon'       => $this->normalizeIcon($validated['icon'] ?? null),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "Đã tạo danh mục {$category->tenDanhMuc}.");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate(
            [
                'tenDanhMuc' => ['required', 'string', 'max:100'],
                'slug'       => ['nullable', 'string', 'max:120'],
                'icon'       => ['nullable', 'string', 'max:100'],
            ],
            [
                'tenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
                'tenDanhMuc.max'      => 'Tên danh mục tối đa 100 ký tự.',
                'slug.max'            => 'Slug tối đa 120 ký tự.',
                'icon.max'            => 'Icon tối đa 100 ký tự.',
            ]
        );

        $name = trim($validated['tenDanhMuc']);
        $slugInput = $validated['slug'] ?? '';

        $payload = [
            'tenDanhMuc' => $name,
            'slug'       => $this->uniqueSlug(
                $slugInput !== '' ? $slugInput : $name,
                (int) $category->getKey()
            ),
            'icon'       => $this->normalizeIcon($validated['icon'] ?? null),
        ];

        $category->update($payload);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroy(Category $category)
    {
        if ($category->courses()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục khi còn khóa học.');
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Đã xóa danh mục.');
    }

    private function uniqueSlug(?string $value, ?int $ignoreId): string
    {
        $base = Str::slug((string) $value);
        if ($base === '') {
            $base = 'danh-muc';
        }

        $unique = $base;
        $suffix = 2;

        while (
            Category::where('slug', $unique)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('maDanhMuc', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $unique = $base . '-' . $suffix;
            $suffix++;
        }

        return $unique;
    }

    private function normalizeIcon(?string $icon): ?string
    {
        $icon = trim((string) $icon);
        return $icon === '' ? null : $icon;
    }
}