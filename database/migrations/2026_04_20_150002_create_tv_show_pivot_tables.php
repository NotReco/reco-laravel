<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tv_show_genre')) {
            Schema::create('tv_show_genre', function (Blueprint $table) {
                $table->foreignId('tv_show_id')->constrained('tv_shows')->cascadeOnDelete();
                $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
                $table->primary(['tv_show_id', 'genre_id']);
            });
        }

        if (!Schema::hasTable('tv_show_person')) {
            Schema::create('tv_show_person', function (Blueprint $table) {
                $table->foreignId('tv_show_id')->constrained('tv_shows')->cascadeOnDelete();
                $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
                $table->string('role')->default('actor');
                $table->string('character_name')->nullable();
                $table->unsignedSmallInteger('display_order')->default(0);
                $table->primary(['tv_show_id', 'person_id', 'role']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_show_person');
        Schema::dropIfExists('tv_show_genre');
    }
};
