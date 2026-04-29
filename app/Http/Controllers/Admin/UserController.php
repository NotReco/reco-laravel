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
     * Roles mà staff được phép quản lý (không bao gồm moderator/admin).
     */
    private const MANAGEABLE_ROLES = ['user', 'tester'];

    /**
     * Danh sách users — chỉ hiển thị user & tester.
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->withCount('reviews')
            ->whereIn('role', self::MANAGEABLE_ROLES);

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('role')) {
            // Chỉ cho filter trong phạm vi cho phép
            if (in_array($request->role, self::MANAGEABLE_ROLES)) {
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
        // Staff không được chỉnh sửa tài khoản có quyền cao hơn
        if (!in_array($user->role->value, self::MANAGEABLE_ROLES)) {
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
        // Staff không được sửa tài khoản privileged
        if (!in_array($user->role->value, self::MANAGEABLE_ROLES)) {
            abort(403, 'Bạn không có quyền sửa tài khoản này.');
        }

        $validated = $request->validate([
            // Staff chỉ được đổi role trong phạm vi user/tester
            'role' => 'required|in:' . implode(',', self::MANAGEABLE_ROLES),
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

        // Staff không được ban tài khoản privileged
        if (!in_array($user->role->value, self::MANAGEABLE_ROLES)) {
            abort(403, 'Bạn không có quyền khóa tài khoản này.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'khóa';
        return back()->with('success', "Đã {$status} tài khoản {$user->name}.");
    }
}
