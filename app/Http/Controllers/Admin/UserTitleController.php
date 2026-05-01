<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserTitle;
use Illuminate\Http\Request;

class UserTitleController extends Controller
{
    public function index()
    {
        $titles = UserTitle::latest()->paginate(20);
        return view('admin.user_titles.index', compact('titles'));
    }

    public function create()
    {
        return view('admin.user_titles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_hex' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');

        UserTitle::create($validated);

        return redirect()->route('admin.user-titles.index')
            ->with('success', 'Đã thêm Danh hiệu mới thành công.');
    }

    public function edit(string $id)
    {
        $userTitle = UserTitle::findOrFail($id);
        return view('admin.user_titles.create', compact('userTitle'));
    }

    public function update(Request $request, string $id)
    {
        $userTitle = UserTitle::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color_hex' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->has('is_active');

        $userTitle->update($validated);

        return redirect()->route('admin.user-titles.index')
            ->with('success', 'Đã cập nhật Danh hiệu thành công.');
    }

    public function destroy(string $id)
    {
        UserTitle::findOrFail($id)->delete();
        return redirect()->route('admin.user-titles.index')
            ->with('success', 'Đã xóa Danh hiệu thành công.');
    }
}
