<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quests', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Loại điều kiện
            $table->string('type'); // enum values defined in QuestType

            // Ngưỡng cần đạt (vd: 10 reviews)
            $table->unsignedInteger('target_value')->default(1);

            // Phần thưởng
            $table->enum('reward_type', ['title', 'frame']);
            $table->foreignId('reward_title_id')
                  ->nullable()
                  ->constrained('user_titles')
                  ->nullOnDelete();
            $table->foreignId('reward_frame_id')
                  ->nullable()
                  ->constrained('avatar_frames')
                  ->nullOnDelete();

            $table->boolean('is_active')->default(true);

            // Thứ tự hiển thị
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
