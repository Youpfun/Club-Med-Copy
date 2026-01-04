@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-4xl mx-auto">
        {{-- Retour au dashboard --}}
        <a href="{{ route('vente.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour au Dashboard
        </a>

        {{-- Messages de session --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✓ Succès!</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>✗ Erreur!</strong> {{ session('error') }}
            </div>
        @endif

        {{-- En-tête --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b p-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg">
                <h1 class="text-2xl font-bold text-white">Gestion des Activités</h1>
                <p class="text-blue-100 mt-1">Réservation #{{ $reservation->numreservation }}</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Client</p>
                        <p class="font-semibold">{{ $reservation->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Resort</p>
                        <p class="font-semibold">{{ $reservation->resort->nomresort ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Dates</p>
                        <p class="font-semibold">{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Prix total actuel</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                        </div>
                        <div>
                            <span class="px-3 py-1 text-sm font-bold rounded-full 
                                @if($reservation->statut === 'en_attente')
                                    bg-yellow-100 text-yellow-800
                                @elseif($reservation->statut === 'payee')
                                    bg-blue-100 text-blue-800
                                @elseif($reservation->statut === 'confirmee')
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($reservation->statut) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des activités --}}
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">Activités Réservées</h2>
                    @if($reservation->activites->isNotEmpty())
                        <form action="{{ route('vente.cancel-all-activities', $reservation->numreservation) }}" 
                              method="POST" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir annuler TOUTES les activités de cette réservation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-bold transition">
                                🗑️ Annuler toutes les activités
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($reservation->activites->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-lg font-semibold">Aucune activité réservée</p>
                    <p class="text-sm mt-2">Cette réservation ne contient pas d'activités à la carte.</p>
                </div>
            @else
                <div class="divide-y">
                    @php
                        $totalActivities = 0;
                    @endphp
                    @foreach($reservation->activites as $resActivity)
                        @php
                            $activityTotal = $resActivity->prix_unitaire * $resActivity->quantite;
                            $totalActivities += $activityTotal;
                        @endphp
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-lg">{{ $resActivity->activite->nomactivite ?? 'Activité N/A' }}</h3>
                                        @if($resActivity->activite->typeActivite)
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                {{ $resActivity->activite->typeActivite->nomtypeactivite }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($resActivity->activite->descriptionactivite)
                                        <p class="text-gray-600 text-sm mt-2">
                                            {{ Str::limit($resActivity->activite->descriptionactivite, 150) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-3 flex flex-wrap gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Quantité:</span>
                                            <span class="font-semibold">{{ $resActivity->quantite }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Prix unitaire:</span>
                                            <span class="font-semibold">{{ number_format($resActivity->prix_unitaire, 2, ',', ' ') }} €</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Sous-total:</span>
                                            <span class="font-bold text-blue-600">{{ number_format($activityTotal, 2, ',', ' ') }} €</span>
                                        </div>
                                    </div>

                                    @if($resActivity->partenaire_validation_status)
                                        <div class="mt-2">
                                            <span class="text-xs 
                                                @if($resActivity->partenaire_validation_status === 'accepted')
                                                    text-green-600
                                                @elseif($resActivity->partenaire_validation_status === 'refused')
                                                    text-red-600
                                                @else
                                                    text-orange-600
                                                @endif
                                            ">
                                                @if($resActivity->partenaire_validation_status === 'accepted')
                                                    ✓ Validé par le partenaire
                                                @elseif($resActivity->partenaire_validation_status === 'refused')
                                                    ✗ Refusé par le partenaire
                                                @else
                                                    ⏳ En attente de validation partenaire
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="ml-4">
                                    <form action="{{ route('vente.cancel-activity', [$reservation->numreservation, $resActivity->numactivite]) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette activité ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded font-medium transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Récapitulatif --}}
                <div class="border-t p-6 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600">Total des activités</p>
                            <p class="text-sm text-gray-500">{{ $reservation->activites->count() }} activité(s)</p>
                        </div>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalActivities, 2, ',', ' ') }} €</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
