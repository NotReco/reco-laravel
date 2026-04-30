<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Stevebauman\Purify\Facades\Purify;

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
        $tags = Tag::whereHas('articles')->orderBy('name')->get();
        return view('admin.articles.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'content'      => ['required', 'string'],
            'thumbnail'    => ['nullable', 'string', 'max:500'],
            'thumbnail_upload' => ['nullable', 'image', 'max:3072', 'mimes:jpeg,jpg,png,webp,gif'],
            'rating_reco'            => ['nullable', 'string', 'regex:/^(10(\.0+)?|[0-9](\.[0-9]+)?)$/'],
            'rating_imdb'            => ['nullable', 'string', 'regex:/^(10(\.0+)?|[0-9](\.[0-9]+)?)$/'],
            'rating_metacritic'      => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])$/'],
            'rating_rotten_tomatoes' => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])%?$/'],
            'rating_tmdb'            => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])%?$/'],
            'is_published' => ['nullable', 'boolean'],
            'tags'         => ['nullable', 'string'],
        ], [
            'rating_reco.regex' => 'Điểm Reco phải từ 0 - 10 (ví dụ: 8.5).',
            'rating_imdb.regex' => 'Điểm IMDb phải từ 0 - 10 (ví dụ: 7.6).',
            'rating_tmdb.regex' => 'Điểm TMDb phải từ 0 - 100 có dấu % (ví dụ: 82%).',
            'rating_metacritic.regex' => 'Điểm Metacritic phải từ 0 - 100 (ví dụ: 80).',
            'rating_rotten_tomatoes.regex' => 'Điểm Rotten Tomatoes phải từ 0 - 100 có hoặc không có dấu % (ví dụ: 93%).',
        ]);

        foreach (['rating_reco', 'rating_imdb', 'rating_metacritic', 'rating_rotten_tomatoes', 'rating_tmdb'] as $rk) {
            $v = isset($validated[$rk]) ? trim((string) $validated[$rk]) : '';
            $validated[$rk] = $v === '' ? null : $v;
        }

        if ($this->isArticleBodyEmpty($validated['content'])) {
            throw ValidationException::withMessages([
                'content' => ['Nội dung bài viết không được để trống.'],
            ]);
        }

        $thumbUrl = trim((string) ($validated['thumbnail'] ?? ''));
        if ($thumbUrl !== '' && filter_var($thumbUrl, FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages([
                'thumbnail' => ['URL ảnh bìa không hợp lệ.'],
            ]);
        }

        $thumbnail = $this->resolveArticleThumbnail(
            $request,
            $thumbUrl === '' ? null : $thumbUrl,
            null
        );

        try {
            $cleanContent = Purify::clean($validated['content']);
        } catch (\Throwable $e) {
            report($e);

            // Bỏ qua lỗi HTMLPurifier (thường do nội dung quá dài hoặc giới hạn bộ nhớ/PCRE)
            // Vì đây là Admin (trusted user), fallback về nội dung gốc và loại bỏ các thành phần độc hại cơ bản.
            $cleanContent = $this->fallbackCleanHtml($validated['content']);
        }

        $article = Article::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'subtitle'     => $validated['subtitle'] ?? null,
            'content'      => $cleanContent,
            'thumbnail'    => $thumbnail,
            'rating_reco'            => $validated['rating_reco'] ?? null,
            'rating_imdb'            => $validated['rating_imdb'] ?? null,
            'rating_metacritic'      => $validated['rating_metacritic'] ?? null,
            'rating_rotten_tomatoes' => $validated['rating_rotten_tomatoes'] ?? null,
            'rating_tmdb'            => $validated['rating_tmdb'] ?? null,
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
        $tags = Tag::whereHas('articles')->orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'tags'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'subtitle'     => ['nullable', 'string', 'max:500'],
            'content'      => ['required', 'string'],
            'thumbnail'    => ['nullable', 'string', 'max:500'],
            'thumbnail_upload' => ['nullable', 'image', 'max:3072', 'mimes:jpeg,jpg,png,webp,gif'],
            'rating_reco'            => ['nullable', 'string', 'regex:/^(10(\.0+)?|[0-9](\.[0-9]+)?)$/'],
            'rating_imdb'            => ['nullable', 'string', 'regex:/^(10(\.0+)?|[0-9](\.[0-9]+)?)$/'],
            'rating_metacritic'      => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])$/'],
            'rating_rotten_tomatoes' => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])%?$/'],
            'rating_tmdb'            => ['nullable', 'string', 'regex:/^(100|[1-9]?[0-9])%?$/'],
            'is_published' => ['nullable', 'boolean'],
            'tags'         => ['nullable', 'string'],
        ], [
            'rating_reco.regex' => 'Điểm Reco phải từ 0 - 10 (ví dụ: 8.5).',
            'rating_imdb.regex' => 'Điểm IMDb phải từ 0 - 10 (ví dụ: 7.6).',
            'rating_tmdb.regex' => 'Điểm TMDb phải từ 0 - 100 có dấu % (ví dụ: 82%).',
            'rating_metacritic.regex' => 'Điểm Metacritic phải từ 0 - 100 (ví dụ: 80).',
            'rating_rotten_tomatoes.regex' => 'Điểm Rotten Tomatoes phải từ 0 - 100 có hoặc không có dấu % (ví dụ: 93%).',
        ]);

        foreach (['rating_reco', 'rating_imdb', 'rating_metacritic', 'rating_rotten_tomatoes', 'rating_tmdb'] as $rk) {
            $v = isset($validated[$rk]) ? trim((string) $validated[$rk]) : '';
            $validated[$rk] = $v === '' ? null : $v;
        }

        if ($this->isArticleBodyEmpty($validated['content'])) {
            throw ValidationException::withMessages([
                'content' => ['Nội dung bài viết không được để trống.'],
            ]);
        }

        $thumbUrl = trim((string) ($validated['thumbnail'] ?? ''));
        if ($thumbUrl !== '' && filter_var($thumbUrl, FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages([
                'thumbnail' => ['URL ảnh bìa không hợp lệ.'],
            ]);
        }

        $thumbnail = $this->resolveArticleThumbnail(
            $request,
            $thumbUrl === '' ? null : $thumbUrl,
            $article->thumbnail
        );

        try {
            $cleanContent = Purify::clean($validated['content']);
        } catch (\Throwable $e) {
            report($e);

            // Bỏ qua lỗi HTMLPurifier cho Admin
            $cleanContent = $this->fallbackCleanHtml($validated['content']);
        }

        $wasPublished = $article->is_published;
        $isPublished = $request->boolean('is_published');

        $article->update([
            'title'        => $validated['title'],
            'subtitle'     => $validated['subtitle'] ?? null,
            'content'      => $cleanContent,
            'thumbnail'    => $thumbnail,
            'rating_reco'            => $validated['rating_reco'] ?? null,
            'rating_imdb'            => $validated['rating_imdb'] ?? null,
            'rating_metacritic'      => $validated['rating_metacritic'] ?? null,
            'rating_rotten_tomatoes' => $validated['rating_rotten_tomatoes'] ?? null,
            'rating_tmdb'            => $validated['rating_tmdb'] ?? null,
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

    /**
     * Ảnh bìa: ưu tiên file upload; không thì URL; để trống cả hai thì null (và xóa file cũ nếu là ảnh đã upload).
     */
    private function resolveArticleThumbnail(Request $request, ?string $urlInput, ?string $previous): ?string
    {
        if ($request->hasFile('thumbnail_upload') && $request->file('thumbnail_upload')->isValid()) {
            $this->deleteStoredArticleThumbnail($previous);

            $path = $request->file('thumbnail_upload')->store('articles/thumbnails', 'public');

            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            return $disk->url($path);
        }

        $url = $urlInput !== null ? trim($urlInput) : '';
        if ($url === '') {
            $this->deleteStoredArticleThumbnail($previous);

            return null;
        }

        if ($previous !== null && $this->isStoredArticleThumbnail($previous) && $url !== $previous) {
            $this->deleteStoredArticleThumbnail($previous);
        }

        return $url;
    }

    private function isStoredArticleThumbnail(?string $thumbnail): bool
    {
        if ($thumbnail === null || $thumbnail === '') {
            return false;
        }

        return (bool) preg_match('#/storage/(articles/thumbnails/.+)$#', $thumbnail, $m);
    }

    private function deleteStoredArticleThumbnail(?string $thumbnail): void
    {
        if (! $this->isStoredArticleThumbnail($thumbnail)) {
            return;
        }
        preg_match('#/storage/(articles/thumbnails/.+)$#', $thumbnail, $m);
        Storage::disk('public')->delete($m[1]);
    }

    /**
     * TinyMCE gửi HTML; coi là rỗng nếu không còn chữ và không có ảnh/video/embed.
     */
    private function isArticleBodyEmpty(string $html): bool
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return true;
        }
        if (preg_match('/<(iframe|img|video|figure|svg|audio|embed|object)\b/i', $trimmed)) {
            return false;
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\x{00A0}/u', ' ', $text);
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        return $text === '';
    }

    /**
     * Tự động làm sạch mã độc (XSS cơ bản) và các thẻ ẩn khi HTMLPurifier bị sập (fallback).
     */
    private function fallbackCleanHtml(string $html): string
    {
        // 1. Gỡ bỏ triệt để các thẻ nguy hiểm (script, style, applet, object, embed, meta, link, base)
        $html = preg_replace('#<(script|style|applet|object|embed|meta|link|base)[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|applet|object|embed|meta|link|base)[^>]*>#is', '', $html);

        // 2. Gỡ bỏ các thuộc tính bắt đầu bằng "on" (chặn mã độc XSS gọi qua sự kiện như onerror, onload...)
        $html = preg_replace('#\s+on[a-z]+\s*=\s*(["\']).*?\1#is', '', $html);
        $html = preg_replace('#\s+on[a-z]+\s*=\s*[^\s>]+#is', '', $html);

        // 3. Chặn mã độc lồng trong javascript: href hoặc src
        $html = preg_replace('#\s+(href|src)\s*=\s*(["\'])\s*javascript:.*?\2#is', ' $1=$2#$2', $html);

        // 4. Nhẹ nhàng dọn dẹp các mã CSS nội tuyến có đặc tính phá vỡ giao diện (position: absolute, fixed)
        // Thay vì xóa toàn bộ thuộc tính style, ta chỉ tìm "position: absolute" và xóa nó đi
        $html = preg_replace('/position\s*:\s*(absolute|fixed)\s*;/i', '', $html);
        
        return $html;
    }
}
