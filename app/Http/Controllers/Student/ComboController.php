<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Support\Cart\StudentComboCart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->limit(1)
            ->get();

        $enrollment = $this->resolveComboEnrollment();

        return view('Student.combo-index', [
            'search' => $search,
            'availableCombos' => $availableCombos,
            'upcomingCombos' => $upcomingCombos,
            'spotlightCombos' => $spotlightCombos,
            'comboCartIds' => StudentComboCart::ids(),
            'activeComboIds' => $enrollment['activeComboIds'],
            'pendingComboIds' => $enrollment['pendingComboIds'],
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

        $enrollment = $this->resolveComboEnrollment();

        return view('Student.combo-show', [
            'combo' => $combo,
            'isAvailable' => $isAvailable,
            'relatedCombos' => $relatedCombos,
            'comboInCart' => StudentComboCart::has($combo->maGoi),
            'activeComboIds' => $enrollment['activeComboIds'],
            'pendingComboIds' => $enrollment['pendingComboIds'],
        ]);
    }

    /**
     * Resolve combo enrollment status for authenticated student
     */
    private function resolveComboEnrollment(): array
    {
        $result = [
            'isAuthenticated'  => false,
            'student'          => null,
            'activeComboIds'   => [],
            'pendingComboIds'  => [],
        ];

        $userId = Auth::id();

        if (!$userId) {
            return $result;
        }

        $result['isAuthenticated'] = true;

        $student = DB::table('hocvien')->where('maND', $userId)->first();

        if (!$student) {
            return $result;
        }

        $result['student'] = $student;


        $comboEnrollments = DB::table('hocvien_khoahoc')
            ->select('maGoi', 'trangThai')
            ->where('maHV', $student->maHV)
            ->whereNotNull('maGoi')
            ->get();

        $active = [];
        $pending = [];

        foreach ($comboEnrollments as $enrollment) {
            $comboId = (int) $enrollment->maGoi;
            
            if ($enrollment->trangThai === 'ACTIVE') {
                $active[] = $comboId;
            } elseif ($enrollment->trangThai === 'PENDING') {
                $pending[] = $comboId;
            }
        }

        $result['activeComboIds'] = array_unique($active);
        $result['pendingComboIds'] = array_unique($pending);

        return $result;
    }
}
