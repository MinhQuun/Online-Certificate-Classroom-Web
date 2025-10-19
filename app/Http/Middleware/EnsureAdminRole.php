<?php

namespace App\Http\Middleware;

use App\Support\RoleResolver;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EnsureAdminRole
{
    /**
     * Đảm bảo người dùng hiện tại là admin đã xác thực.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->expectsJson()) {
                abort(401, 'Bạn cần đăng nhập.');
            }

            return redirect()
                ->route('login', ['redirect' => $request->fullUrl()])
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        if (!RoleResolver::isAdmin($user)) {
            if ($request->expectsJson()) {
                abort(403, 'Bạn không có quyền truy cập khu vực này.');
            }

            return redirect()
                ->route('student.courses.index')
                ->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        }

        return $next($request);
    }
}