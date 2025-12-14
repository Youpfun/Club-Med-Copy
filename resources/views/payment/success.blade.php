@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-6">
                <svg class="w-20 h-20 text-green-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-green-600 mb-2">Paiement réussi!</h1>
            <p class="text-gray-600 text-lg mb-6">Merci pour votre paiement</p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                <p class="text-gray-700 mb-2">
                    <span class="font-semibold">Numéro de réservation :</span> #{{ $reservation->numreservation }}
                </p>
                <p class="text-gray-700 mb-4">
                    <span class="font-semibold">Montant payé :</span> {{ number_format($reservation->prixtotal, 2, ',', ' ') }} €
                </p>
                <p class="text-sm text-green-700">
                    Votre réservation est confirmée et un email de confirmation vous a été envoyé.
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('reservation.show', $reservation->numreservation) }}" 
                   class="block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                    Voir les détails de ma réservation
                </a>
                
                <a href="{{ route('reservations.index') }}" 
                   class="block border border-blue-600 text-blue-600 hover:bg-blue-50 font-bold py-3 px-4 rounded-lg transition duration-200">
                    Mes réservations
                </a>

                <a href="{{ route('resorts.index') }}" 
                   class="block text-gray-600 hover:text-gray-800 py-2">
                    Continuer la navigation
                </a>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200 text-sm text-gray-500">
                <p>Un email de confirmation a été envoyé à <strong>{{ Auth::user()->email }}</strong></p>
                <p class="mt-2">Conservez votre numéro de réservation pour toute question</p>
            </div>
        </div>
    </div>
</div>
@endsection
