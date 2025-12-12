@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-7xl mx-auto">
        {{-- En-tête --}}
        <div class="mb-12">
            <h1 class="text-3xl font-bold mb-2">Suivi des Validations Partenaires</h1>
            <p class="text-gray-600">Suivez les réservations en attente de validation des partenaires</p>
        </div>

        @if($reservations->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500 mb-4">Aucune réservation avec des activités partenaires</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach($reservations as $reservation)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                        {{-- En-tête de la réservation --}}
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Réservation #{{ $reservation->numreservation }}</p>
                                    <h3 class="text-2xl font-bold">{{ $reservation->resort->nomresort ?? 'N/A' }}</h3>
                                    <p class="text-gray-600 mt-1">Client: {{ $reservation->user->name ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-blue-600">{{ $reservation->nbpersonnes }} pers.</div>
                                    <p class="text-sm text-gray-600">
                                        {{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}
                                    </p>
                                    <span class="inline-block mt-2 px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($reservation->statut) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Activités et partenaires --}}
                        <div class="p-6">
                            @if($reservation->activites->isEmpty())
                                <p class="text-gray-500 text-center py-4">Aucune activité à la carte réservée</p>
                            @else
                                <h4 class="font-bold text-lg mb-4">Activités Partenaires</h4>
                                <div class="space-y-3">
                                    @foreach($reservation->activites as $activity)
                                        @if($activity->activite)
                                            <div class="border border-gray-200 rounded p-4 bg-gray-50">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <p class="font-bold text-gray-800">{{ $activity->activite->nomactivite ?? 'N/A' }}</p>
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            Quantité: {{ $activity->quantite }} pers. | Prix: {{ number_format($activity->prix_unitaire, 2, ',', ' ') }} €/pers.
                                                        </p>
                                                    </div>
                                                    <div class="ml-4">
                                                        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                                            En attente
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-6 flex gap-4">
                                <a href="{{ route('stay-confirmation.form', $reservation->numreservation) }}" 
                                   class="flex-1 text-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition">
                                    ✓ Confirmer le Séjour
                                </a>
                                <a href="{{ route('reservations.index') }}" 
                                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-bold transition">
                                    Détails
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
