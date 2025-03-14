<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use HasApiTokens;

class AuthController extends Controller
{
    public function login(Request $request){
          $request->validate([
              'email' => 'required|string|email',
              'password' => 'required|string',
          ]);
          $user =User::where('email' , $request->email)->first();
          if(!$user || !Hash::check($request->password, $user->password)){
              return response()->json([
                  'message' => 'The provided credentials are incorrect.',
              ] , 401 ) ;
          }
          $token = $user->createToken('my-app-token')->plainTextToken;
          return response()->json([
              'message' => 'Logged in successfully',
              'access_token' => $token
          ]);
    }
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        $user = User::create([
            "name"  =>  $request->name,
            "email" =>  $request->email,
            "password"  =>  $request->password
        ]);
        $token = $user->createToken('token-name')->plainTextToken;
        if($user){
            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
            ]);
        }else{
            return response()->json([
                'message' => 'User Registration Failed',
            ]);
        }
    }
    function logout(Request $request){
        $request->user()->tokens->each(function ($token) {
            $token->delete(); // Révoque chaque token de l'utilisateur
        });

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
