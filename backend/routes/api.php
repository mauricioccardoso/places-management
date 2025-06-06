<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/user', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check', [AuthController::class, 'check']);

    Route::apiResource('user', UserController::class)->except('index', 'store');
    Route::apiResource('place', PlaceController::class);
});
