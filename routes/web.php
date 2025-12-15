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
use App\Http\Controllers\StayConfirmationController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\PartnerValidationController;
use App\Http\Controllers\ResortValidationController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;

// Stripe Webhook (doit être AVANT les middlewares auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resorts', [ResortController::class, 'index'])->name('resorts.index');
Route::get('/ficheresort/{numresort}', [FicheResort::class, 'fiche'])->name('resort.show');
Route::get('/resort/{id}/types-activites', [ActiviteController::class, 'indexTypes'])->name('resort.types');
Route::get('/resort/{id}/type/{typeId}/activites', [ActiviteController::class, 'indexActivitesParType'])->name('resort.activites.detail');
Route::get('/resort/{id}/activites', [ActiviteController::class, 'index'])->name('resort.activites');

Route::post('/avis/{numavis}/signaler', [AvisController::class, 'report'])->name('avis.report');

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

Route::middleware(['auth', 'marketing'])->prefix('marketing')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\MarketingController::class, 'index'])->name('marketing.dashboard');
    Route::post('/update-price', [App\Http\Controllers\MarketingController::class, 'updatePrice'])->name('marketing.update_price');
    Route::post('/periodes', [App\Http\Controllers\MarketingController::class, 'storePeriode'])->name('marketing.store_periode');
    Route::post('/bulk-promo', [App\Http\Controllers\MarketingController::class, 'applyBulkPromo'])->name('marketing.bulk_promo');
    Route::post('/reset-promos', [App\Http\Controllers\MarketingController::class, 'resetPromos'])->name('marketing.reset_promos');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::put('/user/update-custom', [UserController::class, 'updateCustom'])->name('user.update.custom');

    Route::post('/api/prix', [ResortController::class, 'getPrix'])->name('api.prix');
    
    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
    // Paiement du panier (toutes les réservations en attente de l'utilisateur) - placer AVANT la route dynamique
    Route::post('/panier/checkout', [StripeController::class, 'checkoutCart'])->name('payment.cart.checkout');
    Route::get('/panier/payment-success', [StripeController::class, 'successCart'])->name('payment.cart.success');
    Route::get('/panier/payment-cancel', [StripeController::class, 'cancelCart'])->name('payment.cart.cancel');
    // Route dynamique en dernier pour éviter les collisions
    Route::get('/panier/{numreservation}', [PanierController::class, 'show'])->name('panier.show');
    Route::delete('/panier/remove/{numreservation}', [PanierController::class, 'remove'])->name('panier.remove');

    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation/{numreservation}/details', [ReservationController::class, 'show'])->name('reservation.show');
    Route::get('/reservation/{numresort}/step1', [ReservationController::class, 'step1'])->name('reservation.step1');
    Route::get('/reservation/{numresort}/step2', [ReservationController::class, 'step2'])->name('reservation.step2');
    Route::get('/reservation/{numresort}/step3', [ReservationController::class, 'step3'])->name('reservation.step3');
    Route::post('/reservation/{numresort}/addToCart', [ReservationController::class, 'addToCart'])->name('reservation.addToCart');
    
    // Routes d'édition de réservation
    Route::get('/reservation/{numreservation}/edit/step1', [ReservationController::class, 'editStep1'])->name('reservation.edit.step1');
    Route::post('/reservation/{numreservation}/update/step1', [ReservationController::class, 'updateStep1'])->name('reservation.update.step1');
    Route::get('/reservation/{numreservation}/edit/step2', [ReservationController::class, 'editStep2'])->name('reservation.edit.step2');
    Route::post('/reservation/{numreservation}/update/step2', [ReservationController::class, 'updateStep2'])->name('reservation.update.step2');
    Route::get('/reservation/{numreservation}/edit/step3', [ReservationController::class, 'editStep3'])->name('reservation.edit.step3');
    Route::post('/reservation/{numreservation}/update/step3', [ReservationController::class, 'updateStep3'])->name('reservation.update.step3');
    
    Route::get('/reservation/{id}/activities', function ($id) {
        return redirect("/reservation/{$id}/step3");
    })->name('reservation.activities');
    Route::get('/reservation/{id}/participants', function ($id) {
        return redirect("/reservation/{$id}/step2");
    })->name('reservation.participants');

    Route::get('/reservation/{id}/avis', [AvisController::class, 'create'])->name('reservation.review');
    Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');

    Route::get('/stay-confirmation/{numreservation}', [StayConfirmationController::class, 'showConfirmationForm'])->name('stay-confirmation.form');
    Route::post('/stay-confirmation/{numreservation}', [StayConfirmationController::class, 'sendConfirmation'])->name('stay-confirmation.send');

    // Routes Stripe / Paiement
    Route::get('/reservation/{numreservation}/payment', [StripeController::class, 'showPaymentPage'])->name('payment.page');
    Route::post('/reservation/{numreservation}/checkout', [StripeController::class, 'checkout'])->name('payment.checkout');
    Route::get('/reservation/{numreservation}/payment-success', [StripeController::class, 'success'])->name('payment.success');
    Route::get('/reservation/{numreservation}/payment-cancel', [StripeController::class, 'cancel'])->name('payment.cancel');

    

    Route::prefix('vente')->middleware('vente')->group(function () {
        Route::get('/dashboard', [VenteController::class, 'dashboard'])->name('vente.dashboard');
        Route::get('/reject-reservation/{numreservation}', [VenteController::class, 'showRejectForm'])->name('vente.reject-form');
        Route::post('/reject-reservation/{numreservation}', [VenteController::class, 'rejectReservation'])->name('vente.reject');
        Route::post('/confirm-reservation/{numreservation}', [VenteController::class, 'confirmReservation'])->name('vente.confirm');
    });

});

// Routes partenaires (pas d'auth obligatoire, sécurisées par token)
Route::get('/partner/validate/{token}', [PartnerValidationController::class, 'show']);
Route::post('/partner/validate/{token}', [PartnerValidationController::class, 'respond']);

// Routes resort (pas d'auth obligatoire, sécurisées par token)
Route::get('/resort/validate/{token}', [ResortValidationController::class, 'show']);
Route::post('/resort/validate/{token}', [ResortValidationController::class, 'respond']);