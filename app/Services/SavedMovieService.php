<?php

namespace App\Services;

use App\Models\SavedMovie;
use App\Repositories\SavedMovieRepository;
use Illuminate\Validation\ValidationException;

class SavedMovieService
{
    public function __construct(
        protected SavedMovieRepository $repository
    ) {}

    public function listForUser(int $userId, array $filters = [])
    {
        return $this->repository->all([...$filters, 'user_id' => $userId]);
    }

    public function saveMovie(int $userId, int $tmdbId, string $status): SavedMovie
    {
        $existing = $this->repository->findByUserAndTmdbId($userId, $tmdbId);

        if ($existing) {
            throw ValidationException::withMessages([
                'tmdb_id' => 'This movie is already in your library.',
            ]);
        }

        return $this->repository->create([
            'user_id' => $userId,
            'tmdb_id' => $tmdbId,
            'status' => $status,
        ]);
    }

    public function updateStatus(int $id, string $status): SavedMovie
    {
        return $this->repository->update($id, ['status' => $status]);
    }

    public function remove(int $id): bool
    {
        return $this->repository->delete($id);
    }
}