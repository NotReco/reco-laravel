<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_remember_enabled')
                ->default(true)
                ->after('two_factor_enabled');

            $table->string('two_factor_trusted_token_hash', 64)
                ->nullable()
                ->after('two_factor_remember_enabled');

            $table->timestamp('two_factor_trusted_until')
                ->nullable()
                ->after('two_factor_trusted_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_remember_enabled',
                'two_factor_trusted_token_hash',
                'two_factor_trusted_until',
            ]);
        });
    }
};

