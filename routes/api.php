<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ResortSearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/prix', [ReservationController::class, 'getPrix']);

// Recherche de resorts pour la barre de recherche dynamique
Route::get('/resorts/search', [ResortSearchController::class, 'search']);
