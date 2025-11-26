<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResortController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TypeclubController;
use App\Http\Controllers\LocalisationController;
use App\Http\Controllers\FicheResort; 


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

Route::get('/clients', [ClientController::class, 'index']);

Route::get('/typeclubs', [TypeclubController::class, 'index']);

Route::get('/localisations', [LocalisationController::class, 'index']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
