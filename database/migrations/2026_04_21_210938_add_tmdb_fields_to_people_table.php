<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Thêm sau cột 'known_for' nếu có
            $table->tinyInteger('gender')->default(0)->after('known_for')->comment('0=unknown,1=female,2=male,3=non-binary');
            $table->string('place_of_birth')->nullable()->after('gender');
            $table->json('also_known_as')->nullable()->after('place_of_birth');
            $table->string('homepage')->nullable()->after('also_known_as');
            $table->string('imdb_id')->nullable()->after('homepage');
            $table->string('instagram_id')->nullable()->after('imdb_id');
            $table->string('twitter_id')->nullable()->after('instagram_id');
            // biography (longText) – nếu đã có cột 'bio', giữ nguyên
            $table->longText('biography')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'place_of_birth', 'also_known_as',
                'homepage', 'imdb_id', 'instagram_id', 'twitter_id', 'biography',
            ]);
        });
    }
};
