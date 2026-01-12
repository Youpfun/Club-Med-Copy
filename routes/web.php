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
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\IndisponibiliteController;
use App\Http\Controllers\AlternativeResortController;
use App\Http\Controllers\DemandeDisponibiliteController;
use App\Http\Controllers\ProspectionResortController;
use App\Http\Controllers\ProspectionPartenaireController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\PersonalDataController;
use App\Http\Controllers\BotManController;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::get('/resorts', [ResortController::class, 'index'])->name('resorts.index');
Route::get('/ficheresort/{numresort}', [FicheResort::class, 'fiche'])->name('resort.show');
Route::get('/resort/{id}/types-activites', [ActiviteController::class, 'indexTypes'])->name('resort.types');
Route::get('/resort/{id}/type/{typeId}/activites', [ActiviteController::class, 'indexActivitesParType'])->name('resort.activites.detail');
Route::get('/resort/{id}/activites', [ActiviteController::class, 'index'])->name('resort.activites');

Route::post('/avis/{numavis}/signaler', [AvisController::class, 'report'])->name('avis.report');

Route::get('/typeclubs', [TypeclubController::class, 'index']);
Route::get('/localisations', [LocalisationController::class, 'index']);
Route::get('/clients', [UserController::class, 'index']);

Route::get('/guide', function () {
    return view('guide');
})->name('guide');

Route::get('/inscription', [InscriptionController::class, 'create'])->name('inscription.create');
Route::post('/inscription', [InscriptionController::class, 'store'])->name('inscription.store');
Route::get('/login', [ConnexionController::class, 'show'])->name('login');
Route::post('/login', [ConnexionController::class, 'login'])->name('login.submit');

Route::get('verify/otp', [TwoFactorController::class, 'index'])->name('2fa.verify');
Route::post('verify/otp', [TwoFactorController::class, 'store'])->name('2fa.store');
Route::post('verify/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');

// --- GROUPE MARKETING ---
Route::middleware(['auth', 'marketing'])->prefix('marketing')->group(function () {
    Route::get('/dashboard', [MarketingController::class, 'index'])->name('marketing.dashboard');
    
    Route::get('/resorts/create', [ResortController::class, 'create'])->name('resort.create');
    Route::post('/resorts', [ResortController::class, 'store'])->name('resort.store');
    Route::get('/resort/{id}/edit-structure', [ResortController::class, 'editStructure'])->name('resort.editStructure');
    Route::put('/resort/{id}/structure', [ResortController::class, 'updateStructure'])->name('resort.updateStructure');

    // Étape 2
    Route::get('/resort/{id}/accommodation', [ResortController::class, 'createAccommodation'])->name('resort.step2');
    Route::post('/resort/{id}/accommodation', [ResortController::class, 'storeAccommodation'])->name('resort.storeStep2');

    // Étape 3
    Route::get('/resort/{id}/activities', [ResortController::class, 'createActivities'])->name('resort.step3');
    Route::post('/resort/{id}/activities', [ResortController::class, 'storeActivities'])->name('resort.storeStep3');
    Route::delete('/resort/{id}/activity/{activityId}', [ResortController::class, 'destroyActivity'])->name('resort.activity.destroy');

    // Étape 4 (Celle qui manquait peut-être)
    Route::get('/resort/{id}/pricing', [ResortController::class, 'createPricing'])->name('resort.step4');
    Route::post('/resort/{id}/pricing', [ResortController::class, 'storePricing'])->name('resort.storeStep4');

    // Validation Finale (Directeur)
    Route::post('/resort/{id}/validate', [MarketingController::class, 'validateResort'])->name('marketing.resort.validate');

    // --- AUTRES ROUTES MARKETING ---
    Route::put('/resort/{id}/status', [MarketingController::class, 'updateStatus'])->name('marketing.resort.status');
    Route::delete('/resort/{id}', [MarketingController::class, 'destroy'])->name('marketing.resort.destroy');
    
    Route::post('/update-price', [MarketingController::class, 'updatePrice'])->name('marketing.update_price');
    Route::post('/periodes', [MarketingController::class, 'storePeriode'])->name('marketing.store_periode');
    Route::post('/bulk-promo', [MarketingController::class, 'applyBulkPromo'])->name('marketing.bulk_promo');
    Route::post('/reset-promos', [MarketingController::class, 'resetPromos'])->name('marketing.reset_promos');
    
    // GESTION INDISPONIBILITÉS & OCCUPATIONS (Modifié)
    Route::get('/indisponibilite/occupancy', [IndisponibiliteController::class, 'occupancy'])->name('marketing.indisponibilite.occupancy');
    Route::get('/indisponibilite/select', [IndisponibiliteController::class, 'selectResort'])->name('marketing.indisponibilite.select');
    Route::get('/indisponibilite/create/{numresort}', [IndisponibiliteController::class, 'create'])->name('marketing.indisponibilite.create');
    Route::post('/indisponibilite', [IndisponibiliteController::class, 'store'])->name('marketing.indisponibilite.store');
    Route::get('/indisponibilites', [IndisponibiliteController::class, 'index'])->name('marketing.indisponibilite.index');
    Route::delete('/indisponibilite/{id}', [IndisponibiliteController::class, 'destroy'])->name('marketing.indisponibilite.destroy');
    
    Route::get('/demandes', [DemandeDisponibiliteController::class, 'index'])->name('marketing.demandes.index');
    Route::get('/demandes/create', [DemandeDisponibiliteController::class, 'create'])->name('marketing.demandes.create');
    Route::post('/demandes', [DemandeDisponibiliteController::class, 'store'])->name('marketing.demandes.store');
    Route::get('/demandes/{numdemande}', [DemandeDisponibiliteController::class, 'show'])->name('marketing.demandes.show');
    Route::post('/demandes/{numdemande}/resend', [DemandeDisponibiliteController::class, 'resend'])->name('marketing.demandes.resend');

    Route::get('/prospection', [ProspectionResortController::class, 'index'])->name('marketing.prospection.index');
    Route::get('/prospection/create', [ProspectionResortController::class, 'create'])->name('marketing.prospection.create');
    Route::post('/prospection', [ProspectionResortController::class, 'store'])->name('marketing.prospection.store');
    Route::get('/prospection/{numprospection}', [ProspectionResortController::class, 'show'])->name('marketing.prospection.show');
    Route::put('/prospection/{numprospection}/statut', [ProspectionResortController::class, 'updateStatut'])->name('marketing.prospection.update-statut');
    Route::post('/prospection/{numprospection}/resend', [ProspectionResortController::class, 'resend'])->name('marketing.prospection.resend');
    Route::delete('/prospection/{numprospection}', [ProspectionResortController::class, 'destroy'])->name('marketing.prospection.destroy');

    Route::get('/prospection-partenaire', [ProspectionPartenaireController::class, 'index'])->name('marketing.prospection-partenaire.index');
    Route::get('/prospection-partenaire/create', [ProspectionPartenaireController::class, 'create'])->name('marketing.prospection-partenaire.create');
    Route::post('/prospection-partenaire', [ProspectionPartenaireController::class, 'store'])->name('marketing.prospection-partenaire.store');
    Route::get('/prospection-partenaire/{numprospection}', [ProspectionPartenaireController::class, 'show'])->name('marketing.prospection-partenaire.show');
    Route::put('/prospection-partenaire/{numprospection}/statut', [ProspectionPartenaireController::class, 'updateStatut'])->name('marketing.prospection-partenaire.update-statut');
    Route::post('/prospection-partenaire/{numprospection}/resend', [ProspectionPartenaireController::class, 'resend'])->name('marketing.prospection-partenaire.resend');
    Route::delete('/prospection-partenaire/{numprospection}', [ProspectionPartenaireController::class, 'destroy'])->name('marketing.prospection-partenaire.destroy');
});

