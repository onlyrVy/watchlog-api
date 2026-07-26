<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SavedMovieController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MovieController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/{tmdbId}', [MovieController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/saved-movies', [SavedMovieController::class, 'index']);
    Route::post('/saved-movies', [SavedMovieController::class, 'store']);
    Route::put('/saved-movies/{savedMovie}', [SavedMovieController::class, 'update']);
    Route::delete('/saved-movies/{savedMovie}', [SavedMovieController::class, 'destroy']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    
    
});