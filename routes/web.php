<?php

use App\Http\Controllers\FilmController;
use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

Route::get('/', [FilmController::class, 'index']);
Route::get('/film/create', [FilmController::class, 'create']);
Route::get('/film/store', [FilmController::class, 'store']);
Route::get('/film/{film}', [FilmController::class, 'show']);
Route::get('/film/genres/{genre:slug}', GenreController::class);

Route::get('/about', function () {
    return Inertia::render('About');
});

// 43 data that does not have both rating except imdb
// total 71 data that either does not have tomatoes rating or metacritic

Route::fallback(function () {
    return Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404);
});


Route::get('/testing', function () {
    $response = Http::withToken('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')
        ->post('https://api.openai.com/v1/chat/completions', [
'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => 'Give me a random fun fact.'],
            ],
        ]);

    Log::info('OpenAI response:', $response->json()); // log the full response

    return response()->json($response->json()); // show entire response for debugging
});
