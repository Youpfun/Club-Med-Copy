@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Proposition de Resort Alternatif</h1>
                    <p class="text-gray-600">Réservation #{{ $reservation->numreservation }}</p>
                </div>

                {{-- Message d'alerte --}}
                <div class="mb-8 p-6 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <div class="flex items-start">
                        <span class="text-2xl mr-4">⚠️</span>
                        <div>
                            <h3 class="font-bold text-yellow-800">Resort indisponible</h3>
                            <p class="text-yellow-700 mt-1">
                                Le resort <strong>{{ $originalResort->nomresort }}</strong> n'est pas disponible pour les dates de votre séjour.
                                Notre équipe vous propose un resort alternatif.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Détails de la réservation --}}
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-bold mb-4 text-blue-800">Détails de votre réservation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Dates du séjour</p>
                            <p class="font-semibold">{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nombre de personnes</p>
                            <p class="font-semibold">{{ $reservation->nbpersonnes }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Prix total</p>
                            <p class="font-semibold text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                </div>

                {{-- Comparaison des resorts --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">Comparaison des Resorts</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Resort original --}}
                        <div class="border-2 border-red-300 rounded-lg p-6 bg-red-50 opacity-75">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xl">❌</span>
                                <span class="font-bold text-red-600">Resort initial (indisponible)</span>
                            </div>
                            <h3 class="text-lg font-bold line-through">{{ $originalResort->nomresort }}</h3>
                            @if($originalResort->pays)
                                <p class="text-gray-600">{{ $originalResort->pays->nompays }}</p>
                            @endif
                            @if($originalResort->nbtridents)
                                <p class="mt-2">
                                    @for($i = 0; $i < $originalResort->nbtridents; $i++)
                                        🔱
                                    @endfor
                                </p>
                            @endif
                        </div>

                        {{-- Resort alternatif --}}
                        <div class="border-2 border-green-400 rounded-lg p-6 bg-green-50">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xl">✅</span>
                                <span class="font-bold text-green-600">Resort proposé</span>
                            </div>
                            <h3 class="text-lg font-bold">{{ $alternativeResort->nomresort }}</h3>
                            @if($alternativeResort->pays)
                                <p class="text-gray-600">{{ $alternativeResort->pays->nompays }}</p>
                            @endif
                            @if($alternativeResort->nbtridents)
                                <p class="mt-2">
                                    @for($i = 0; $i < $alternativeResort->nbtridents; $i++)
                                        🔱
                                    @endfor
                                </p>
                            @endif
                            @if($alternativeResort->descriptionresort)
                                <p class="mt-4 text-sm text-gray-600">{{ Str::limit($alternativeResort->descriptionresort, 200) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Message du service vente --}}
                @if($reservation->alternative_resort_message)
                    <div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-400 rounded">
                        <h3 class="font-bold text-blue-800 mb-2">💬 Message de notre équipe</h3>
                        <p class="text-gray-700">{!! nl2br(e($reservation->alternative_resort_message)) !!}</p>
                    </div>
                @endif

                {{-- Formulaire de réponse --}}
                <form action="{{ url('/client/alternative-resort/' . $token) }}" method="POST" id="response-form">
                    @csrf
                    
                    <div class="text-center mb-6">
                        <p class="text-lg font-semibold text-gray-800">Acceptez-vous ce resort alternatif ?</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button 
                            type="submit" 
                            name="action" 
                            value="accept"
                            class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition text-lg"
                        >
                            ✓ Accepter ce resort
                        </button>
                        <button 
                            type="submit" 
                            name="action" 
                            value="refuse"
                            class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition text-lg"
                        >
                            ✗ Refuser
                        </button>
                    </div>
                </form>

                {{-- Note --}}
                <div class="mt-8 p-4 bg-gray-100 rounded text-sm text-gray-600 text-center">
                    <p>Ce lien expire le <strong>{{ $reservation->alternative_resort_token_expires_at->format('d/m/Y à H:i') }}</strong></p>
                    <p class="mt-1">Si vous avez des questions, contactez notre service commercial.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
