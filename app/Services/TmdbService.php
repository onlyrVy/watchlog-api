<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TmdbService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
        $this->baseUrl = config('services.tmdb.base_url');
    }

    public function search(string $query, int $page = 1): array
    {
        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
            'page' => $page,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('TMDb search request failed.');
        }

        $data = $response->json();

        return [
            'results' => array_map(fn ($movie) => $this->mapSearchResult($movie), $data['results'] ?? []),
            'page' => $data['page'] ?? 1,
            'total_pages' => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? 0,
        ];
    }

    public function findById(int $tmdbId): array
    {
        // Cache movie details for 6 hours — this data barely changes,
        // and it avoids hammering TMDb every time someone reopens a
        // movie they've already viewed recently.
        return Cache::remember("tmdb_movie_{$tmdbId}", now()->addHours(6), function () use ($tmdbId) {
            $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->status() === 404) {
                throw new \RuntimeException('Movie not found.');
            }

            if ($response->failed()) {
                throw new \RuntimeException('TMDb request failed.');
            }

            return $this->mapDetail($response->json());
        });
    }

    protected function mapSearchResult(array $movie): array
    {
        return [
            'tmdb_id' => $movie['id'],
            'title' => $movie['title'],
            'poster_path' => $movie['poster_path'] ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}" : null,
            'year' => isset($movie['release_date']) && $movie['release_date']
                ? substr($movie['release_date'], 0, 4)
                : null,
            'overview' => $movie['overview'] ?? '',
            'rating' => $movie['vote_average'] ?? 0,
        ];
    }

    protected function mapDetail(array $movie): array
    {
        return [
            'tmdb_id' => $movie['id'],
            'title' => $movie['title'],
            'poster_path' => $movie['poster_path'] ? "https://image.tmdb.org/t/p/w500{$movie['poster_path']}" : null,
            'backdrop_path' => $movie['backdrop_path'] ? "https://image.tmdb.org/t/p/w780{$movie['backdrop_path']}" : null,
            'year' => isset($movie['release_date']) && $movie['release_date']
                ? substr($movie['release_date'], 0, 4)
                : null,
            'genres' => array_map(fn ($g) => $g['name'], $movie['genres'] ?? []),
            'overview' => $movie['overview'] ?? '',
            'runtime' => $movie['runtime'] ?? null,
            'rating' => $movie['vote_average'] ?? 0,
        ];
    }
}