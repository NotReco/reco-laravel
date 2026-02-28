<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tmdb_id')->nullable()->unique()->comment('ID gốc từ TMDb API');
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('trailer_url')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('runtime')->nullable()->comment('Thời lượng phút');
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->bigInteger('budget')->nullable()->comment('Ngân sách USD');
            $table->bigInteger('revenue')->nullable()->comment('Doanh thu USD');
            $table->decimal('avg_rating', 3, 1)->default(0.0);
            $table->integer('rating_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_approved')->default(false);
            $table->enum('status', ['active', 'hidden', 'upcoming'])->default('active');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('release_date');
            $table->index('avg_rating');
        });

        // Pivot: movie <-> genre (nhiều-nhiều)
        Schema::create('movie_genre', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['movie_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_genre');
        Schema::dropIfExists('movies');
    }
};
