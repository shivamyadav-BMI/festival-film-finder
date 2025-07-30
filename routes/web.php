<?php

use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FilmController::class, 'index']);
Route::get('/film/create', [FilmController::class, 'create']);
Route::get('/film/store', [FilmController::class, 'store']);
Route::get('/film/{film}', [FilmController::class, 'show']);
Route::get('/film/genres/{genre:slug}',GenreController::class);

Route::get('/about', function () {
    return Inertia::render('About');
});

// 43 data that does not have both rating except imdb
// total 71 data that either does not have tomatoes rating or metacritic

Route::fallback(function () {
    return Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404);
});
