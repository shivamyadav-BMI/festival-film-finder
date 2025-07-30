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

        $query = VerifiedFilm::query();

        // sorting
        $query->when($sortBy ?? false, function ($q) use ($sortBy) {
            //prevents from the any other value allows only asc and dsc
            if (!in_array(strtolower($sortBy), ['asc', 'desc'])) {
                // abort(404);
                return Inertia::render('Errors/NotFound')
                    ->toResponse(request())
                    ->setStatusCode(404);
            }
            $q->orderBy('imdb_rating', $sortBy);
        });

        //searching functionality
        $query->when($search ?? false, function ($q) use ($search) {
            $q->whereAny(['title'], 'LIKE', "%" . $search . "%");
        });

        // filter by gernes
        $query->whereHas('genres', function ($q) use ($genre) {
                $q->where('slug', $genre->slug);
            });

        $films = $query->paginate(10)->withQueryString();
        // dd($films);

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
