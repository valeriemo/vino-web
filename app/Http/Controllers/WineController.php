<?php

namespace App\Http\Controllers;

use App\Models\CellarHasWine;
use App\Models\Wine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wines = Wine::select()->paginate(24);
        return Inertia::render('Wine/WineTest', compact('wines'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cellars = Auth::user()->cellar;
        return Inertia::render('Wine/CreateView', compact('cellars'));
    }

    public function searchResult(Request $request)
    {
        $search = $request->search;
        $count = Wine::like('name', $search)->count();
        $results = Wine::like('name', $search)->get();
        $cellars = Auth::user()->cellar;
        return Inertia::render('Wine/SearchView', compact('results', 'search', 'cellars', 'count'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'min:3 | max:100',
            'type' => 'min:3 | max:45 | nullable',
            'country' => 'min:3 | max:100 | nullable',
            'size' => 'min:3 | max:45 | nullable',
            'price' => 'numeric | gte:0 | nullable'
        ]);
        return $request;
        $wine = Wine::create([
            'name' => $request->name,
            'type' => $request->type,
            'country' => $request->country,
            'size' => $request->size,
            'price' => $request->price,
        ]);
        return redirect(route('wine.show', $wine));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function show(Wine $wine)
    {
        return Inertia::render('Wine/ShowView', compact('wine'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function edit(Wine $wine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wine $wine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wine $wine)
    {
        //
    }
}
