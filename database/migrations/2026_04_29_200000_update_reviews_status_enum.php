<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chuyển từ mô hình pre-moderation (pending/draft/rejected)
     * sang post-moderation (published/hidden).
     * Tất cả review hiện tại được chuyển thành published.
     */
    public function up(): void
    {
        // 1. Chuyển tất cả status cũ sang 'published'
        DB::table('reviews')
            ->whereIn('status', ['draft', 'pending', 'rejected'])
            ->update(['status' => 'published', 'published_at' => now()]);

        // 2. Thay đổi enum — MySQL cần ALTER COLUMN
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('published', 'hidden') NOT NULL DEFAULT 'published'");
    }

    /**
     * Khôi phục enum cũ nếu cần rollback.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'rejected', 'hidden') NOT NULL DEFAULT 'draft'");
    }
};
