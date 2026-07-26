<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MovieController extends Controller
{
    public function __construct(
        protected TmdbService $tmdb
    ) {}

    public function search(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        try {
            $results = $this->tmdb->search($request->query('query'), (int) $request->query('page', 1));
            return ApiResponseService::success($results);
        } catch (\RuntimeException $e) {
            return ApiResponseService::error('Unable to reach TMDb right now. Please try again.', 503);
        }
    }

    public function show(int $tmdbId)
    {
        try {
            $movie = $this->tmdb->findById($tmdbId);
            return ApiResponseService::success($movie);
        } catch (\RuntimeException $e) {
            return ApiResponseService::error($e->getMessage(), 404);
        }
    }
}