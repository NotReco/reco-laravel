<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumCategoryController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::withCount('threads')->orderBy('order')->paginate(15);
        return view('admin.forum-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.forum-categories.form', ['category' => new ForumCategory()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forum_categories,slug',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->has('is_active');

        ForumCategory::create($validated);

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Chuyên mục đã được thêm thành công.');
    }

    public function edit(ForumCategory $forumCategory)
    {
        return view('admin.forum-categories.form', ['category' => $forumCategory]);
    }

    public function update(Request $request, ForumCategory $forumCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forum_categories,slug,' . $forumCategory->id,
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->has('is_active');

        $forumCategory->update($validated);

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Chuyên mục đã được cập nhật thành công.');
    }

    public function destroy(ForumCategory $forumCategory)
    {
        if ($forumCategory->threads()->exists()) {
            return back()->with('error', 'Không thể xóa chuyên mục đang có bài viết bên trong.');
        }

        $forumCategory->delete();

        return redirect()->route('admin.forum-categories.index')
            ->with('success', 'Chuyên mục đã được xóa thành công.');
    }
}
