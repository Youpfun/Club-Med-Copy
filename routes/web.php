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
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AvisController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resorts', [ResortController::class, 'index']);
Route::get('/ficheresort/{numresort}', [FicheResort::class, 'fiche'])->name('resort.show');
Route::get('/resort/{id}/types-activites', [ActiviteController::class, 'indexTypes'])->name('resort.types');
Route::get('/resort/{id}/type/{typeId}/activites', [ActiviteController::class, 'indexActivitesParType'])->name('resort.activites.detail');
Route::get('/resort/{id}/activites', [ActiviteController::class, 'index'])->name('resort.activites');

Route::get('/typeclubs', [TypeclubController::class, 'index']);
Route::get('/localisations', [LocalisationController::class, 'index']);
Route::get('/clients', [UserController::class, 'index']);

Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');
Route::get('/login', [ConnexionController::class, 'show'])->name('login');
Route::post('/login', [ConnexionController::class, 'login'])->name('login.submit');

Route::get('verify/otp', [TwoFactorController::class, 'index'])->name('2fa.verify');
Route::post('verify/otp', [TwoFactorController::class, 'store'])->name('2fa.store');
Route::post('verify/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::put('/user/update-custom', [UserController::class, 'updateCustom'])->name('user.update.custom');

    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
    Route::post('/panier/resort/{numresort}', [PanierController::class, 'add'])->name('cart.addResort');
    Route::delete('/panier/remove/{numresort}', [PanierController::class, 'remove'])->name('cart.remove');

    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    
    Route::get('/reservation/{id}/participants', function () { return "Page modification participants"; })->name('reservation.participants');
    Route::get('/reservation/{id}/activites', function () { return "Page ajout activités"; })->name('reservation.activities');

    Route::get('/reservation/{id}/avis', [AvisController::class, 'create'])->name('reservation.review');
    Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');

});