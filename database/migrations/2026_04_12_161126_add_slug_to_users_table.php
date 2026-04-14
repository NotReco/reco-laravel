<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // First add it as nullable so we can backfill
            $table->string('slug')->nullable()->after('name');
        });

        // Backfill existing users using the same logic as HasSlug
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $slugBase = Str::slug($user->name);
            if (empty($slugBase)) {
                $slugBase = 'user-' . time();
            }

            // Find unique
            $slug = $slugBase;
            $counter = 1;
            while (DB::table('users')->where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                $slug = $slugBase . '-' . $counter;
                $counter++;
            }

            DB::table('users')->where('id', $user->id)->update(['slug' => $slug]);
        }

        // Now make it unique (and implicitly we want it not nullable for the future, but standard soft constraints are fine)
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
