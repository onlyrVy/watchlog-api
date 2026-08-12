<?php

namespace App\Services;

use App\Models\SavedMovie;
use Illuminate\Support\Carbon;

class StatisticsService
{
    public function __construct(
        protected TmdbService $tmdb
    ) {}

    public function forUser(int $userId): array
    {
        $watchedMovies = SavedMovie::where('user_id', $userId)
            ->where('status', 'watched')
            ->get();

        return [
            'movies_watched' => $watchedMovies->count(),
            'watchlist_size' => SavedMovie::where('user_id', $userId)->where('status', 'watchlist')->count(),
            'average_rating' => $this->averageRating($userId),
            'movies_added_this_month' => SavedMovie::where('user_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'favorite_genre' => $this->favoriteGenre($watchedMovies),
            'most_watched_year' => $this->mostWatchedYear($watchedMovies),
        ];
    }

    protected function averageRating(int $userId): ?float
    {
        $average = SavedMovie::where('saved_movies.user_id', $userId)
            ->join('reviews', 'reviews.saved_movie_id', '=', 'saved_movies.id')
            ->avg('reviews.rating');

        return $average !== null ? round((float) $average, 1) : null;
    }

    protected function favoriteGenre($watchedMovies): ?string
    {
        if ($watchedMovies->isEmpty()) {
            return null;
        }

        $genreCounts = [];
        foreach ($watchedMovies as $saved) {
            try {
                $movie = $this->tmdb->findById($saved->tmdb_id);
                foreach ($movie['genres'] as $genre) {
                    $genreCounts[$genre] = ($genreCounts[$genre] ?? 0) + 1;
                }
            } catch (\RuntimeException $e) {
                // Skip movies TMDb can't currently resolve rather than
                // failing the whole statistics request over one bad ID.
                continue;
            }
        }

        if (empty($genreCounts)) {
            return null;
        }

        arsort($genreCounts);
        return array_key_first($genreCounts);
    }

    protected function mostWatchedYear($watchedMovies): ?string
    {
        if ($watchedMovies->isEmpty()) {
            return null;
        }

        $yearCounts = [];
        foreach ($watchedMovies as $saved) {
            try {
                $movie = $this->tmdb->findById($saved->tmdb_id);
                if ($movie['year']) {
                    $yearCounts[$movie['year']] = ($yearCounts[$movie['year']] ?? 0) + 1;
                }
            } catch (\RuntimeException $e) {
                continue;
            }
        }

        if (empty($yearCounts)) {
            return null;
        }

        arsort($yearCounts);
        return array_key_first($yearCounts);
    }
}