<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with(['user', 'tags'])
            ->withCount('comments')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('admin.articles.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'content'      => ['required', 'string'],
            'thumbnail'    => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'tags'         => ['nullable', 'string'],
        ]);

        $article = Article::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'subtitle'     => $validated['subtitle'] ?? null,
            'content'      => $validated['content'],
            'thumbnail'    => $validated['thumbnail'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        // Sync tags
        $this->syncTags($article, $validated['tags'] ?? '');

        return redirect()->route('admin.articles.index')
            ->with('success', 'Đã tạo bài viết thành công.');
    }

    public function edit(Article $article)
    {
        $article->load('tags');
        $tags = Tag::orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'tags'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'content'      => ['required', 'string'],
            'thumbnail'    => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'tags'         => ['nullable', 'string'],
        ]);

        $wasPublished = $article->is_published;
        $isPublished = $request->boolean('is_published');

        $article->update([
            'title'        => $validated['title'],
            'subtitle'     => $validated['subtitle'] ?? null,
            'content'      => $validated['content'],
            'thumbnail'    => $validated['thumbnail'] ?? null,
            'is_published' => $isPublished,
            'published_at' => $isPublished && !$wasPublished ? now() : $article->published_at,
        ]);

        $this->syncTags($article, $validated['tags'] ?? '');

        return redirect()->route('admin.articles.index')
            ->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Đã xóa bài viết.');
    }

    /**
     * Sync tags từ chuỗi (phân tách bằng dấu phẩy).
     */
    private function syncTags(Article $article, string $tagsString): void
    {
        if (empty(trim($tagsString))) {
            $article->tags()->detach();
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $tagsString)));
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
            $tagIds[] = $tag->id;
        }

        $article->tags()->sync($tagIds);
    }
}
