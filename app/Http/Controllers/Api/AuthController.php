<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
              'login' => 'required',
              'password' => 'required'
            ]);
            
            $user = User::where('login', $request->login)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response('Login invalid', 503);
            }
            
            Auth::login($user);
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
              'status_code' => 200,
              'access_token' => $tokenResult,
              'token_type' => 'Bearer',
            ]);
        } catch (Exception $error) {
            return response()->json([
              'status_code' => 500,
              'message' => 'Error in Login',
              'error' => $error,
            ]);
        }
    }
}
