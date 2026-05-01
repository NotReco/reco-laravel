<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cho phép watchlists, favorites, reviews, và vibes hỗ trợ cả TV shows.
 * Thêm tv_show_id nullable vào các bảng liên quan.
 * movie_id sẽ được đổi thành nullable để 1 trong 2 (movie_id hoặc tv_show_id) được set.
 */
return new class extends Migration {
    public function up(): void
    {
        // ── Watchlists ──
        Schema::table('watchlists', function (Blueprint $table) {
            // Thêm tv_show_id FK
            $table->foreignId('tv_show_id')
                ->nullable()
                ->after('movie_id')
                ->constrained('tv_shows')
                ->cascadeOnDelete();

            // Thêm index riêng cho tv_show queries
            $table->index(['user_id', 'tv_show_id']);
        });

        // ── Favorites ──
        Schema::table('favorites', function (Blueprint $table) {
            $table->foreignId('tv_show_id')
                ->nullable()
                ->after('movie_id')
                ->constrained('tv_shows')
                ->cascadeOnDelete();
        });

        // ── Reviews ──
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('tv_show_id')
                ->nullable()
                ->after('movie_id')
                ->constrained('tv_shows')
                ->cascadeOnDelete();
        });

        // ── Vibes (bảng riêng cho tv shows) ──
        Schema::create('tv_show_vibes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained('tv_shows')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mood')->nullable();
            $table->string('tone')->nullable();
            $table->timestamps();

            $table->unique(['tv_show_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_show_vibes');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['tv_show_id']);
            $table->dropColumn('tv_show_id');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['tv_show_id']);
            $table->dropColumn('tv_show_id');
            $table->unique(['user_id', 'movie_id']);
        });

        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'tv_show_id']);
            $table->dropForeign(['tv_show_id']);
            $table->dropColumn('tv_show_id');
            $table->unique(['user_id', 'movie_id']);
        });
    }
};
