<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PromotionAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::query()->withCount(['combos', 'courses']);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($keyword) {
                $builder->where('tenKM', 'like', "%{$keyword}%")
                    ->orWhere('maKM', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('target')) {
            $target = strtoupper((string) $request->input('target'));
            $query->where('apDungCho', $target);
        }

        if ($request->filled('status')) {
            $query->where('trangThai', strtoupper((string) $request->input('status')));
        }

        if ($request->filled('type')) {
            $query->where('loaiUuDai', strtoupper((string) $request->input('type')));
        }

        $promotions = $query
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Promotion::count(),
            'active' => Promotion::where('trangThai', 'ACTIVE')->count(),
            'inactive' => Promotion::where('trangThai', 'INACTIVE')->count(),
            'expired' => Promotion::where('trangThai', 'EXPIRED')->count(),
        ];

        return view('Admin.promotions', compact('promotions', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePromotion($request);

        $promotion = new Promotion();
        $promotion->fill($data);
        $promotion->created_by = Auth::id();
        $promotion->save();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', '�?A� t���o khuy���n mA�i m��>i thA�nh cAng');
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $this->validatePromotion($request, $promotion->maKM);

        $promotion->fill($data);
        $promotion->save();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', '�?A� c��-p nh��-t khuy���n mA�i thA�nh cAng.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->courses()->detach();
        $promotion->combos()->detach();
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', '�?A� xoA�a khuy���n mA�i.');
    }

    protected function validatePromotion(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'tenKM' => ['required', 'string', 'max:150'],
            'moTa' => ['nullable', 'string', 'max:2000'],
            'apDungCho' => ['required', Rule::in([
                Promotion::TARGET_COMBO,
                Promotion::TARGET_COURSE,
                Promotion::TARGET_BOTH,
            ])],
            'loaiUuDai' => ['required', Rule::in([
                Promotion::TYPE_PERCENT,
                Promotion::TYPE_FIXED,
                Promotion::TYPE_GIFT,
            ])],
            'giaTriUuDai' => ['required', 'numeric', 'min:0'],
            'ngayBatDau' => ['required', 'date'],
            'ngayKetThuc' => ['required', 'date', 'after_or_equal:ngayBatDau'],
            'soLuongGioiHan' => ['nullable', 'integer', 'min:1'],
            'trangThai' => ['required', Rule::in(['ACTIVE', 'INACTIVE', 'EXPIRED'])],
        ]);

        if ($validated['loaiUuDai'] === Promotion::TYPE_PERCENT) {
            if ($validated['giaTriUuDai'] <= 0 || $validated['giaTriUuDai'] > 100) {
                throw ValidationException::withMessages([
                    'giaTriUuDai' => 'GiA� t��� ph���n trA?m ph���i trong khoA?ng 1 - 100.',
                ]);
            }
        }

        if ($validated['loaiUuDai'] === Promotion::TYPE_FIXED && $validated['giaTriUuDai'] <= 0) {
            throw ValidationException::withMessages([
                'giaTriUuDai' => 'GiA� tiA�n khuy���n mA�i ph���i lA�n h��n 0.',
            ]);
        }

        if ($validated['loaiUuDai'] === Promotion::TYPE_GIFT) {
            $validated['giaTriUuDai'] = 0;
        }

        if ($validated['soLuongGioiHan'] === null || $validated['soLuongGioiHan'] === '') {
            $validated['soLuongGioiHan'] = null;
        }

        $validated['giaTriUuDai'] = round((float) $validated['giaTriUuDai'], 2);
        $validated['apDungCho'] = strtoupper($validated['apDungCho']);
        $validated['loaiUuDai'] = strtoupper($validated['loaiUuDai']);
        $validated['trangThai'] = strtoupper($validated['trangThai']);

        return $validated;
    }
}
