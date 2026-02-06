<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Procesa el login del usuario
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            /** @var User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;
        
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }

    // // Procesa el logout del usuario
    // public function logout()
    // {
    //     Auth::logout();
    //     return redirect()->route('login');
    // }
}
