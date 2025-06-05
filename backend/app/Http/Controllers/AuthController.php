<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if(Auth::attempt(credentials: $credentials))
        {
            $user = User::where('email', '=', $credentials['email'])->firstOrFail();

            $user->tokens()->delete();

            $expiresAt = now()->addHours((int) env('TOKEN_EXPIRES', 24));
            $token = $user->createToken(name: 'general', expiresAt: $expiresAt)->plainTextToken;

            return response()->json([
                'message' => 'Authorized',
                'token' => $token,
                'user' => new UserResource($user)
            ]);
        }

        return response()->json(['error' => 'Not Authorized'], 403);
    }

    public function check() {
        return response()->json([
            'check' => Auth::check()
        ]);
    }
}
