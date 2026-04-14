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
        $tables = ['comments', 'article_comments', 'forum_replies'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Add nullable uuid first
                $table->uuid('uuid')->nullable()->after('id');
            });

            // Backfill
            $records = DB::table($tableName)->get();
            foreach ($records as $record) {
                DB::table($tableName)->where('id', $record->id)->update(['uuid' => (string) Str::uuid()]);
            }

            // Make unique and not nullable
            Schema::table($tableName, function (Blueprint $table) {
                $table->uuid('uuid')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['comments', 'article_comments', 'forum_replies'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique([$tableName . '_uuid_unique'] ?? ['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
