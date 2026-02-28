<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Trait HasSlug
 *
 * Tự động tạo slug có ý nghĩa từ cột nguồn (name hoặc title).
 * Slug được tạo khi model được creating và sẽ đảm bảo tính duy nhất
 * bằng cách thêm hậu tố số nếu trùng lặp.
 *
 * Ví dụ:
 *   - "Avengers: Endgame" → "avengers-endgame"
 *   - "Avengers: Endgame" (trùng) → "avengers-endgame-2"
 *   - "Phim Kinh Dị" → "phim-kinh-di"
 *
 * Sử dụng:
 *   use HasSlug;
 *   protected $slugSource = 'title'; // hoặc 'name' (mặc định)
 */
trait HasSlug
{
    /**
     * Boot trait — đăng ký event tự động tạo slug.
     */
    public static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = $model->generateUniqueSlug();
            }
        });

        static::updating(function ($model) {
            // Nếu cột nguồn thay đổi và slug chưa được thay đổi thủ công,
            // tự động cập nhật slug mới
            $source = $model->getSlugSource();
            if ($model->isDirty($source) && !$model->isDirty('slug')) {
                $model->slug = $model->generateUniqueSlug();
            }
        });
    }

    /**
     * Lấy tên cột nguồn để tạo slug.
     * Có thể override bằng cách khai báo $slugSource trong model.
     */
    public function getSlugSource(): string
    {
        return $this->slugSource ?? 'name';
    }

    /**
     * Tạo slug duy nhất từ cột nguồn.
     * Nếu slug đã tồn tại, thêm hậu tố -2, -3, ...
     */
    public function generateUniqueSlug(): string
    {
        $source = $this->getAttribute($this->getSlugSource());
        $slug = Str::slug($source);

        // Đảm bảo slug không rỗng (trường hợp tên toàn ký tự đặc biệt)
        if (empty($slug)) {
            $slug = Str::slug(class_basename($this)) . '-' . time();
        }

        // Kiểm tra trùng lặp (bỏ qua bản ghi hiện tại nếu đang update)
        $query = static::withTrashed()->where('slug', $slug);
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        if (!$query->exists()) {
            return $slug;
        }

        // Tìm hậu tố lớn nhất đã tồn tại
        $count = static::withTrashed()
            ->where('slug', 'LIKE', $slug . '-%')
            ->orWhere('slug', $slug)
            ->count();

        return $slug . '-' . ($count + 1);
    }

    /**
     * Sử dụng slug cho route model binding
     * Ví dụ: /movies/avengers-endgame thay vì /movies/1
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
