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
        Schema::create('banned_words', function (Blueprint $table) {
            $table->id();
            
            $table->string('word', 100)->unique();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->enum('action', ['pending', 'hide', 'delete'])->default('hide');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['is_active', 'severity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
};
