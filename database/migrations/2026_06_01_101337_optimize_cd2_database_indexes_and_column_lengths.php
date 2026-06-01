<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration tối ưu Database — CĐ2 Ngày 4
 *
 * Các thay đổi:
 *  A. Giảm độ dài varchar(255) → 100 cho các cột *_type polymorphic
 *     (viewable_type, interactable_type, target_type) — max data thực tế = 17 chars
 *  B. Giảm search_histories.keyword từ varchar(255) → 100
 *     — max data thực tế = 6 chars; đảm bảo index hoạt động trong utf8mb4
 *  C. Đổi user_interactions.score từ DOUBLE → decimal(5,2)
 *     — giá trị thực tế chỉ là 0, 1, 2; decimal chính xác hơn cho scoring
 *  D. Thêm index view_count trên movies và tv_shows
 *     — RecommendationService, HomeController hay dùng orderByDesc('view_count')
 *  E. Thêm index created_at trên user_interactions
 *     — UserInteractionService filter "10 phút gần đây" dùng created_at
 *  F. Thêm composite index (tv_show_id, status) trên reviews
 *     — Tương đương index (movie_id, status) đã có; TV review listing cần index này
 *
 * Các index polymorphic (viewable_type+id, interactable_type+id, target_type+id)
 * đã được tạo tự động bởi nullableMorphs() — KHÔNG tạo lại.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ════════════════════════════════════════════
        //  A. Giảm *_type columns: varchar(255) → 100
        // ════════════════════════════════════════════

        // view_histories.viewable_type
        // nullableMorphs() tạo index trên (viewable_type, viewable_id) — index này cần được
        // drop rồi re-create sau khi thay đổi column length (MySQL yêu cầu).
        Schema::table('view_histories', function (Blueprint $table) {
            $table->dropIndex('view_histories_viewable_type_viewable_id_index');
        });
        Schema::table('view_histories', function (Blueprint $table) {
            $table->string('viewable_type', 100)->nullable()->change();
            $table->index(['viewable_type', 'viewable_id'], 'view_histories_viewable_type_viewable_id_index');
        });

        // user_interactions.interactable_type
        Schema::table('user_interactions', function (Blueprint $table) {
            $table->dropIndex('user_interactions_interactable_type_interactable_id_index');
        });
        Schema::table('user_interactions', function (Blueprint $table) {
            $table->string('interactable_type', 100)->nullable()->change();
            $table->index(['interactable_type', 'interactable_id'], 'user_interactions_interactable_type_interactable_id_index');
        });

        // activity_logs.target_type
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_target_type_target_id_index');
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('target_type', 100)->nullable()->change();
            $table->index(['target_type', 'target_id'], 'activity_logs_target_type_target_id_index');
        });

        // ════════════════════════════════════════════
        //  B. search_histories.keyword: varchar(255) → 100
        // ════════════════════════════════════════════
        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex('search_histories_keyword_index');
        });
        Schema::table('search_histories', function (Blueprint $table) {
            $table->string('keyword', 100)->change();
            $table->index('keyword', 'search_histories_keyword_index');
        });

        // ════════════════════════════════════════════
        //  C. user_interactions.score: double → decimal(5,2)
        //  Giá trị hiện tại: 0, 1, 2 — an toàn chuyển đổi
        // ════════════════════════════════════════════
        Schema::table('user_interactions', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->default(0)->change();
        });

        // ════════════════════════════════════════════
        //  D. Thêm index view_count (movies, tv_shows)
        // ════════════════════════════════════════════
        Schema::table('movies', function (Blueprint $table) {
            if (!$this->hasIndex('movies', 'movies_view_count_index')) {
                $table->index('view_count', 'movies_view_count_index');
            }
        });

        Schema::table('tv_shows', function (Blueprint $table) {
            if (!$this->hasIndex('tv_shows', 'tv_shows_view_count_index')) {
                $table->index('view_count', 'tv_shows_view_count_index');
            }
        });

        // ════════════════════════════════════════════
        //  E. Thêm index created_at trên user_interactions
        // ════════════════════════════════════════════
        Schema::table('user_interactions', function (Blueprint $table) {
            if (!$this->hasIndex('user_interactions', 'user_interactions_created_at_index')) {
                $table->index('created_at', 'user_interactions_created_at_index');
            }
        });

        // ════════════════════════════════════════════
        //  F. Thêm composite index (tv_show_id, status) trên reviews
        // ════════════════════════════════════════════
        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->hasIndex('reviews', 'reviews_tv_show_id_status_index')) {
                $table->index(['tv_show_id', 'status'], 'reviews_tv_show_id_status_index');
            }
        });
    }

    public function down(): void
    {
        // ── F. Reviews index ──
        Schema::table('reviews', function (Blueprint $table) {
            if ($this->hasIndex('reviews', 'reviews_tv_show_id_status_index')) {
                $table->dropIndex('reviews_tv_show_id_status_index');
            }
        });

        // ── E. user_interactions.created_at index ──
        Schema::table('user_interactions', function (Blueprint $table) {
            if ($this->hasIndex('user_interactions', 'user_interactions_created_at_index')) {
                $table->dropIndex('user_interactions_created_at_index');
            }
        });

        // ── D. view_count indexes ──
        Schema::table('tv_shows', function (Blueprint $table) {
            if ($this->hasIndex('tv_shows', 'tv_shows_view_count_index')) {
                $table->dropIndex('tv_shows_view_count_index');
            }
        });
        Schema::table('movies', function (Blueprint $table) {
            if ($this->hasIndex('movies', 'movies_view_count_index')) {
                $table->dropIndex('movies_view_count_index');
            }
        });

        // ── C. score: decimal → double ──
        Schema::table('user_interactions', function (Blueprint $table) {
            $table->double('score')->default(0)->change();
        });

        // ── B. keyword: 100 → 255 ──
        Schema::table('search_histories', function (Blueprint $table) {
            $table->dropIndex('search_histories_keyword_index');
        });
        Schema::table('search_histories', function (Blueprint $table) {
            $table->string('keyword', 255)->change();
            $table->index('keyword', 'search_histories_keyword_index');
        });

        // ── A. *_type: 100 → 255 ──
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_target_type_target_id_index');
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('target_type', 255)->nullable()->change();
            $table->index(['target_type', 'target_id'], 'activity_logs_target_type_target_id_index');
        });

        Schema::table('user_interactions', function (Blueprint $table) {
            $table->dropIndex('user_interactions_interactable_type_interactable_id_index');
        });
        Schema::table('user_interactions', function (Blueprint $table) {
            $table->string('interactable_type', 255)->nullable()->change();
            $table->index(['interactable_type', 'interactable_id'], 'user_interactions_interactable_type_interactable_id_index');
        });

        Schema::table('view_histories', function (Blueprint $table) {
            $table->dropIndex('view_histories_viewable_type_viewable_id_index');
        });
        Schema::table('view_histories', function (Blueprint $table) {
            $table->string('viewable_type', 255)->nullable()->change();
            $table->index(['viewable_type', 'viewable_id'], 'view_histories_viewable_type_viewable_id_index');
        });
    }

    /**
     * Kiểm tra index có tồn tại trong bảng không.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
