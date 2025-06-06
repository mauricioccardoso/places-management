<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PlaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/place",
     *     summary="Listar lugares",
     *     tags={"Lugares"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filtrar pelo nome",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="query",
     *         description="Filtrar pela slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filtrar pela cidade",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="query",
     *         description="Filtrar pelo estado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de lugares",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="city", type="string"),
     *                 @OA\Property(property="state", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $places = Place::when($request->filled('name'), function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->when($request->filled('slug'), function ($query) use ($request) {
            $query->where('slug', 'like', '%' . $request->slug . '%');
        })
        ->when($request->filled('city'), function ($query) use ($request) {
            $query->where('city', 'like', '%' . $request->city . '%');
        })
        ->when($request->filled('state'), function ($query) use ($request) {
            $query->where('state', 'like', '%' . $request->state . '%');
        })
        ->get();

        return response()->json(PlaceResource::collection($places));
    }

    /**
     * @OA\Post(
     *     path="/api/place",
     *     summary="Criar um lugar",
     *     tags={"Lugares"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "city", "state"},
     *             @OA\Property(property="name", type="string", maxLength=62),
     *             @OA\Property(property="city", type="string", maxLength=62),
     *             @OA\Property(property="state", type="string", maxLength=62)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Lugar criado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string")
     *         )
     *     )
     * )
     */
    public function store(StorePlaceRequest $request)
    {
        $placeData = $request->validated();

        $slugExists = Place::where('slug', Str::slug($placeData['name']))->exists();
        if($slugExists){
            throw new BadRequestHttpException('Lugar com mesma slug já existente');
        }

        try {
            $place = Place::create($placeData);

            return response()->json(new PlaceResource($place), 201);
        } catch (\Throwable $th) {
            $error = 'Falha ao criar lugar.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/place/{id}",
     *     summary="Exibir lugar",
     *     tags={"Lugares"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string")
     *         )
     *     )
     * )
     */
    public function show(int $placeId)
    {
        try {
            $place = Place::findOrFail($placeId);

            return response()->json(new PlaceResource($place));

        } catch (\Throwable $th) {
            $error = 'Lugar não encontrado.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/place/{placeId}",
     *     summary="Atualizar lugar",
     *     tags={"Lugares"},
     *     @OA\Parameter(name="placeId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=62),
     *             @OA\Property(property="city", type="string", maxLength=62, nullable=true),
     *             @OA\Property(property="state", type="string", maxLength=62, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lugar atualizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string"),
     *             @OA\Property(property="created_at", type="string"),
     *             @OA\Property(property="updated_at", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdatePlaceRequest $request, int $placeId)
    {
        $placeData = $request->validated();

        $slug = Str::slug($placeData['name']);
        $slugExists = Place::where('slug', $slug)
                ->where('id', '!=', $placeId)
                ->exists();

        if($slugExists){
            throw new BadRequestHttpException('Lugar com mesma slug já existente');
        }

        try {
            $place = Place::findOrFail($placeId);

            $placeDataFiltered = array_filter(
                $placeData,
                fn($value) => !is_null($value)
            );
            $place->update($placeDataFiltered);

            return response()->json(new PlaceResource($place));

        } catch (\Throwable $th) {
            $error = 'Falha ao atualizar lugar.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/place/{id}",
     *     summary="Deletar lugar",
     *     tags={"Lugares"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=204,
     *         description="Lugar deletado com sucesso",
     *         @OA\JsonContent(example={})
     *     )
     * )
     */
    public function destroy(int $placeId)
    {
        try {
            $place = Place::findOrFail($placeId);

            $place->delete();

            return response()->json([], 204);

        } catch (\Throwable $th) {
            $error = 'Falha ao deletar um lugar.';
            Logger::log($th, $error);

            return response()->json(["error" => $error], 400);
        }
    }
}
