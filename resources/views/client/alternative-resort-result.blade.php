@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8 text-center">
                @if($status === 'accepted')
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-green-600 mb-4">Resort Alternatif Accepté !</h1>
                    
                    <p class="text-gray-600 mb-6">
                        Merci d'avoir accepté le resort <strong>{{ $alternativeResort->nomresort }}</strong>.
                        Votre réservation a été mise à jour.
                    </p>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h3 class="font-bold text-green-800 mb-2">Prochaines étapes</h3>
                        <p class="text-green-700 text-sm">
                            Le resort {{ $alternativeResort->nomresort }} va maintenant valider votre réservation.
                            Vous recevrez un email de confirmation dès que la validation sera effectuée.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Réservation #{{ $reservation->numreservation }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}<br>
                            {{ $reservation->nbpersonnes }} personnes<br>
                            <strong>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</strong>
                        </p>
                    </div>
                @else
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-red-600 mb-4">Proposition Refusée</h1>
                    
                    <p class="text-gray-600 mb-6">
                        Vous avez refusé le resort alternatif proposé.
                    </p>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <h3 class="font-bold text-yellow-800 mb-2">Que se passe-t-il maintenant ?</h3>
                        <p class="text-yellow-700 text-sm">
                            Notre équipe commerciale va vous contacter pour discuter d'autres options 
                            ou pour procéder à l'annulation de votre réservation si vous le souhaitez.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Réservation #{{ $reservation->numreservation }}</h3>
                        <p class="text-sm text-gray-600">
                            Un conseiller vous contactera sous 24h.
                        </p>
                    </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('reservations.index') }}" class="inline-block px-6 py-3 bg-clubmed-blue hover:bg-blue-900 text-white font-bold rounded-lg transition">
                        Voir mes réservations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
