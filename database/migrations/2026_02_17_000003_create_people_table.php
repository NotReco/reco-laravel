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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tmdb_id')->nullable()->unique()->comment('ID gốc từ TMDb API');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('date_of_death')->nullable();
            $table->string('nationality')->nullable();
            $table->string('known_for')->nullable()->comment('Diễn viên, Đạo diễn, Biên kịch...');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot: movie <-> person (cast & crew)
        Schema::create('movie_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->enum('role', ['actor', 'director', 'writer', 'producer']);
            $table->string('character_name')->nullable()->comment('Tên nhân vật cho diễn viên');
            $table->integer('display_order')->default(0);

            $table->index(['movie_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_person');
        Schema::dropIfExists('people');
    }
};
