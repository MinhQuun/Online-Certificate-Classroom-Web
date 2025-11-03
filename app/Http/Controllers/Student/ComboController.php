<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ComboController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $today = Carbon::today();

        $baseQuery = Combo::query()
            ->with(['promotions'])
            ->withCount('courses')
            ->where('trangThai', '!=', 'ARCHIVED');

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('tenGoi', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('moTa', 'like', "%{$search}%");
            });
        }

        $availableCombos = (clone $baseQuery)
            ->available()
            ->orderByDesc('created_at')
            ->paginate(6)
            ->withQueryString();

        $upcomingCombos = (clone $baseQuery)
            ->where('trangThai', 'PUBLISHED')
            ->whereNotNull('ngayBatDau')
            ->whereDate('ngayBatDau', '>', $today)
            ->orderBy('ngayBatDau')
            ->limit(6)
            ->get();

        $spotlightCombos = (clone $baseQuery)
            ->available()
            ->orderByDesc('rating_avg')
            ->limit(3)
            ->get();

        return view('Student.combo-index', [
            'search' => $search,
            'availableCombos' => $availableCombos,
            'upcomingCombos' => $upcomingCombos,
            'spotlightCombos' => $spotlightCombos,
            'comboCartIds' => StudentComboCart::ids(),
        ]);
    }

    public function show(string $slug): View
    {
        $combo = Combo::with([
                'courses.teacher',
                'promotions',
            ])
            ->where('slug', $slug)
            ->where('trangThai', '!=', 'ARCHIVED')
            ->firstOrFail();

        $isAvailable = $combo->isCurrentlyAvailable();

        $relatedCombos = Combo::available()
            ->where('maGoi', '!=', $combo->maGoi)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        return view('Student.combo-show', [
            'combo' => $combo,
            'isAvailable' => $isAvailable,
            'relatedCombos' => $relatedCombos,
        ]);
    }
}
