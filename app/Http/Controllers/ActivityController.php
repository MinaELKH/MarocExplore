<?php

namespace App\Http\Controllers;

use App\Models\activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(){
        $activities = Activity::all();
        return response()->json([
            'activities' => $activities
        ]);
    }
    public function show(Activity $activity){

    }
    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:endroit,experience,plat',
        ]);
        $activity = activity::create([
            'title'=>$request->title ,
            'description'=>$request->description ,
            'type'=>$request->type
        ]);
        if($activity){
            return response()->json([
                'message'=>'success : the activity has been created'
            ]) ;
        }else {
            return response()->json([
                'message'=>'fail :the activity  has not been created'
            ]);
        }
    }
    public function destroy(Activity $activity){
        if($activity->delete()){
            return response()->json([
                'message'=>'success : the activity has been deleted'
            ]);
        } else{
            return response()->json([
                'message'=>'fail :the activity  has not been deleted'
            ]);
        }
    }
}
