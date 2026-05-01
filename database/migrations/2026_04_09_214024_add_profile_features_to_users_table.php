<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('movie_quote', 255)->nullable();
            $table->integer('reputation_score')->default(0);
            $table->foreignId('active_title_id')->nullable()->constrained('user_titles')->nullOnDelete();
            $table->foreignId('active_frame_id')->nullable()->constrained('avatar_frames')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['active_title_id']);
            $table->dropForeign(['active_frame_id']);
            $table->dropColumn(['movie_quote', 'reputation_score', 'active_title_id', 'active_frame_id']);
        });
    }
};
