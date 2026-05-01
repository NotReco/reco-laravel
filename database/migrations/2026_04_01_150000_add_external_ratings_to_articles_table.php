<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('rating_reco', 32)->nullable()->after('thumbnail');
            $table->string('rating_imdb', 32)->nullable()->after('rating_reco');
            $table->string('rating_metacritic', 32)->nullable()->after('rating_imdb');
            $table->string('rating_rotten_tomatoes', 32)->nullable()->after('rating_metacritic');
            $table->string('rating_tmdb', 32)->nullable()->after('rating_rotten_tomatoes');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'rating_reco',
                'rating_imdb',
                'rating_metacritic',
                'rating_rotten_tomatoes',
                'rating_tmdb',
            ]);
        });
    }
};
