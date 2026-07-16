<?php

namespace App\Repositories;

use App\Models\SavedMovie;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class SavedMovieRepository implements RepositoryInterface
{
    public function all(array $filters = [])
    {
        $query = SavedMovie::with('review')
            ->where('user_id', $filters['user_id']);

        // Filter by status (Watchlist/Watching/Watched tabs)
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sort: recently_added (default), highest_rated, alphabetical
        // Note: "highest_rated" and "alphabetical" need the review's
        // rating or TMDb's title — title sorting happens client-side
        // in Phase 4 since title isn't stored here (TMDb owns it).
        $sort = $filters['sort'] ?? 'recently_added';
        $query = match ($sort) {
            'highest_rated' => $query->join('reviews', 'reviews.saved_movie_id', '=', 'saved_movies.id')
                ->orderByDesc('reviews.rating')
                ->select('saved_movies.*'),
            default => $query->orderByDesc('created_at'),
        };

        return $query->paginate(20);
    }

    public function find(int $id)
    {
        return SavedMovie::with('review')->findOrFail($id);
    }

    public function create(array $data)
    {
        return SavedMovie::create($data);
    }

    public function update(int $id, array $data)
    {
        $savedMovie = SavedMovie::findOrFail($id);
        $savedMovie->update($data);
        return $savedMovie;
    }

    public function delete(int $id): bool
    {
        return (bool) SavedMovie::destroy($id);
    }

    public function findByUserAndTmdbId(int $userId, int $tmdbId)
    {
        return SavedMovie::where('user_id', $userId)
            ->where('tmdb_id', $tmdbId)
            ->first();
    }
}