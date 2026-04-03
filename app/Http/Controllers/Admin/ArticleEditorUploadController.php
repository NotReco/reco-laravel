<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleEditorUploadController extends Controller
{
    private const MAX_VIDEO_BYTES = 50 * 1024 * 1024;

    /**
     * TinyMCE: trả JSON { "location": "https://.../storage/..." }.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $mime = $file->getMimeType() ?: '';

        if (str_starts_with($mime, 'image/')) {
            $request->validate([
                'file' => ['image', 'max:5120'],
            ]);
            $path = $file->store('articles/editor/images', 'public');
        } elseif (in_array($mime, [
            'video/mp4',
            'video/webm',
            'video/quicktime',
            'video/ogg',
        ], true)) {
            if ($file->getSize() > self::MAX_VIDEO_BYTES) {
                return response()->json([
                    'error' => 'Video tối đa 50MB.',
                ], 422);
            }
            $path = $file->store('articles/editor/videos', 'public');
        } else {
            return response()->json([
                'error' => 'Chỉ chấp nhận ảnh (JPEG, PNG, WebP, GIF) hoặc video (MP4, WebM, MOV, OGG).',
            ], 422);
        }

        $url = Storage::disk('public')->url($path);

        // Return relative URL to avoid port issues
        // TinyMCE will resolve it relative to current domain
        if (str_starts_with($url, 'http')) {
            // Extract just the path part (e.g., /storage/articles/...)
            $parsed = parse_url($url);
            $url = $parsed['path'] ?? $url;
        }

        return response()->json(['location' => $url]);
    }
}
