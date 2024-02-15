<?php

namespace App\Http\Controllers;

use App\Models\BuyList;
use App\Models\Wine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BuyListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::id();

        $buylist = BuyList::where('buy_lists.user_id', $userId)
            ->join('wines', 'wines.id', '=', 'buy_lists.wine_id')
            ->get();

        return Inertia::render('BuyList/IndexView', compact('buylist'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($wine_id)
    {
        $wine = Wine::findOrFail($wine_id);
        $wineData = [
            'id' => $wine->id,
            'name' => $wine->name,
            'photo' => $wine->photo,
        ];
        return Inertia::render('BuyList/CreateView', compact('wineData'));
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
            'quantity' => 'numeric|min:1'
        ]);

        $userId = Auth::id();

        $newBuyList = BuyList::create([
            'wine_id' => $request->wine_id,
            'user_id' => $userId,
            'quantity'=> $request->quantity
        ]);

        return redirect(route('buylist.index', $newBuyList->id))->withSuccess('The item has been added to your shopping list successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BuyList  $buyList
     * @return \Illuminate\Http\Response
     */
    public function show(BuyList $buyList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BuyList  $buyList
     * @return \Illuminate\Http\Response
     */
    public function edit(BuyList $buyList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BuyList  $buyList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BuyList $buyList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BuyList  $buyList
     * @return \Illuminate\Http\Response
     */
    public function destroy($wine_id)
    {
        $userId = Auth::id();
        $buylist = BuyList::where('user_id', $userId)->where('wine_id', $wine_id)->first();

        if ($buylist) {

            $buylist->delete();
            return redirect(route('buylist.index'))->withSuccess('The item on your shopping list was successfully deleted');

        } else {

            return redirect(route('buylist.index'))->withError('Unable to delete item from your shopping list');
        }
    }
}
