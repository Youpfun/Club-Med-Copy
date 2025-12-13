@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Validation de Réservation</h1>
                    <p class="text-gray-600">Resort {{ $reservation->resort->nomresort }}</p>
                </div>

                <!-- Détails de la réservation -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-blue-800">Réservation #{{ $reservation->numreservation }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Client</p>
                            <p class="font-bold text-lg">{{ $reservation->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Nombre de personnes</p>
                            <p class="font-bold text-lg">{{ $reservation->nbpersonnes }} personne(s)</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date d'arrivée</p>
                            <p class="font-bold text-lg">{{ $reservation->datedebut->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date de départ</p>
                            <p class="font-bold text-lg">{{ $reservation->datefin->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Durée du séjour</p>
                            <p class="font-bold text-lg">{{ $reservation->datedebut->diffInDays($reservation->datefin) }} jours</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Prix total</p>
                            <p class="font-bold text-lg text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                        </div>
                    </div>
                </div>

                <!-- Chambres réservées -->
                @if($reservation->chambres && $reservation->chambres->isNotEmpty())
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4">Chambres réservées</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="space-y-2">
                                @foreach($reservation->chambres as $chambre)
                                    <li class="flex items-center">
                                        <span class="text-blue-500 mr-2">🛏️</span>
                                        <span class="font-medium">{{ $chambre->typechambre->nomtype ?? 'N/A' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Activités à la carte -->
                @if($reservation->activites && $reservation->activites->isNotEmpty())
                    <div class="mb-8">
                        <h3 class="text-xl font-bold mb-4">Activités à la carte</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="space-y-3">
                                @foreach($reservation->activites as $activite)
                                    <li class="border-b border-gray-200 pb-2 last:border-0">
                                        <p class="font-bold text-gray-800">{{ $activite->activite->nomactivite ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $activite->quantite }} personne(s) × {{ number_format($activite->prix_unitaire, 2, ',', ' ') }} € = 
                                            <span class="font-bold">{{ number_format($activite->quantite * $activite->prix_unitaire, 2, ',', ' ') }} €</span>
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Message d'information -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Important :</strong> En validant cette réservation, les partenaires des activités seront automatiquement contactés pour confirmer leur disponibilité.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de validation -->
                <form action="{{ url('/resort/validate/' . $token) }}" method="POST" id="validation-form">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Commentaire (optionnel)</label>
                        <textarea 
                            name="comment" 
                            rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                            placeholder="Ajouter un commentaire si nécessaire..."
                        ></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button 
                            type="submit" 
                            name="action" 
                            value="accept"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition text-center text-lg"
                        >
                            ✓ Valider la réservation
                        </button>
                        
                        <button 
                            type="submit" 
                            name="action" 
                            value="refuse"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg transition text-center text-lg"
                            onclick="return confirm('Êtes-vous sûr de vouloir refuser cette réservation ?')"
                        >
                            ✗ Refuser la réservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
