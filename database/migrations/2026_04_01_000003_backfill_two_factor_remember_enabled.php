<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ensure existing users get the intended default behavior:
        // "remember login" is ON by default (can be turned off in settings for demo).
        DB::table('users')
            ->whereNull('two_factor_remember_enabled')
            ->update(['two_factor_remember_enabled' => true]);
    }

    public function down(): void
    {
        // no-op
    }
};

