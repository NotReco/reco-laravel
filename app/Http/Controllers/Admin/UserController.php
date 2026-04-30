<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserTitle;
use App\Models\AvatarFrame;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Roles mà staff được phép quản lý.
     * Admin có thể quản lý cả moderator và admin, Mod chỉ quản lý user và tester.
     */
    private const MANAGEABLE_ROLES = ['user', 'tester'];
    private const ADMIN_MANAGEABLE_ROLES = ['user', 'tester', 'moderator', 'admin'];

    /**
     * Trả về danh sách roles được phép chỉnh sửa dựa theo role người đang đăng nhập.
     */
    private function manageableRoles(): array
    {
        return auth()->user()->role->value === 'admin'
            ? self::ADMIN_MANAGEABLE_ROLES
            : self::MANAGEABLE_ROLES;
    }

    /**
     * Danh sách users — chỉ hiển thị user & tester.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount('reviews')
            ->whereIn('role', $this->manageableRoles());

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('role')) {
            // Chỉ cho filter trong phạm vi cho phép
            if (in_array($request->role, $this->manageableRoles())) {
                $query->where('role', $request->role);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Trang chỉnh sửa thành viên.
     */
    public function edit(User $user)
    {
        // Chỉ được chỉnh sửa tài khoản trong phạm vi cho phép
        if (!in_array($user->role->value, $this->manageableRoles())) {
            abort(403, 'Bạn không có quyền chỉnh sửa tài khoản này.');
        }

        $user->load(['titles', 'frames', 'roles']);
        $titles = UserTitle::all();
        $frames = AvatarFrame::all();

        return view('admin.users.edit', compact('user', 'titles', 'frames'));
    }

    /**
     * Cập nhật role, uy tín và kho đồ user.
     */
    public function update(Request $request, User $user)
    {
        // Chỉ được sửa tài khoản trong phạm vi cho phép
        if (!in_array($user->role->value, $this->manageableRoles())) {
            abort(403, 'Bạn không có quyền sửa tài khoản này.');
        }

        $validated = $request->validate([
            'role' => 'required|in:' . implode(',', $this->manageableRoles()),
            'reputation_score' => 'required|integer',
            'titles' => 'nullable|array',
            'titles.*' => 'exists:user_titles,id',
            'frames' => 'nullable|array',
            'frames.*' => 'exists:avatar_frames,id',
        ]);

        $user->update([
            'role'             => $validated['role'],
            'reputation_score' => $validated['reputation_score'],
        ]);

        $user->titles()->sync($request->titles ?? []);
        $user->frames()->sync($request->frames ?? []);

        // Remove active equipment if they no longer own it
        if ($user->active_title_id && !in_array($user->active_title_id, $request->titles ?? [])) {
            $user->update(['active_title_id' => null]);
        }
        if ($user->active_frame_id && !in_array($user->active_frame_id, $request->frames ?? [])) {
            $user->update(['active_frame_id' => null]);
        }

        return redirect()->route('admin.users.index')->with('success', "Đã cập nhật tài khoản của {$user->name}.");
    }

    /**
     * Ban/Unban user.
     */
    public function toggleBan(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể ban chính mình.');
        }

        // Chỉ được ban tài khoản trong phạm vi cho phép
        if (!in_array($user->role->value, $this->manageableRoles())) {
            abort(403, 'Bạn không có quyền khóa tài khoản này.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'khóa';
        return back()->with('success', "Đã {$status} tài khoản {$user->name}.");
    }
}
