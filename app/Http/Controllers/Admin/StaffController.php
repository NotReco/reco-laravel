<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Quản lý tài khoản Staff & Admin — chỉ Super Admin mới truy cập được.
 */
class StaffController extends Controller
{
    /** Roles thuộc phạm vi quản lý của panel này */
    private const STAFF_ROLES = ['moderator', 'admin'];

    public function index(Request $request)
    {
        $query = User::query()
            ->withCount(['reviews'])
            ->whereIn('role', self::STAFF_ROLES);

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('role') && in_array($request->role, self::STAFF_ROLES)) {
            $query->where('role', $request->role);
        }

        $staffAccounts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $spatieRoles = \Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get();

        return view('super.staff.index', compact('staffAccounts', 'spatieRoles'));
    }

    public function create()
    {
        $spatieRoles = \Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get();

        return view('super.staff.create', compact('spatieRoles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'        => 'required|in:moderator,admin',
            'spatie_roles' => 'nullable|array',
            'spatie_roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        if (!empty($validated['spatie_roles'])) {
            $user->syncRoles($validated['spatie_roles']);
        }

        return redirect()
            ->route('super.staff.index')
            ->with('success', "Đã tạo tài khoản {$user->name} thành công.");
    }

    public function edit(User $user)
    {
        // Chỉ cho edit moderator/admin
        if (!in_array($user->role->value, self::STAFF_ROLES)) {
            abort(403, 'Tài khoản này không thuộc phạm vi quản lý.');
        }

        // Không cho sửa chính mình tại đây (tránh lock-out)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vui lòng dùng trang hồ sơ để chỉnh sửa thông tin của chính mình.');
        }

        $user->load('roles');
        $spatieRoles = \Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')
            ->orderBy('name')
            ->get();

        return view('super.staff.create', compact('user', 'spatieRoles'));
    }

    public function update(Request $request, User $user)
    {
        if (!in_array($user->role->value, self::STAFF_ROLES)) {
            abort(403, 'Tài khoản này không thuộc phạm vi quản lý.');
        }

        if ($user->id === auth()->id()) {
            abort(403, 'Không thể tự sửa tài khoản của mình tại đây.');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'role'         => 'required|in:moderator,admin',
            'spatie_roles' => 'nullable|array',
            'spatie_roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        $user->syncRoles($request->spatie_roles ?? []);

        return redirect()
            ->route('super.staff.index')
            ->with('success', "Đã cập nhật tài khoản {$user->name}.");
    }

    /**
     * Đặt lại mật khẩu cho staff.
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!in_array($user->role->value, self::STAFF_ROLES)) {
            abort(403);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', "Đã đặt lại mật khẩu cho {$user->name}.");
    }

    /**
     * Kích hoạt / Khoá tài khoản staff.
     */
    public function toggleBan(User $user)
    {
        if (!in_array($user->role->value, self::STAFF_ROLES)) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể khóa tài khoản của chính mình.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'kích hoạt' : 'khóa';
        return back()->with('success', "Đã {$status} tài khoản {$user->name}.");
    }
}
