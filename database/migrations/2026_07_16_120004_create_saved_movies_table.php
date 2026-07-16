<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('tmdb_id');
            $table->enum('status', ['watchlist', 'watching', 'watched'])->default('watchlist');
            $table->timestamps();

            // Prevents the same user from saving the same movie twice —
            // enforced at the DB level, not just checked in application code.
            $table->unique(['user_id', 'tmdb_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_movies');
    }
};