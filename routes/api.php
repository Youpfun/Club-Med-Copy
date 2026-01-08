<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ResortSearchController;
use App\Http\Controllers\CookieConsentController;

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

// API Cookies - Consentement RGPD
Route::prefix('cookies')->group(function () {
    Route::get('/consent', [CookieConsentController::class, 'getConsent']);
    Route::post('/consent', [CookieConsentController::class, 'saveConsent']);
    Route::post('/accept-all', [CookieConsentController::class, 'acceptAll']);
    Route::post('/reject-all', [CookieConsentController::class, 'rejectAll']);
    Route::delete('/consent', [CookieConsentController::class, 'revokeConsent']);
});
