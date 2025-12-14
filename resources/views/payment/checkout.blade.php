@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Finaliser votre paiement</h1>

            <!-- Résumé de la réservation -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Résumé de votre réservation</h2>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Resort</p>
                        <p class="font-semibold text-gray-800">{{ $details->nomresort }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Type de chambre</p>
                        <p class="font-semibold text-gray-800">{{ $details->nomtype }}</p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Dates</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($details->datedebut)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($details->datefin)->format('d/m/Y') }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-gray-600">Nombre de personnes</p>
                        <p class="font-semibold text-gray-800">{{ $details->nbpersonnes }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-300 mt-6 pt-4">
                    <div class="flex justify-between items-center text-lg">
                        <p class="font-semibold text-gray-700">Montant à payer</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($details->prixtotal, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de paiement -->
            <form action="{{ route('payment.checkout', $reservation->numreservation) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="numreservation" value="{{ $reservation->numreservation }}">

                <!-- Information utilisateur -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Vous serez redirigé vers Stripe pour sécuriser votre paiement
                    </p>
                </div>

                <!-- Bouton de paiement -->
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    <i class="fas fa-lock mr-2"></i>
                    Procéder au paiement sécurisé
                </button>

                <!-- Lien d'annulation -->
                <a href="{{ route('panier.show', $reservation->numreservation) }}" class="block text-center text-gray-600 hover:text-gray-800 py-2">
                    Retour au panier
                </a>
            </form>

            <!-- Messages de sécurité -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-4 text-center text-sm text-gray-600">
                    <div>
                        <i class="fas fa-lock text-blue-600 text-xl mb-2 block"></i>
                        <p>Paiement 100% sécurisé</p>
                    </div>
                    <div>
                        <i class="fas fa-credit-card text-blue-600 text-xl mb-2 block"></i>
                        <p>Géré par Stripe</p>
                    </div>
                    <div>
                        <i class="fas fa-check-circle text-blue-600 text-xl mb-2 block"></i>
                        <p>Données chiffrées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe JS (optionnel pour futur use) -->
<script src="https://js.stripe.com/v3/"></script>
@endsection
