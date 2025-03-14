<?php

namespace App\Http\Controllers;

use App\Models\itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueryBuilderController extends Controller
{
    //• Requête pour récupérer tous les itinéraires avec leurs destinations
    //http://127.0.0.1:8000/api/itineraries/All
    //Route::get('/itineraries/All' , [QueryBuilderController::class , 'ItinerariesWithDestinations'])->name('statistiques.All');
    public function ItinerariesWithDestinations()
    {
        //Requête pour récupérer tous les itinéraires avec leurs destinations

        $itineraries = DB::table('itineraries')
            ->join('destinations', 'destinations.itinerary_id', '=', 'itineraries.id')
            ->select('itineraries.*', 'destinations.name')
            ->get();
        return response()->json($itineraries);
    }

// http://127.0.0.1:8000/api/itineraries/filter?title=montagne&duration=5
    public function filterItinerariesByCategorieAndDuree(Request $request): \Illuminate\Http\JsonResponse
    {

        //  return response()->json($request->title);  //    "\"montagne\""    pourquoi me donne cette resultat a la palce de montagne

        $itineraries = DB::table('itineraries')
            ->join('categories', 'categories.id', '=', 'itineraries.category_id')
            ->select('itineraries.*', 'categories.title')
            ->when($request->has('title'), function ($query) use ($request) {
                //  return $query->where('categories.title', 'like', '%' . $request->title . '%');
                return $query->where('categories.title', 'like', '%' . $request->title . '%');
            })
            ->when($request->has('duration'), function ($query) use ($request) {
                return $query->where('itineraries.duration', '=', $request->duration);
            })
            ->get();

        return response()->json($itineraries);

    }

    //• Ajouter un itinéraire à la liste personnelle "À visiter"
//
    public function store(Request $request , itinerary $itinerary)
    {

       $user_id = Auth::id();
        $id_i = $itinerary->id ;
        $itineraire = itinerary::find($id_i);
        if (!$itineraire) {
            return response()->json([
                'message' => 'Itinéraire non trouvé.'
            ], 404);  // Retourner une erreur 404 si l'itinéraire n'existe pas
        }

        $exist = DB::table('favoris')
            ->where('user_id', '=', $user_id)
           ->where('itinerary_id', '=', $id_i)
           ->exists();

       if ($exist) {
           return response()->json([
               'message'=>'Vous avez deja cet iternaire dans votre liste'
           ]) ;
       } else {
           $query = DB::table('favoris')
               ->insert([
                   'user_id' => $user_id,
                    'itinerary_id'=> $id_i,
                   'created_at' => now(),
                   'updated_at' => now()
               ]);
       }
        return response()->json(['message' => 'L\'itinéraire a été ajouté à votre liste "À visiter".'], 201);
    }


// Recherche d'itinéraires contenant un mot-clé dans le titre
// http://127.0.0.1:8000/api/itineraries/search?title=montagne
    public function searchItineraries(Request $request)
    {
        // Récupère le mot-clé de la requête
        $keyword = $request->input('mot');  // Le mot-clé de la recherche (par exemple : "montagne")

        // Effectue la recherche dans la table 'itineraries' avec un 'like' pour le mot-clé dans le titre
        $itineraries = DB::table('itineraries')
            ->where('title', 'like', '%' . $keyword . '%')  // Utilisation de 'like' avec des jokers pour rechercher partiellement
            ->get();  // Récupère les itinéraires qui correspondent à la recherche

        // Retourne les itinéraires trouvés au format JSON
        return response()->json($itineraries);
    }

// Récupérer les itinéraires les plus populaires (avec le plus de favoris)
//
//select i.id , count(f.itinerary_id) as tot from itineraries i
//inner join favoris f on i.id = f.itinerary_id
//group by i.id
//limit 1


    public function topItinerary(){
        $itineraries = DB::table('itineraries')
            ->join('favoris', 'favoris.itinerary_id', '=', 'itineraries.id')
            ->select('itineraries.*', DB::raw('COUNT(favoris.user_id) as total'))
            ->groupBy('itineraries.id', 'itineraries.title', 'itineraries.description', 'itineraries.duration', 'itineraries.image', 'itineraries.category_id', 'itineraries.created_at', 'itineraries.updated_at', 'itineraries.user_id') // Ajoute toutes les colonnes sélectionnées
            ->orderByDesc('total')
            ->limit(1)
            ->get();

        return response()->json($itineraries);
    }

//• Statistiques : Nombre total d'itinéraires par catégorie

    public function CountItineraryByCategorie(){
        $itineraries = DB::table('itineraries')
            ->join('categories', 'categories.id', '=', 'itineraries.category_id')
            ->select('categories.title', DB::raw('COUNT(itineraries.id) as nb_itineraries'))
            ->groupBy('categories.title') // Ajoute toutes les colonnes sélectionnées
            ->orderByDesc('nb_itineraries')
            ->get();

        return response()->json($itineraries);
    }

}
