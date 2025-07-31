<?php

namespace App\Http\Controllers;

use App\Http\Resources\FilmResource;
use App\Models\Genre;
use App\Models\VerifiedFilm;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class GenreController extends Controller
{
    public function __invoke(Genre $genre)
    {

        // dd($genre);
        $genres = Genre::select('name', 'slug')->get();

        // for searching with title or director
        $search = request()->input('search');
        $sortBy = request()->input('sort_by');

        // if sort by value does not match with asc or desc abort
        if ($sortBy && !in_array($sortBy, ['asc', 'desc'])) {
            return Inertia::render('Errors/NotFound')->toResponse(request())->setStatusCode(404);
        }
        // films data
        $films = VerifiedFilm::filterBySearch($search)
            ->filterBySort($sortBy)
            ->filterByGenre($genre)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('GenreFilm', [
            'films' => Inertia::deepMerge(fn() => FilmResource::collection($films->items())), //deepmerge for object/array to be nested deep merge
            'pagination' => Arr::except($films->toArray(), 'data'),
            'search' => $search,
            'sort_by' => $sortBy,
            'genres' => $genres,
            'genre' => $genre->slug,
        ]);
    }
}
