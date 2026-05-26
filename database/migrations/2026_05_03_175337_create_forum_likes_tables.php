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
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('forum_thread_id');
            $table->timestamps();

            $table->unique(['user_id', 'forum_thread_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('forum_thread_id')->references('id')->on('forum_threads')->cascadeOnDelete();
        });

        Schema::create('forum_reply_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('forum_reply_id');
            $table->timestamps();

            $table->unique(['user_id', 'forum_reply_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('forum_reply_id')->references('id')->on('forum_replies')->cascadeOnDelete();
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
