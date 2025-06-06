<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $userData = $request->validated();

        DB::beginTransaction();
        try {
            $user = User::create($userData);

            DB::commit();

            return new UserResource($user);

        } catch (\Throwable $th) {
            DB::rollBack();

            $error = 'Falha ao criar usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $userId)
    {
        if (Auth::id() != $userId) {
            throw new HttpException(403, 'Não autorizado');
        }

        try {
            $user = User::findOrFail($userId);

            return new UserResource($user);

        } catch (\Throwable $th) {
            $error = 'Usuário não encontrado.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }

    /**
     * Update the specified resource in storage.
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

            return new UserResource($user);

        } catch (\Throwable $th) {

            $error = 'Falha ao atualizar usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $userId)
    {
        if (Auth::user()->id != $userId) {
            throw new HttpException(403, 'Não autorizado');
        }

        try {
            $user = User::findOrFail($userId);

            $user->delete();

            return response()->json([], 204);

        } catch (\Throwable $th) {
            $error = 'Falha ao deletar o usuário.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }
}
