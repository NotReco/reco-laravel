<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Danh sách tin tức (published).
     */
    public function index(Request $request)
    {
        $query = Article::published()
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->orderByDesc('published_at');

        // Lọc theo tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        $articles = $query->paginate(12)->withQueryString();

        $tags = Tag::whereHas('articles', function ($q) {
            $q->published();
        })->get();

        $activeTag = $request->input('tag');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('news.partials.article_list', compact('articles'))->render()
            ]);
        }

        return view('news.index', compact('articles', 'tags', 'activeTag'));
    }

    /**
     * Chi tiết bài viết.
     */
    public function show(Article $article)
    {
        // Chỉ cho xem bài đã published (hoặc staff)
        if (!$article->is_published && (!auth()->check() || !auth()->user()->isStaff())) {
            abort(404);
        }

        // Tăng lượt xem
        $article->increment('views_count');

        $article->load(['user', 'tags', 'comments' => function ($q) {
            $q->whereNull('parent_id')
              ->with(['user', 'replies.user'])
              ->orderByDesc('created_at');
        }]);

        $commentsCount = $article->comments()->count();

        return view('news.show', compact('article', 'commentsCount'));
    }
}
