<?php

namespace App\Http\Controllers;

use App\Models\destination;
use App\Models\itinerary;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(){
        $destinations = destination::all();
         return response()->json($destinations);

    }
    public function store(Request $request , itinerary $itinerary){
        $request->validate([
            'name' => 'required',
            'lodging' => 'required'
        ]) ;
        $destination = new Destination([
            'name' => $request->name,
            'lodging' => $request->lodging
        ]);
        $itinerary->destinations()->save($destination);
        return response()->json([
            'message' => 'Success: Destination added',
            'destination' =>  $destination
        ], 201);

    }
    public function destroy(Request $request , destination $destination){
        $destination->delete();
        return response()->json([
            'message' => 'Success: Destination deleted'
        ]) ;
    }
    public function showByItinerary(Request $request , itinerary $itinerary){
        $destinations = $itinerary->destinations()->get() ;
        return response()->json($destinations ) ;
    }
    public function show(Request $request , itinerary $itinerary){
        $destinations = $itinerary->destinations() ;
        return response()->json($destinations ) ;
    }
}
