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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
