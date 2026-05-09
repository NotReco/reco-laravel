<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE quests MODIFY COLUMN reward_type ENUM('title', 'frame', 'both') NOT NULL DEFAULT 'title'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting enum changes might cause data truncation if 'both' is used.
        // It's recommended to handle manually if needed, but for simplicity we revert back to ('title', 'frame').
        DB::statement("ALTER TABLE quests MODIFY COLUMN reward_type ENUM('title', 'frame') NOT NULL DEFAULT 'title'");
    }
};
