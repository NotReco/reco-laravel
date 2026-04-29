<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->where('name', '!=', 'Super Admin')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('super.roles.index')->with('success', 'Đã tạo nhóm quyền thành công.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Không thể chỉnh sửa Super Admin.');
        }

        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Không thể chỉnh sửa Super Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('super.roles.index')->with('success', 'Đã cập nhật nhóm quyền thành công.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Không thể xóa Super Admin.');
        }

        $role->delete();
        return redirect()->route('super.roles.index')->with('success', 'Đã xóa nhóm quyền.');
    }
}
