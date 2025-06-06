<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login do usuário",
     *     tags={"Autenticação"},
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Authorized"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/check",
     *     summary="Verifica se o usuário está autenticado",
     *     tags={"Autenticação"},
     *     @OA\Response(
     *         response=200,
     *         description="Status de autenticação",
     *         @OA\JsonContent(
     *             @OA\Property(property="check", type="boolean")
     *         )
     *     )
     * )
     */
    public function check() {
        return response()->json([
            'check' => Auth::check()
        ]);
    }
}
