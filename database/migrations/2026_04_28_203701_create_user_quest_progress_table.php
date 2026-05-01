<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_quest_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quest_id')->constrained()->cascadeOnDelete();

            // Giá trị tiến độ hiện tại (so sánh với quests.target_value)
            $table->unsignedInteger('progress')->default(0);

            // Thời điểm hoàn thành (null = chưa xong)
            $table->timestamp('completed_at')->nullable();

            // Thời điểm phát thưởng (null = chưa phát)
            $table->timestamp('rewarded_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'quest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_quest_progress');
    }
};
