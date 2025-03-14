<?php

namespace App\Http\Controllers;
use App\Models\itinerary;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ItineraryController extends Controller
{
    // list all the itinerary
    public function index(){
        $allItinerary = itinerary::with('category')->get();
        return response()->json($allItinerary);
    }
    public function show(Itinerary $itinerary){
        return response()->json($itinerary);
    }
    public function store(Request $request, $cat_id) {
        $user_id = auth()->id();

        // Si l'utilisateur n'est pas authentifié, retourner une erreur
        if (!$user_id) {
            return response()->json([
                "message" => "Unauthorized. Please log in."
            ], 401);
        }

        // Debugging pour voir les données envoyées
        Log::info("User ID: " . $user_id);
        Log::info("Request Data: ", $request->all());

        $request->validate([
            'title'       => 'required',
            'description' => 'required',
            'duration'    => 'required',
            'image'       => 'required'
        ]);

        // Création de l'itinéraire
        $createItinerary = Itinerary::create([
            'title'       => $request->title,
            'description' => $request->description,
            'duration'    => $request->duration,
            'image'       => $request->image,
            'category_id' => $cat_id,
            'user_id'     => $user_id  // Ici, on s'assure que user_id est bien inséré
        ]);

        return response()->json([
            'message' => $createItinerary ? "The itinerary has been created" : "The itinerary has not been created"
        ]);
    }

//    public function store(Request $request,$cat_id){
//        $user_id = auth()->id();
//        if ($user_id) {
//            return response()->json([
//                "message" => "authorized." . $user_id
//            ], 401);
//        }
//        $request->validate([
//            'title' => 'required',
//            'description' => 'required',
//            'duration' => 'required',
//            'image' => 'required'
//        ]);
//        $createItinerary = itinerary::create([
//            'title'         =>  $request->title,
//            'description'   =>  $request->description,
//            'duration'      =>  $request->duration,
//            'image'         =>  $request->image,
//            'category_id'   =>  $cat_id ,
//            'user_id'       => $user_id
//        ]);
//        // check if the procces is done
//        if($createItinerary){
//            return response()->json([
//                'message'   => "the itinerary has been created"
//            ]);
//        }else{
//            return response()->json([
//                "message"   => "the itinerary has not been created"
//            ]);
//        }
//    }

    public function update(Request $request,itinerary $itinerary){
        $user_id = auth()->id();
        if($itinerary->user_id !== $user_id)
        {
            return response()->json(['message'=>"Vous n'êtes pas autorisé à modifier cet itinéraire"], 403);
        }
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'duration' => 'required',
            'image' => 'required'
        ]);
        $updateItinerary = $itinerary->update([
            'title' => $request->title ,
            'description'=> $request->description ,
            'duration'=> $request->duration,
            'image'=> $request->image
        ]);

        if($updateItinerary){
            return response()->json([
                'message'   => "the itinerary has been updated"

            ]) ;
        }
    }
    public function destroy(itinerary $itinerary){
        $user_id = auth()->id();
        if($itinerary->user_id !== $user_id){
            return response()->json([
                'message'=>'vous etes pas autorise a modifier'
            ] , 403); }
           // $res = $this->destroy($itinerary);
        $itinerary->delete();   // Supprime l'itinéraire après la vérification.
            return response()->json([
                "message" => "the itinerary has been deleted"
            ] ,200);
        }


    public function filter(Request $request)
    {
        // Validation des paramètres de filtrage
        $request->validate([
            'category_id' => 'nullable|exists:categories,id', // Vérifie que la catégorie existe dans la table des catégories
            'duration' => 'nullable|integer|min:1', // Vérifie que la durée est un nombre entier
        ]);

        // Début de la requête
        $query = Itinerary::query();

        // Filtrage par catégorie si le paramètre est présent
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrage par durée si le paramètre est présent
        if ($request->has('duration')) {
            $query->where('duration', '<=', $request->duration); // Filtre les itinéraires dont la durée est inférieure ou égale à celle spécifiée
        }

        // Exécution de la requête et retour des résultats
        $itineraries = $query->get();
        if ($itineraries->isEmpty()) {
            return response()->json(['message' => 'Aucun itinéraire trouvé pour ces critères.'], 404);
        }
        return response()->json($itineraries);
    }


}
