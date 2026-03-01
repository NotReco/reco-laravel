<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Kiểm tra user có role yêu cầu.
     *
     * Sử dụng:
     *   - Route::middleware('role:admin')       → chỉ admin
     *   - Route::middleware('role:moderator')   → moderator + admin
     *   - Route::middleware('role:staff')       → moderator + admin (alias)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Alias: 'staff' = moderator trở lên
        if (in_array('staff', $roles)) {
            if ($user->isStaff()) {
                return $next($request);
            }
        }

        // Kiểm tra hierarchical: nếu role yêu cầu là 'moderator' thì admin cũng pass
        foreach ($roles as $roleName) {
            $requiredRole = UserRole::tryFrom($roleName);
            if ($requiredRole && $user->hasRoleLevel($requiredRole)) {
                return $next($request);
            }
        }

        abort(403, 'Bạn không có quyền truy cập trang này.');
    }
}
