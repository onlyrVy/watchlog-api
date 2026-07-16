<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SavedMovie extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tmdb_id',
        'status',
    ];

    protected $casts = [
        'tmdb_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}