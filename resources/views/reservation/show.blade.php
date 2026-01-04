@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    
    {{-- En-tête avec Navigation --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <a href="{{ route('reservations.index') }}" class="text-gray-500 hover:text-[#113559] flex items-center mb-2 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour à mes séjours
            </a>
            <h1 class="text-3xl font-serif font-bold text-[#113559]">Détails de ma réservation</h1>
            <p class="text-gray-500">Référence dossier : <span class="font-mono font-bold text-black">#{{ $reservation->numreservation }}</span></p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-full text-sm font-bold border 
                {{ $reservation->statut === 'Confirmée' ? 'bg-green-100 text-green-700 border-green-200' : 
                   ($reservation->statut === 'En attente' ? 'bg-yellow-100 text-yellow-700 border-yellow-200' : 
                   ($reservation->statut === 'Annulée' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-gray-100 text-gray-700 border-gray-200')) }}">
                {{ $reservation->statut }}
            </span>
            <button onclick="window.print()" class="bg-white border border-gray-300 text-gray-700 p-2 rounded-lg hover:bg-gray-50 print:hidden" title="Imprimer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </button>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLONNE GAUCHE (Informations Principales) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. LE RESORT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-[#113559] text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Destination
                    </h2>
                    @if($reservation->resort->pays)
                        <span class="text-blue-200 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $reservation->resort->pays->nompays }}
                        </span>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-serif font-bold text-[#113559] mb-2">{{ $reservation->resort->nomresort }}</h3>
                    <p class="text-gray-600 mb-4">{{ $reservation->resort->descriptionresort }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Arrivée</p>
                            <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($reservation->datedebut)->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">Départ</p>
                            <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($reservation->datefin)->format('d/m/Y') }}</p>
                        </div>
                        <div class="md:col-span-2 border-t border-gray-200 pt-2 mt-2">
                            <p class="text-sm text-gray-600">Durée du séjour : <span class="font-bold text-[#113559]">{{ \Carbon\Carbon::parse($reservation->datedebut)->diffInDays(\Carbon\Carbon::parse($reservation->datefin)) }} nuits</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. HÉBERGEMENT & VOYAGEURS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#ffc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Hébergement & Participants
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Types de chambres réservées --}}
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Chambres réservées ({{ $chambres->sum('quantite') }})
                        </h4>
                        @if($chambres && $chambres->count() > 0)
                            <div class="space-y-3">
                                @foreach($chambres as $chambre)
                                    <div class="flex items-start gap-4 bg-gradient-to-r from-yellow-50 to-white border border-yellow-200 rounded-lg p-4">
                                        <div class="p-3 bg-yellow-100 rounded-lg text-yellow-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <h5 class="font-bold text-gray-900 text-lg">{{ $chambre->nomtype }}</h5>
                                                <span class="px-3 py-1 bg-yellow-600 text-white rounded-full text-sm font-bold">× {{ $chambre->quantite }}</span>
                                            </div>
                                            <div class="grid grid-cols-2 md:grid-cols-2 gap-3 text-sm text-gray-600 mb-2">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                                    </svg>
                                                    {{ $chambre->surface }} m²
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Max: {{ $chambre->capacitemax }} pers.
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 italic">"{{ $chambre->textepresentation }}"</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">Aucune chambre spécifiée.</p>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Détails des voyageurs ({{ $reservation->nbpersonnes }})
                        </h4>
                        @if($reservation->participants && $reservation->participants->count() > 0)
                            <div class="space-y-4">
                                @foreach($reservation->participants as $participant)
                                    <div class="bg-gradient-to-r from-gray-50 to-white border-l-4 border-blue-500 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                        {{-- En-tête du participant --}}
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold mr-3 text-lg shadow">
                                                    {{ substr($participant->prenomparticipant, 0, 1) }}{{ substr($participant->nomparticipant, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-lg text-gray-900">{{ $participant->prenomparticipant }} {{ $participant->nomparticipant }}</p>
                                                    <p class="text-sm text-gray-500">
                                                        <span class="inline-flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            Né(e) le {{ \Carbon\Carbon::parse($participant->datenaissanceparticipant)->format('d/m/Y') }}
                                                        </span>
                                                        <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold
                                                            {{ \Carbon\Carbon::parse($participant->datenaissanceparticipant)->age >= 15 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                                            {{ \Carbon\Carbon::parse($participant->datenaissanceparticipant)->age }} ans
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Transport du participant --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                                <div class="flex items-center mb-1">
                                                    <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                    </svg>
                                                    <span class="text-xs font-semibold text-gray-500 uppercase">Transport</span>
                                                </div>
                                                @if($participant->transport)
                                                    <p class="font-medium text-gray-800">{{ $participant->transport->nomtransport }}</p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ number_format($participant->transport->prixtransport, 2, ',', ' ') }} €
                                                    </p>
                                                @else
                                                    <p class="text-sm text-gray-500 italic">Non spécifié</p>
                                                @endif
                                            </div>

                                            {{-- Activités du participant --}}
                                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                                <div class="flex items-center mb-1">
                                                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span class="text-xs font-semibold text-gray-500 uppercase">Activités</span>
                                                </div>
                                                @if($participant->activites && $participant->activites->count() > 0)
                                                    <ul class="space-y-1">
                                                        @foreach($participant->activites as $activite)
                                                            <li class="flex items-start">
                                                                <span class="text-purple-500 mr-1">•</span>
                                                                <div class="flex-1">
                                                                    <p class="text-sm font-medium text-gray-800">{{ $activite->nomactivite }}</p>
                                                                    @if($activite->aLaCarte)
                                                                        <p class="text-xs text-gray-600">{{ number_format($activite->aLaCarte->prixmin, 2, ',', ' ') }} €</p>
                                                                    @endif
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-sm text-gray-500 italic">Aucune activité</p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Total pour ce participant --}}
                                        @php
                                            $prixTransport = $participant->transport ? $participant->transport->prixtransport : 0;
                                            $prixActivites = $participant->activites->sum(function($act) {
                                                return $act->aLaCarte ? $act->aLaCarte->prixmin : 0;
                                            });
                                            $totalParticipant = $prixTransport + $prixActivites;
                                        @endphp
                                        @if($totalParticipant > 0)
                                            <div class="mt-2 pt-2 border-t border-gray-200 flex justify-between items-center">
                                                <span class="text-sm font-semibold text-gray-600">Coûts supplémentaires :</span>
                                                <span class="text-base font-bold text-blue-600">{{ number_format($totalParticipant, 2, ',', ' ') }} €</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">Détails des participants non renseignés.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. ACTIVITÉS & SERVICES --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-700 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#ffc000]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activités & Options
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        {{-- Transport --}}
                        <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <span class="bg-blue-100 text-blue-700 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </span>
                                <div>
                                    <p class="font-bold text-gray-800">Transport</p>
                                    <p class="text-sm text-gray-500">{{ $reservation->transport->nomtransport ?? 'Sans transport' }}</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-700">
                                @if($reservation->transport && $reservation->transport->prixtransport > 0)
                                    {{ number_format($reservation->transport->prixtransport * $reservation->nbpersonnes, 2, ',', ' ') }} €
                                @else
                                    Inclus
                                @endif
                            </span>
                        </div>

                        {{-- Activités payantes --}}
                        @if($reservation->activites->count() > 0)
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mt-4 mb-2">Activités à la carte</p>
                            @foreach($reservation->activites as $activite)
                                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-[#ffc000] mr-3"></div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $activite->activite->nomactivite }}</p>
                                            <p class="text-xs text-gray-500">Quantité : {{ $activite->quantite }} x {{ number_format($activite->prix_unitaire, 2, ',', ' ') }} €</p>
                                        </div>
                                    </div>
                                    <span class="font-bold text-gray-700">{{ number_format($activite->prix_unitaire * $activite->quantite, 2, ',', ' ') }} €</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-gray-500 text-sm">
                                Aucune activité payante ajoutée.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- COLONNE DROITE (Finances & Actions) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                {{-- RECAPITULATIF FINANCIER --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-[#113559] px-6 py-4">
                        <h3 class="font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Récapitulatif financier
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        {{-- Prix hébergement de base --}}
                        @php
                            $totalTransports = $reservation->participants->sum(function($p) {
                                return $p->transport ? $p->transport->prixtransport : 0;
                            });
                            $totalActivites = $reservation->participants->sum(function($p) {
                                return $p->activites->sum(function($act) {
                                    return $act->aLaCarte ? $act->aLaCarte->prixmin : 0;
                                });
                            });
                            $prixHebergement = $reservation->prixtotal - $totalTransports - $totalActivites;
                        @endphp

                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-blue-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Hébergement
                                </span>
                                <span class="font-bold text-blue-900">{{ number_format($prixHebergement, 2, ',', ' ') }} €</span>
                            </div>
                            <p class="text-xs text-blue-700">{{ $reservation->nbpersonnes }} pers. × {{ \Carbon\Carbon::parse($reservation->datedebut)->diffInDays(\Carbon\Carbon::parse($reservation->datefin)) }} nuits</p>
                        </div>

                        {{-- Détail transports --}}
                        @if($totalTransports > 0)
                        <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-orange-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Transports
                                </span>
                                <span class="font-bold text-orange-900">{{ number_format($totalTransports, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="space-y-1">
                                @foreach($reservation->participants as $participant)
                                    @if($participant->transport && $participant->transport->prixtransport > 0)
                                        <div class="flex justify-between text-xs text-orange-800">
                                            <span>• {{ $participant->prenomparticipant }}</span>
                                            <span>{{ number_format($participant->transport->prixtransport, 2, ',', ' ') }} €</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Détail activités --}}
                        @if($totalActivites > 0)
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-purple-900 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Activités à la carte
                                </span>
                                <span class="font-bold text-purple-900">{{ number_format($totalActivites, 2, ',', ' ') }} €</span>
                            </div>
                            <div class="space-y-1">
                                @foreach($reservation->participants as $participant)
                                    @if($participant->activites && $participant->activites->count() > 0)
                                        @php
                                            $totalActParticipant = $participant->activites->sum(function($act) {
                                                return $act->aLaCarte ? $act->aLaCarte->prixmin : 0;
                                            });
                                        @endphp
                                        @if($totalActParticipant > 0)
                                            <div class="flex justify-between text-xs text-purple-800">
                                                <span>• {{ $participant->prenomparticipant }} ({{ $participant->activites->count() }})</span>
                                                <span>{{ number_format($totalActParticipant, 2, ',', ' ') }} €</span>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Total général --}}
                        <div class="border-t-2 border-dashed border-gray-300 pt-4 mt-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-gray-800 text-base">Total TTC</span>
                                <span class="font-bold text-[#113559] text-2xl">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</span>
                            </div>
                            <p class="text-xs text-right text-gray-400">TVA incluse • Tous services</p>
                        </div>
                    </div>

                    {{-- Section Paiements (Historique) --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Historique des règlements</h4>
                        @if($reservation->paiements && $reservation->paiements->count() > 0)
                            <ul class="space-y-2">
                                @foreach($reservation->paiements as $paiement)
                                    <li class="flex justify-between text-sm">
                                        <span class="text-gray-600"><span class="text-green-600">✔</span> Le {{ \Carbon\Carbon::parse($paiement->datepaiement)->format('d/m/Y') }}</span>
                                        <span class="font-bold text-gray-700">{{ number_format($paiement->montantpaiement, 2, ',', ' ') }} €</span>
                                    </li>
                                @endforeach
                            </ul>
                            {{-- Solde restant --}}
                            @php
                                $totalPaye = $reservation->paiements->sum('montantpaiement');
                                $reste = $reservation->prixtotal - $totalPaye;
                            @endphp
                            @if($reste > 0)
                                <div class="mt-3 pt-2 border-t border-gray-200 flex justify-between text-sm font-bold text-red-600">
                                    <span>Reste à payer</span>
                                    <span>{{ number_format($reste, 2, ',', ' ') }} €</span>
                                </div>
                            @else
                                <div class="mt-3 text-center text-xs font-bold text-green-600 bg-green-100 py-1 rounded">
                                    Réservation soldée
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500 italic">Aucun paiement enregistré.</p>
                        @endif
                    </div>
                </div>

                {{-- Actions Rapides --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h4 class="font-bold text-gray-800 mb-4">Besoin d'aide ?</h4>
                    <div class="space-y-3">
                        <a href="#" class="block w-full text-center py-2 px-4 border border-[#113559] text-[#113559] rounded-lg hover:bg-blue-50 transition-colors text-sm font-semibold">
                            Contacter le service client
                        </a>
                        <a href="{{ route('reservation.activities', $reservation->numreservation) }}" class="block w-full text-center py-2 px-4 bg-[#ffc000] text-[#113559] rounded-lg hover:bg-[#e0a800] transition-colors text-sm font-bold shadow-sm">
                            Ajouter des activités
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection