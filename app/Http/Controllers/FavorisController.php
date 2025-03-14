<?php

namespace App\Http\Controllers;

use App\Models\favoris;
use App\Models\itinerary;
use Illuminate\Http\Request;

class FavorisController extends Controller
{
    public function index(){
        $user = auth()->user();
        $favorites =$user->favoriteItineraries()->get();
        return response()->json($favorites);
    }

    public function store(Request $request, $itinerary_id)
    {
        $user = auth()->user();
        $itinerary = Itinerary::findOrfail($itinerary_id);
        $user->favoriteItineraries()->attach($itinerary_id);
        return response()->json([
            'message'=>'itinerary added to favoris'
        ]);
    }
    public function destroy($itinerary_id)
    {
        $user = auth()->user();
        $user->favoriteItineraries()->detach($itinerary_id);
        return response()->json([
            'message'=>'itinerary removed from favoris'
        ]) ;
    }


}
