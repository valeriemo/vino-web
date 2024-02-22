<?php

namespace App\Http\Controllers;

use App\Models\CellarHasWine;
use App\Models\BuyList;
use App\Models\Wine;
use Illuminate\Http\Request;
use App\Http\Resources\WineResource;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WineController extends Controller
{
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

    /**
     * Display a listing of the resource.
     */
    public function searchResult(Request $request)
    {
        $request->validate([
            'search' => 'min:2'
        ]);
        $search = $request->search;
        $count = Wine::like('name', $search)->where('user_id', null)->count();
        $results = WineResource::collection(Wine::like('name', $search)
            ->where('user_id', null)
            ->limit(1000)
            ->get()
        )->resolve();
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
            'name' => 'min:3|max:100',
            'type' => 'min:3|max:45|nullable',
            'country' => 'min:3|max:100|nullable',
            'size' => 'min:3|max:45|nullable',
            'price' => 'numeric|gte:0|nullable',
            'cellar_id' => Rule::exists('cellars', 'id')->where(function ($query) {
                return $query->where('user_id', Auth::id());
            }),
            'cellar_qty' => Rule::requiredIf(!$request->buyList_qty),
            'buyList_qty' => Rule::requiredIf(!$request->cellar_qty)
        ]);
        $type = json_encode(['fr' => $request->type]);
        $country = json_encode(['fr' => $request->country]);
        $wine = Wine::create([
            'name' => $request->name,
            'type' => $type,
            'country' => $country,
            'size' => $request->size,
            'price' => $request->price,
            'user_id' => Auth::id()
        ]);
        if($request->cellar_qty) {
            CellarHasWine::create([
                'wine_id' => $wine->id,
                'cellar_id' => $request->cellar_id,
                'quantity' => $request->cellar_qty
            ]);
        }
        if($request->buyList_qty) {
            BuyList::create([
                'wine_id' => $wine->id,
                'user_id' => Auth::id(),
                'quantity' => $request->buyList_qty
            ]);
        }
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
        $userId = Auth::id();

        $exists = BuyList::where('user_id', $userId)
        ->where('wine_id', $wine->id)
        ->exists();
        $wine = new WineResource($wine);
        $wine = $wine->resolve();
        
        return Inertia::render('Wine/ShowView', compact('wine','exists'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function edit(Wine $wine)
    {
        return Inertia::render('Wine/EditView', compact('wine'));
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
        $request->validate([
            'name' => 'min:3|max:100',
            'type' => 'min:3|max:45|nullable',
            'country' => 'min:3|max:100|nullable',
            'size' => 'min:3|max:45|nullable',
            'price' => 'numeric|gte:0|nullable'
        ]);
        $wine->update($request->all());
        return redirect(route('collection'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wine  $wine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wine $wine)
    {
        Wine::destroy($wine->id);
    }
}
