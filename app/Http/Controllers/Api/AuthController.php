<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('watchlog-mobile')->plainTextToken;

        return ApiResponseService::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Account created successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponseService::error('Invalid email or password', 401);
        }

        $token = $user->createToken('watchlog-mobile')->plainTextToken;

        return ApiResponseService::success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Logged in successfully');
    }

    public function logout(Request $request)
    {
        // Revokes only the token used for this request, not all of
        // the user's tokens across other devices/sessions.
        $request->user()->currentAccessToken()->delete();

        return ApiResponseService::success(null, 'Logged out successfully');
    }

    public function profile(Request $request)
    {
        return ApiResponseService::success(new UserResource($request->user()));
    }
}