// Routes accessibles sans connexion pour voir les dates/prix/activités
Route::get('/reservation/{numresort}/step1', [ReservationController::class, 'step1'])->name('reservation.step1');
Route::get('/reservation/{numresort}/step2', [ReservationController::class, 'step2'])->name('reservation.step2');
Route::get('/reservation/{numresort}/step3', [ReservationController::class, 'step3'])->name('reservation.step3');
Route::post('/reservation/{numresort}/saveToSession', [ReservationController::class, 'saveToSession'])->name('reservation.saveToSession');
Route::post('/api/prix', [ResortController::class, 'getPrix'])->name('api.prix');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::put('/user/update-custom', [UserController::class, 'updateCustom'])->name('user.update.custom');
    
    // RGPD - Données personnelles
    Route::get('/profile/personal-data', [PersonalDataController::class, 'index'])->name('profile.personal-data');
    Route::get('/profile/export-data', [PersonalDataController::class, 'export'])->name('profile.export-data');
    Route::get('/profile/delete-account', [PersonalDataController::class, 'showDeleteForm'])->name('profile.delete-account');
    Route::post('/profile/request-deletion', [PersonalDataController::class, 'requestDeletion'])->name('profile.request-deletion');
    Route::post('/profile/cancel-deletion', [PersonalDataController::class, 'cancelDeletion'])->name('profile.cancel-deletion');
    
    // RGPD - Gestion des données (Anonymisation et Suppression)
    Route::get('/profile/gdpr-request', [PersonalDataController::class, 'showGdprRequest'])->name('profile.gdpr-request');
    Route::post('/profile/gdpr-request', [PersonalDataController::class, 'processGdprRequest'])->name('profile.process-gdpr-request');
    
    Route::post('/logout', function () {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    Route::get('/panier', [PanierController::class, 'index'])->name('cart.index');
    Route::post('/panier/checkout', [StripeController::class, 'checkoutCart'])->name('payment.cart.checkout');
    Route::get('/panier/payment-success', [StripeController::class, 'successCart'])->name('payment.cart.success');
    Route::get('/panier/payment-cancel', [StripeController::class, 'cancelCart'])->name('payment.cart.cancel');
    Route::get('/panier/{numreservation}', [PanierController::class, 'show'])->name('panier.show');
    Route::delete('/panier/remove/{numreservation}', [PanierController::class, 'remove'])->name('panier.remove');

    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservation/{numreservation}/details', [ReservationController::class, 'show'])->name('reservation.show');
    Route::post('/reservation/{numresort}/addToCart', [ReservationController::class, 'addToCart'])->name('reservation.addToCart');
    
    Route::get('/reservation/{numreservation}/edit', [ReservationController::class, 'editReservation'])->name('reservation.edit');
    Route::post('/reservation/{numreservation}/update', [ReservationController::class, 'updateReservationComplete'])->name('reservation.update.complete');
    
    Route::get('/reservation/{numreservation}/activities', [ReservationController::class, 'showAddActivities'])->name('reservation.activities');
    Route::post('/reservation/{numreservation}/activities/checkout', [StripeController::class, 'checkoutActivities'])->name('activities.checkout');
    Route::get('/reservation/{numreservation}/activities/success', [StripeController::class, 'activitiesSuccess'])->name('activities.success');
    Route::get('/reservation/{numreservation}/activities/cancel', [StripeController::class, 'activitiesCancel'])->name('activities.cancel');
    
    Route::get('/reservation/{id}/participants', function ($id) {
        return redirect("/reservation/{$id}/step2");
    })->name('reservation.participants');

    Route::get('/reservation/{id}/avis', [AvisController::class, 'create'])->name('reservation.review');
    Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');

    Route::get('/stay-confirmation/{numreservation}', [StayConfirmationController::class, 'showConfirmationForm'])->name('stay-confirmation.form');
    Route::post('/stay-confirmation/{numreservation}', [StayConfirmationController::class, 'sendConfirmation'])->name('stay-confirmation.send');

    Route::get('/reservation/{numreservation}/payment', [StripeController::class, 'showPaymentPage'])->name('payment.page');
    Route::post('/reservation/{numreservation}/checkout', [StripeController::class, 'checkout'])->name('payment.checkout');
    Route::get('/reservation/{numreservation}/payment-success', [StripeController::class, 'success'])->name('payment.success');
    Route::get('/reservation/{numreservation}/payment-cancel', [StripeController::class, 'cancel'])->name('payment.cancel');

    Route::prefix('vente')->middleware('vente')->group(function () {
        Route::get('/dashboard', [VenteController::class, 'dashboard'])->name('vente.dashboard');
        Route::get('/reject-reservation/{numreservation}', [VenteController::class, 'showRejectForm'])->name('vente.reject-form');
        Route::post('/reject-reservation/{numreservation}', [VenteController::class, 'rejectReservation'])->name('vente.reject');
        Route::get('/propose-alternative/{numreservation}', [VenteController::class, 'showProposeAlternativeForm'])->name('vente.propose-alternative-form');
        Route::post('/propose-alternative/{numreservation}', [VenteController::class, 'proposeAlternativeResort'])->name('vente.propose-alternative');
        
        Route::get('/reservation/{numreservation}/activities', [VenteController::class, 'showActivities'])->name('vente.activities');
        Route::delete('/reservation/{numreservation}/activity/{numactivite}', [VenteController::class, 'cancelActivity'])->name('vente.cancel-activity');
        Route::delete('/reservation/{numreservation}/activities', [VenteController::class, 'cancelAllActivities'])->name('vente.cancel-all-activities');
        Route::delete('/reservation/{numreservation}/pending-partner-activities', [VenteController::class, 'cancelPendingPartnerActivities'])->name('vente.cancel-pending-partner-activities');
        
        Route::get('/avis', [VenteController::class, 'avisIndex'])->name('vente.avis');
        Route::get('/avis/{numavis}', [VenteController::class, 'avisShow'])->name('vente.avis.show');
        Route::post('/avis/{numavis}/repondre', [AvisController::class, 'repondre'])->name('vente.avis.repondre');
        
        Route::get('/test-reject/{numreservation}', function ($numreservation) {
            $reservation = \App\Models\Reservation::findOrFail($numreservation);
            return view('vente.reject-reservation-test', ['reservation' => $reservation]);
        })->name('vente.reject-test');
    });

});

Route::get('/partner/validate/{token}', [PartnerValidationController::class, 'show']);
Route::post('/partner/validate/{token}', [PartnerValidationController::class, 'respond']);
Route::get('/resort/validate/{token}', [ResortValidationController::class, 'show']);
Route::post('/resort/validate/{token}', [ResortValidationController::class, 'respond']);

Route::get('/client/alternative-resort/{token}', [AlternativeResortController::class, 'show']);
Route::post('/client/alternative-resort/{token}', [AlternativeResortController::class, 'respond']);

Route::get('/resort/disponibilite/{token}', [DemandeDisponibiliteController::class, 'showResortResponse']);
Route::post('/resort/disponibilite/{token}', [DemandeDisponibiliteController::class, 'storeResortResponse']);

Route::match(['get', 'post'], '/botman', 'App\Http\Controllers\BotManController@handle');