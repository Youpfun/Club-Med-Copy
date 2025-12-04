<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ResortController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TypeclubController;
use App\Http\Controllers\LocalisationController;
use App\Http\Controllers\FicheResort;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ConnexionController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AvisController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resorts', [ResortController::class, 'index'])->name('resorts.index');
Route::get('/ficheresort/{numresort}', [FicheResort::class, 'fiche'])->name('resort.show');

Route::get('/resort/{id}/types-activites', [ActiviteController::class, 'indexTypes'])->name('resort.types');
Route::get('/resort/{id}/type/{typeId}/activites', [ActiviteController::class, 'indexActivitesParType'])->name('resort.activites.detail');

Route::get('/typeclubs', [TypeclubController::class, 'index']);
Route::get('/localisations', [LocalisationController::class, 'index']);
Route::get('/clients', [UserController::class, 'index']);

Route::get('/resort/{id}/activites', [ActiviteController::class, 'index'])->name('resort.activites');

Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/login', [ConnexionController::class, 'show'])->name('login');
Route::post('/login', [ConnexionController::class, 'login'])->name('login.submit');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Panier
    Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
    Route::get('/panier/{numreservation}', [PanierController::class, 'show'])->name('panier.show');
    Route::delete('/panier/remove/{numreservation}', [PanierController::class, 'remove'])->name('panier.remove');

    // Réservations
    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    
    // Étapes de réservation
    Route::get('/reservation/{numresort}/step1', [ReservationController::class, 'step1'])->name('reservation.step1');
    Route::get('/reservation/{numresort}/step2', [ReservationController::class, 'step2'])->name('reservation.step2');
    Route::get('/reservation/{numresort}/step3', [ReservationController::class, 'step3'])->name('reservation.step3');
    Route::post('/reservation/{numresort}/addToCart', [ReservationController::class, 'addToCart'])->name('reservation.addToCart');

    // Avis
    Route::get('/reservation/{id}/avis', [AvisController::class, 'create'])->name('reservation.review');
    Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');

});
