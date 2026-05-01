<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForumCategoryController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Re-index all categories so order values are 0, 1, 2, ... with no gaps.
     * Call this after insert/update/delete to keep things clean.
     */
    private function reindex(): void
    {
        $categories = ForumCategory::orderBy('order')->orderBy('id')->get();
        foreach ($categories as $i => $cat) {
            if ($cat->order !== $i) {
                $cat->timestamps = false;          // don't touch updated_at
                $cat->order = $i;
                $cat->save();
            }
        }
    }

    /**
     * Shift every category whose order >= $from upward by 1
     * to make room for a new insertion at position $from.
     */
    private function makeRoomAt(int $from, ?int $excludeId = null): void
    {
        $query = ForumCategory::where('order', '>=', $from);
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        // Use DB to avoid mass-assignment overhead & timestamp noise
        DB::table('forum_categories')
            ->where('order', '>=', $from)
            ->when($excludeId !== null, fn($q) => $q->where('id', '!=', $excludeId))
            ->increment('order');
    }

    // ── CRUD ───────────────────────────────────────────────────────────────

    public function index()
    {
        $categories = ForumCategory::withCount('threads')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return view('admin.forum-categories.index', compact('categories'));
    }

    public function create()
    {
        $existingCategories = ForumCategory::orderBy('order')->get();
        $nextOrder = ForumCategory::max('order') + 1; // default = append at end

        return view('admin.forum-categories.form', [
            'category'           => new ForumCategory(),
            'existingCategories' => $existingCategories,
            'nextOrder'          => $nextOrder,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:forum_categories,slug',
            'description' => 'nullable|string',
            'insert_at'   => 'required|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $insertAt = (int) $validated['insert_at'];

        DB::transaction(function () use ($validated, $insertAt, $request) {
            // Push all existing categories at this position and beyond down by 1
            $this->makeRoomAt($insertAt);

            // Insert new category at the exact position
            ForumCategory::create([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'order'       => $insertAt,
                'is_active'   => $request->boolean('is_active', true),
            ]);

            // Reindex to ensure clean sequential values
            $this->reindex();
        });

        return redirect()
            ->route('admin.forum-categories.index')
            ->with('success', 'Chuyên mục đã được thêm thành công.');
    }

    public function edit(ForumCategory $forumCategory)
    {
        $existingCategories = ForumCategory::orderBy('order')
            ->where('id', '!=', $forumCategory->id)
            ->get();

        return view('admin.forum-categories.form', [
            'category'           => $forumCategory,
            'existingCategories' => $existingCategories,
        ]);
    }

    public function update(Request $request, ForumCategory $forumCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:forum_categories,slug,' . $forumCategory->id,
            'description' => 'nullable|string',
            'insert_at'   => 'required|integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $newOrder = (int) $validated['insert_at'];

        DB::transaction(function () use ($validated, $newOrder, $request, $forumCategory) {
            $oldOrder = $forumCategory->order;

            if ($newOrder !== $oldOrder) {
                if ($newOrder > $oldOrder) {
                    // Moving DOWN: shift items between (oldOrder+1 .. newOrder) UP by 1
                    DB::table('forum_categories')
                        ->where('id', '!=', $forumCategory->id)
                        ->where('order', '>', $oldOrder)
                        ->where('order', '<=', $newOrder)
                        ->decrement('order');
                } else {
                    // Moving UP: shift items between (newOrder .. oldOrder-1) DOWN by 1
                    DB::table('forum_categories')
                        ->where('id', '!=', $forumCategory->id)
                        ->where('order', '>=', $newOrder)
                        ->where('order', '<', $oldOrder)
                        ->increment('order');
                }
            }

            $forumCategory->update([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'order'       => $newOrder,
                'is_active'   => $request->boolean('is_active'),
            ]);

            // Reindex to ensure clean sequential values
            $this->reindex();
        });

        return redirect()
            ->route('admin.forum-categories.index')
            ->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ForumCategory $forumCategory)
    {
        if ($forumCategory->threads()->exists()) {
            return back()->with('error', 'Không thể xóa chuyên mục đang có bài viết bên trong.');
        }

        DB::transaction(function () use ($forumCategory) {
            $forumCategory->delete();
            $this->reindex();
        });

        return redirect()
            ->route('admin.forum-categories.index')
            ->with('success', 'Chuyên mục đã được xóa và danh sách đã được sắp xếp lại.');
    }
}
