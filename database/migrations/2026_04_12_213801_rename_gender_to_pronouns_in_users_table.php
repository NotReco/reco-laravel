<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new pronouns column
        Schema::table('users', function (Blueprint $table) {
            $table->string('pronouns', 100)->nullable()->after('gender');
        });

        // Step 2: Migrate existing gender data to pronouns
        DB::table('users')->where('gender', 'male')->update(['pronouns' => 'Anh ấy/He']);
        DB::table('users')->where('gender', 'female')->update(['pronouns' => 'Cô ấy/She']);
        DB::table('users')->where('gender', 'other')->update(['pronouns' => 'Họ/They']);

        // Step 3: Drop old gender column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('bio');
        });

        DB::table('users')->where('pronouns', 'Anh ấy/He')->update(['gender' => 'male']);
        DB::table('users')->where('pronouns', 'Cô ấy/She')->update(['gender' => 'female']);
        DB::table('users')->whereNotNull('pronouns')->whereNull('gender')->update(['gender' => 'other']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('pronouns');
        });
    }
};
