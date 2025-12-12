@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-6">Confirmer le Séjour</h1>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Détails de la réservation -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Détails de la Réservation</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 font-semibold">Numéro de réservation:</p>
                        <p class="text-lg">{{ $reservation->numreservation }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Resort:</p>
                        <p class="text-lg">{{ $reservation->resort->nomresort ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Client:</p>
                        <p class="text-lg">{{ $reservation->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Nombre de personnes:</p>
                        <p class="text-lg">{{ $reservation->nbpersonnes }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Dates:</p>
                        <p class="text-lg">{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Prix total:</p>
                        <p class="text-lg font-bold text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de confirmation -->
            <form id="stay-confirmation-form" action="{{ route('stay-confirmation.send', $reservation->numreservation) }}" method="POST">
                @csrf

                <!-- Sélection des destinataires -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Destinataires</h2>
                    
                    <div class="space-y-4">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="notify_resort" value="1" checked class="w-4 h-4 text-blue-600">
                            <span class="ml-3">
                                <span class="font-semibold">Notifier le Resort</span>
                                <p class="text-sm text-gray-600">{{ $reservation->resort->nomresort ?? 'Le resort' }} sera notifié de cette réservation</p>
                            </span>
                        </label>

                        @if($partenaires->isNotEmpty())
                            <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" name="notify_partenaires" value="1" checked class="w-4 h-4 text-blue-600">
                                <span class="ml-3">
                                    <span class="font-semibold">Notifier les Partenaires</span>
                                    <p class="text-sm text-gray-600">{{ $partenaires->count() }} partenaire(s) sera/seront notifié(s) de leurs activités réservées</p>
                                    <div class="mt-2">
                                        @foreach($partenaires as $partenaire)
                                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs mr-2 mb-1">
                                                {{ $partenaire->nompartenaire }}
                                            </span>
                                        @endforeach
                                    </div>
                                </span>
                            </label>
                        @else
                            <p class="text-gray-500 p-4 bg-gray-50 rounded-lg">Aucun partenaire associé à cette réservation</p>
                        @endif
                    </div>
                </div>

                <!-- Message personnalisé -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Message Personnalisé (Optionnel)</h2>
                    <textarea 
                        name="confirmation_message" 
                        class="w-full p-4 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        rows="4"
                        placeholder="Ajouter un message personnalisé à envoyer aux destinataires..."
                    ></textarea>
                    <p class="text-sm text-gray-600 mt-2">Maximum 1000 caractères</p>
                </div>

                <!-- Boutons d'action -->
                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        form="stay-confirmation-form"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition"
                    >
                        Confirmer et Envoyer les Emails
                    </button>
                    <a 
                        href="{{ route('reservations.index') }}" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition text-center"
                    >
                        Annuler
                    </a>
                </div>
            </form>

            <!-- Informations importantes -->
            <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h3 class="font-semibold text-yellow-800 mb-2">⚠️ Information importante</h3>
                <p class="text-yellow-700 text-sm">
                    L'envoi de ces emails marquera la réservation comme "confirmée". Assurez-vous que les partenaires ont bien validé la disponibilité avant de procéder.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
