<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tv_shows', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tmdb_id')->nullable()->unique()->comment('ID từ TMDb API');
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('trailer_url')->nullable();

            // Ngày phát sóng
            $table->date('first_air_date')->nullable()->comment('Ngày phát sóng đầu tiên');
            $table->date('last_air_date')->nullable()->comment('Ngày phát sóng cuối cùng');

            // Thông số series
            $table->unsignedSmallInteger('number_of_seasons')->nullable();
            $table->unsignedSmallInteger('number_of_episodes')->nullable();
            $table->unsignedSmallInteger('episode_runtime')->nullable()->comment('Thời lượng mỗi tập (phút)');

            // Kênh/nền tảng phát sóng (JSON array: [{id, name, logo_path}])
            $table->json('networks')->nullable()->comment('Danh sách networks phát sóng');

            // Loại series
            $table->string('type')->nullable()->comment('Scripted, Reality, Documentary, News, Talk Show...');

            // Trạng thái từ TMDb: Returning Series, Ended, Cancelled, In Production...
            $table->string('tmdb_status')->nullable();

            $table->string('country')->nullable();
            $table->string('language')->nullable();

            // Rating & stats
            $table->decimal('avg_rating', 3, 1)->default(0.0);
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);

            $table->boolean('is_approved')->default(false);
            $table->enum('status', ['active', 'hidden', 'upcoming'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->unsignedTinyInteger('featured_order')->nullable();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('first_air_date');
            $table->index('avg_rating');
            $table->index('status');
        });

        // Pivot: tv_show <-> genre
        Schema::create('tv_show_genre', function (Blueprint $table) {
            $table->foreignId('tv_show_id')->constrained('tv_shows')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->primary(['tv_show_id', 'genre_id']);
        });

        // Pivot: tv_show <-> person (diễn viên, đạo diễn...)
        Schema::create('tv_show_person', function (Blueprint $table) {
            $table->foreignId('tv_show_id')->constrained('tv_shows')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('role')->default('actor')->comment('actor, director, writer, producer');
            $table->string('character_name')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->primary(['tv_show_id', 'person_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_show_person');
        Schema::dropIfExists('tv_show_genre');
        Schema::dropIfExists('tv_shows');
    }
};
