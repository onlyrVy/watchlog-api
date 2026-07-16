<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'saved_movie_id',
        'rating',
        'review_text',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function savedMovie(): BelongsTo
    {
        return $this->belongsTo(SavedMovie::class);
    }
}