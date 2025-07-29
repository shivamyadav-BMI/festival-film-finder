<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\VerifiedFilm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $film = VerifiedFilm::where('rotten_tomatoes_rating',Null)->orWhere('metacritic_rating',Null)->count();
        // dd($film);
        // for searching with title or director
        $search = request()->input('search');
        $sortBy = request()->input('sort_by');

        $query = VerifiedFilm::query();

        // sorting
        $query->when($sortBy ?? false, function ($q) use ($sortBy) {
            $q->orderBy('imdb_rating', $sortBy);
        });

        //searching functionality
        $query->when($search ?? false, function ($q) use ($search) {
            $q->whereAny(['title', 'director'], 'LIKE', "%" . $search . "%");
        });

        $films = $query->paginate(10)->withQueryString();

        return Inertia::render('Films/Index', [
            'films' => Inertia::deepMerge(fn() => $films->items()), //deepmerge for object/array to be nested deep merge
            'pagination' => Arr::except($films->toArray(), 'data'),
            'search' => $search,
            'sort_by' => $sortBy
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Films/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $json = storage_path('app/films.json');
        $films = json_decode(file_get_contents($json), true);

        foreach ($films as $film) {
            Film::create([
                'title' => $film['Title'] ?? '',
                'director' => $film['Director'] ?? '',
                'year' => is_numeric($film['Year'] ?? null) ? $film['Year'] : '0000',

                'genres' => $film['Genres'] ?? '',
                'festival_awards' => $film['Festival Awards'] ?? '',
                'imdb_rating' => ($film["IMDb (out of 10)"] === 'NA' || empty($film["IMDb (out of 10)"])) ? 0 : (float) $film["IMDb (out of 10)"],
                'rotten_tomatoes_rating' => ($film["Rotten Tomatoes (out of 100)"] === 'NA' || empty($film["Rotten Tomatoes (out of 100)"])) ? 0 : (float) $film["Rotten Tomatoes (out of 100)"],
                'metacritic_rating' => (
                    !array_key_exists("Metacritic (out of 100)", $film) ||
                    $film["Metacritic (out of 100)"] === 'NA' ||
                    empty($film["Metacritic (out of 100)"])
                ) ? 0 : (float) $film["Metacritic (out of 100)"],

                'plot_summary' => $film['Plot Summary'] ?? '',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VerifiedFilm $film)
    {
        return Inertia::render('Films/Show', [
            'film' => $film
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
