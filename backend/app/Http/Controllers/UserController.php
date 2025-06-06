<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Criar usuário",
     *     tags={"Usuários"},
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", minLength=3),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     * )
     */
    public function store(StoreUserRequest $request)
    {
        $userData = $request->validated();

        try {
            $user = User::create($userData);

            return response()->json(new UserResource($user), 201);

        } catch (\Throwable $th) {
            $error = 'Falha ao criar usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Exibir usuário",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     * )
     */
    public function show(int $userId)
    {
        if (Auth::id() != $userId) {
            throw new HttpException(403, 'Não autorizado');
        }

        try {
            $user = User::findOrFail($userId);

            return response()->json(new UserResource($user));

        } catch (\Throwable $th) {
            $error = 'Usuário não encontrado.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/{id}",
     *     summary="Atualizar usuário",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", minLength=3),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, nullable=true),
     *             @OA\Property(property="password_confirmation", type="string", format="password", minLength=8, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     * )
     */
    public function update(UpdateUserRequest $request, int $userId)
    {
        $userData = $request->validated();

        try {
            $user = User::findOrFail($userId);

            $userDataFiltered = array_filter(
                $userData,
                fn($value) => !is_null($value)
            );
            $user->update($userDataFiltered);

            return response()->json(new UserResource($user));

        } catch (\Throwable $th) {

            $error = 'Falha ao atualizar usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Excluir usuário",
     *     tags={"Usuários"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Usuário excluído",
     *         @OA\JsonContent(example={})
     *     ),
     * )
     */
    public function destroy(int $userId)
    {
        if (Auth::user()->id != $userId) {
            throw new HttpException(403, 'Não autorizado');
        }

        try {
            $user = User::findOrFail($userId);

            $user->tokens()->delete();

            $user->delete();

            return response()->json([], 204);

        } catch (\Throwable $th) {
            $error = 'Falha ao deletar o usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }
}
