<?php

namespace App\Http\Controllers;
use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store (Request $request){
        $request->validate([
            'title' => 'required|min:3|unique:categories',
        ]);
        $category = category::create([
            'title' => $request->title
        ]);
        if($category){
            return response()->json([
                'message'   => "the category has been created"
            ]);
        }else{
            return response()->json([
                "message"   => "the category has not been created"
            ]);
        }
    }
}
