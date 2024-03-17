<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication was successful
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $role =$user->roles->first()->name;

            return response()->json([
                'user' => $user,
                'token' => $token,
                'role'=>$role,
                ]
                , 200);
        } else {
            // Authentication failed
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

   

}
