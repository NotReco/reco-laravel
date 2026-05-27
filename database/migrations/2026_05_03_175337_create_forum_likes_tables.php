<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: dùng unsignedInteger thay foreignId() vì DB dùng INT UNSIGNED (MariaDB 5.7)
     */
    public function up(): void
    {
        Schema::create('forum_thread_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('forum_thread_id')->constrained('forum_threads')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'forum_thread_id']);
        });

        Schema::create('forum_reply_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('forum_reply_id')->constrained('forum_replies')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'forum_reply_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_reply_likes');
        Schema::dropIfExists('forum_thread_likes');
    }
};
