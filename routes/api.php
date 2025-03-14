<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FavorisController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QueryBuilderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// les ressouces pour categories / iternaire / destination / favoris
Route::group(['middleware'=>['auth:sanctum']], function() {
    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
    //categories
    Route::post('/category/add', [CategoryController::class, 'store'])->name('category.add');

    //itineraries
    Route::post('itinerary/add/{id_categorie}', [ItineraryController::class, 'store'])->name('itinerary.add');
    Route::put('itinerary/{itinerary}', [ItineraryController::class, 'update'])->name('itinerary.add');
    Route::delete('itinerary/{itinerary}', [ItineraryController::class, 'destroy'])->name('itinerary.delete');
    Route::get('itineraries', [ItineraryController::class, 'index'] )->name('itineraries');
    Route::get('itinerary/{itinerary}', [ItineraryController::class, 'show'])->name('itinerary.show');

    //destinations
    Route::post('itineraries/{itinerary}/destination/add', [DestinationController::class, 'store'])->name('destination.add');
    Route::delete('destination/{destination}', [DestinationController::class, 'destroy'])->name('destination.delete');
    Route::get('destination/{destination}', [DestinationController::class, 'show'])->name('destination.show');
    Route::get('destinations', [DestinationController::class, 'index'] )->name('destinations');
    Route::get('destinations/{itinerary}', [DestinationController::class, 'showByItinerary'])->name('destinations');


    //activities
    Route::post('activity/add', [ActivityController::class, 'store'])->name('activity.add');
    Route::delete('activity/{activity}', [ActivityController::class, 'destroy'])->name('activity.delete');
    Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::delete('activity/{activity}', [ActivityController::class, 'destroy'])->name('activity.delete');

    //favoris
    Route::get('favorites' , [FavorisController::class, 'index'])->name('favorites.index');
    Route::post('favorites/{itinerary}/add' , [FavorisController::class, 'store'])->name('favorites.store');
    Route::delete('favorites/{itinerary}/delete', [FavorisController::class, 'destroy'])->name('favorites.delete');

    //filtre par duree et catégorie
    Route::get('itineraries/filter', [ItineraryController::class, 'filter'])->name('itineraries.filter');
    });



// QueryBuilder   : statistique
//• Requête pour récupérer tous les itinéraires avec leurs destinations
//http://127.0.0.1:8000/api/itineraries/All
Route::get('/itineraries/All' , [QueryBuilderController::class , 'ItinerariesWithDestinations'])->name('statistiques.All');


//Filtrer les itinéraires par catégorie et durée
//http://127.0.0.1:8000/api/itineraries/filter?title=montagne&duration=5
Route::get('/itineraries/filter' , [QueryBuilderController::class , 'filterItinerariesByCategorieAndDuree'])->name('statistiques.filter');



Route::get('/itineraries/search', [QueryBuilderController::class , 'searchItineraries']);



//Récupérer les itinéraires les plus populaires (avec le plus de favoris)
Route::get('/topItinerary', [QueryBuilderController::class , 'topItinerary']);


//• Statistiques : Nombre total d'itinéraires par catégorie

Route::get('/CountItineraryByCategorie', [QueryBuilderController::class , 'CountItineraryByCategorie']);


//• Ajouter un itinéraire à la liste personnelle "À visiter"
//127.0.0.1:8000/api/favorites/13/addByQueryBuilder

Route::post('/favorites/{itinerary}/addByQueryBuilder', [QueryBuilderController::class, 'store'])
    ->name('favorites.addByQueryBuilder')
    ->middleware('auth:sanctum');
;


