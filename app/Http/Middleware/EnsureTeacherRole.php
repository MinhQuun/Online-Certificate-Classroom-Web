<?php

namespace App\Http\Middleware;

use App\Support\RoleResolver;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureTeacherRole
{
    /**
     * Ensure the current authenticated user has teacher or admin privileges.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Ban can dang nhap.'], 401);
            }

            return redirect()
                ->route('login', ['redirect' => $request->fullUrl()])
                ->with('error', 'Vui long dang nhap de tiep tuc.');
        }

        $role = RoleResolver::resolve($user);

        if (!in_array($role, ['teacher', 'admin'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Ban khong co quyen truy cap khu vuc nay.'], 403);
            }

            return redirect()
                ->route('student.courses.index')
                ->with('error', 'Khu vuc giang vien chi danh cho nguoi phu hop.');
        }

        return $next($request);
    }
}