<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Danh sách users — search + filter by role.
     */
    public function index(Request $request)
    {
        $query = User::query()->withCount('reviews');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Cập nhật role user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:' . implode(',', array_column(UserRole::cases(), 'value')),
        ]);

        // Không cho phép thay đổi role của chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi role của chính mình.');
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', "Đã cập nhật role của {$user->name} thành «{$user->role->label()}».");
    }

    /**
     * Ban/Unban user.
     */
    public function toggleBan(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể ban chính mình.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'khóa';
        return back()->with('success', "Đã {$status} tài khoản {$user->name}.");
    }
}
