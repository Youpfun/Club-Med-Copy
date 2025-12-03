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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ficheresort/{numresort}', [FicheResort::class, 'fiche']);

Route::get('/resorts', [ResortController::class, 'index']);

Route::get('/clients', [UserController::class, 'index']);

Route::get('/typeclubs', [TypeclubController::class, 'index']);

Route::get('/localisations', [LocalisationController::class, 'index']);

Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/resort/{id}/types-activites', [ActiviteController::class, 'indexTypes'])->name('resort.types');

Route::get('/resort/{id}/type/{typeId}/activites', [ActiviteController::class, 'indexActivitesParType'])->name('resort.activites.detail');

// Panier / réservations en cours
Route::post('/panier/resort/{numresort}', [PanierController::class, 'add'])
    ->middleware('auth')
    ->name('cart.addResort');

Route::get('/panier', [PanierController::class, 'index'])
    ->middleware('auth')
    ->name('cart.index');

// Mes réservations (réservations finalisées) - placeholder pour l'instant
Route::get('/mes-reservations', function () {
    return view('reservations');
})->middleware('auth')->name('reservations.index');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

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
});
