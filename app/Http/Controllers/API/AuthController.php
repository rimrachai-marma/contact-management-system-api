<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function register(RegisterRequest $request) : JsonResponse {
        $user = User::create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => $user,
            "access_token" => ['token' => $token, 'token_type' => 'Bearer']
        ], 201);

    }

    public function login(LoginRequest $request) : JsonResponse {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'status' => 'error',
                'message' => __('auth.failed'),
            ], 401);
        }
        
        $user = Auth::user(); /** @var \App\Models\User $user */
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
            "access_token" => ['token' => $token, 'token_type' => 'Bearer']
        ], 200);
    }

    public function logout(Request $request) : JsonResponse {        
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful'
        ], 200);
    }
}
