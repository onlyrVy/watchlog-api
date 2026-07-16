<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saved_movie_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('review_text')->nullable();
            $table->timestamps();

            // One review per saved movie entry.
            $table->unique('saved_movie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};