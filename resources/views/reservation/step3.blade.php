@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-orange-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white">
        <div class="container mx-auto px-4 py-6">
            <nav class="flex items-center text-white/80 text-sm mb-2">
                <a href="/" class="hover:text-white">Accueil</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('resort.show', $resort->numresort) }}" class="hover:text-white">{{ $resort->nomresort }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white font-medium">Réservation</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold">{{ $resort->nomresort }}</h1>
            @if($resort->pays)
                <p class="text-white/90 flex items-center mt-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $resort->pays->nompays }}
                </p>
            @endif
        </div>
    </div>

    <!-- Étapes de progression -->
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-center space-x-4 md:space-x-8">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="ml-2 font-semibold text-green-500 hidden sm:inline">Séjour</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="ml-2 font-semibold text-green-500 hidden sm:inline">Transport</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-green-500"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-sm">3</div>
                    <span class="ml-2 font-semibold text-orange-500 hidden sm:inline">Activités</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('reservation.addToCart', $resort->numresort) }}" method="POST" id="step3Form">
            @csrf
            <input type="hidden" name="dateDebut" value="{{ $dateDebut }}">
            <input type="hidden" name="dateFin" value="{{ $dateFin }}">
            @foreach($chambres as $numtype => $qty)
                @if($qty > 0)
                    <input type="hidden" name="chambres[{{ $numtype }}]" value="{{ $qty }}">
                @endif
            @endforeach
            @foreach($transportsParticipants as $key => $numtransport)
                <input type="hidden" name="transports[{{ $key }}]" value="{{ $numtransport }}">
            @endforeach
            <input type="hidden" name="nbAdultes" value="{{ $nbAdultes }}">
            <input type="hidden" name="nbEnfants" value="{{ $nbEnfants }}">
            
            {{-- Passer les informations des participants --}}
            @foreach($participants as $key => $participant)
                <input type="hidden" name="participants[{{ $key }}][nom]" value="{{ $participant['nom'] ?? '' }}">
                <input type="hidden" name="participants[{{ $key }}][prenom]" value="{{ $participant['prenom'] ?? '' }}">
                <input type="hidden" name="participants[{{ $key }}][genre]" value="{{ $participant['genre'] ?? '' }}">
                <input type="hidden" name="participants[{{ $key }}][datenaissance]" value="{{ $participant['datenaissance'] ?? '' }}">
            @endforeach

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Section Activités -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activités optionnelles
                            </h2>
                            <p class="text-white/80 text-sm mt-1">Sélectionnez les participants pour chaque activité</p>
                        </div>
                        
                        <div class="p-6">
                            @if($activites->count() > 0)
                                <div class="space-y-6">
                                    @foreach($activites as $activite)
                                        <div class="border-2 border-gray-200 rounded-xl p-5">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex items-start space-x-4">
                                                    <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-lg text-gray-800">{{ $activite->nomactivite }}</h3>
                                                        <p class="text-gray-500 text-sm mt-1">{{ $activite->descriptionactivite }}</p>
                                                        <div class="mt-2">
                                                            <span class="text-2xl font-bold text-orange-600">{{ number_format($activite->prixmin, 0, ',', ' ') }} €</span>
                                                            <span class="text-sm text-gray-500"> / personne</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Sélection des participants -->
                                            <div class="border-t border-gray-200 pt-4 mt-4">
                                                <p class="text-sm font-medium text-gray-700 mb-3">Qui participe à cette activité ?</p>
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                    @for($i = 1; $i <= $nbAdultes; $i++)
                                                        @php
                                                            $participantKey = 'adulte_' . $i;
                                                            $participantInfo = $participants[$participantKey] ?? [];
                                                            $nom = $participantInfo['nom'] ?? '';
                                                            $prenom = $participantInfo['prenom'] ?? '';
                                                            $displayName = trim($prenom . ' ' . $nom) ?: "Adulte $i";
                                                        @endphp
                                                        <label class="flex items-center space-x-2 cursor-pointer group">
                                                            <input type="checkbox" 
                                                                   name="activites[{{ $activite->numactivite }}][]" 
                                                                   value="adulte_{{ $i }}" 
                                                                   class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                                                                   data-prix="{{ $activite->prixmin }}"
                                                                   data-activite="{{ $activite->numactivite }}">
                                                            <span class="text-sm text-gray-700 group-hover:text-orange-600">
                                                                <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                                {{ $displayName }}
                                                            </span>
                                                        </label>
                                                    @endfor
                                                    
                                                    @for($i = 1; $i <= $nbEnfants; $i++)
                                                        @php
                                                            $participantKey = 'enfant_' . $i;
                                                            $participantInfo = $participants[$participantKey] ?? [];
                                                            $nom = $participantInfo['nom'] ?? '';
                                                            $prenom = $participantInfo['prenom'] ?? '';
                                                            $displayName = trim($prenom . ' ' . $nom) ?: "Enfant $i";
                                                        @endphp
                                                        <label class="flex items-center space-x-2 cursor-pointer group">
                                                            <input type="checkbox" 
                                                                   name="activites[{{ $activite->numactivite }}][]" 
                                                                   value="enfant_{{ $i }}" 
                                                                   class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                                                                   data-prix="{{ $activite->prixmin }}"
                                                                   data-activite="{{ $activite->numactivite }}">
                                                            <span class="text-sm text-gray-700 group-hover:text-orange-600">
                                                                <svg class="w-4 h-4 inline mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                {{ $displayName }}
                                                            </span>
                                                        </label>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucune activité disponible</h3>
                                    <p class="text-gray-500">Ce resort ne propose pas d'activités optionnelles pour le moment.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Colonne récapitulative -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-4">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Récapitulatif complet</h3>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Resort -->
                            <div class="flex items-start space-x-5">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Resort</p>
                                    <p class="font-semibold text-gray-800">{{ $resort->nomresort }}</p>
                                    <p class="text-sm text-gray-500">{{ $resort->pays->nompays ?? '' }}</p>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="flex items-start space-x-5">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Dates du séjour</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
                                    <p class="text-sm text-orange-500 font-medium">{{ $nbNuits }} nuit(s)</p>
                                </div>
                            </div>

                            <!-- Voyageurs -->
                            <div class="flex items-start space-x-5">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 ml-2">
                                    <p class="text-sm text-gray-500 mb-2">Voyageurs</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                            <span class="text-lg font-bold text-gray-800">{{ $nbAdultes }}</span>
                                            <p class="text-xs text-gray-500">Adultes</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                            <span class="text-lg font-bold text-gray-800">{{ $nbEnfants }}</span>
                                            <p class="text-xs text-gray-500">Enfants</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hébergement -->
                            <div class="flex items-start space-x-5">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Hébergement</p>
                                    @foreach($chambres as $numtype => $qty)
                                        @if($qty > 0)
                                            @php
                                                $typeChambre = \App\Models\Typechambre::find($numtype);
                                            @endphp
                                            <p class="font-semibold text-gray-800">{{ $qty }}x {{ $typeChambre->nomtype ?? 'Chambre' }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Transport -->
                            <div class="flex items-start space-x-5">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Transport</p>
                                    <p class="font-semibold text-gray-800">{{ $nbAdultes + $nbEnfants }} voyageur(s)</p>
                                </div>
                            </div>

                            <!-- Séparateur -->
                            <div class="border-t border-gray-200"></div>

                            <!-- Détail des prix -->
                            <div class="space-y-3">
                                <h4 class="font-bold text-gray-800">Détail des prix</h4>
                                
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Hébergement ({{ $nbNuits }} nuits)</span>
                                    <span class="font-medium text-gray-800">{{ number_format($prixChambre, 0, ',', ' ') }} €</span>
                                </div>
                                
                                @if($prixTransport > 0)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Transport ({{ $nbAdultes + $nbEnfants }} pers.)</span>
                                    <span class="font-medium text-gray-800">{{ number_format($prixTransport, 0, ',', ' ') }} €</span>
                                </div>
                                @endif
                                
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Activités</span>
                                    <span class="font-medium text-gray-800" id="prixActivites">0 €</span>
                                </div>
                                
                                <div class="border-t border-dashed border-gray-200 pt-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600">Sous-total</span>
                                        <span class="font-medium text-gray-800" id="sousTotal">{{ number_format($prixChambre + $prixTransport, 0, ',', ' ') }} €</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm mt-1">
                                        <span class="text-gray-600">TVA (20%)</span>
                                        <span class="font-medium text-gray-800" id="tva">{{ number_format(($prixChambre + $prixTransport) * 0.2, 0, ',', ' ') }} €</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 -mx-6 -mb-6 px-6 py-5">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-white/80 text-sm">Prix total</p>
                                        <p class="text-3xl font-bold text-white" id="prixTotal">{{ number_format(($prixChambre + $prixTransport) * 1.2, 0, ',', ' ') }} €</p>
                                    </div>
                                    <div class="text-right text-white/80 text-sm">
                                        <p>soit <span class="font-bold text-white" id="prixParPersonne">{{ number_format((($prixChambre + $prixTransport) * 1.2) / ($nbAdultes + $nbEnfants), 0, ',', ' ') }} €</span></p>
                                        <p>par personne</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="p-6 bg-gray-50 border-t space-y-3">
                            @auth
                                <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span id="submitBtnText">Ajouter au panier</span>
                                    <svg id="submitBtnSpinner" class="hidden w-6 h-6 ml-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                </button>
                            @endauth
                            @guest
                                <button type="button" id="open-login-modal" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Ajouter au panier
                                </button>
                            @endguest
                            <a href="{{ route('reservation.step2', $resort->numresort) }}" 
                               class="block w-full text-center border-2 border-gray-300 text-gray-600 py-3 px-6 rounded-xl font-semibold hover:bg-gray-100 transition-all">
                                ← Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const prixChambre = {{ $prixChambre }};
    const prixTransport = {{ $prixTransport }};
    const nbAdultes = {{ $nbAdultes }};
    const nbEnfants = {{ $nbEnfants }};
    const nbPersonnes = nbAdultes + nbEnfants;
    let totalActivites = 0;

    function updatePrix() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-prix]');
        totalActivites = 0;
        
        checkboxes.forEach(cb => {
            if (cb.checked) {
                totalActivites += parseFloat(cb.dataset.prix);
            }
        });
        
        const sousTotal = prixChambre + prixTransport + totalActivites;
        const tvaVal = sousTotal * 0.2;
        const total = sousTotal + tvaVal;
        const parPersonne = total / nbPersonnes;
        
        document.getElementById('prixActivites').textContent = new Intl.NumberFormat('fr-FR').format(totalActivites) + ' €';
        document.getElementById('sousTotal').textContent = new Intl.NumberFormat('fr-FR').format(sousTotal) + ' €';
        document.getElementById('tva').textContent = new Intl.NumberFormat('fr-FR').format(tvaVal) + ' €';
        document.getElementById('prixTotal').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' €';
        document.getElementById('prixParPersonne').textContent = new Intl.NumberFormat('fr-FR').format(Math.round(parPersonne)) + ' €';
    }
    
    // Ajouter des écouteurs sur toutes les checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"][data-prix]');
        allCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePrix);
        });
        
        updatePrix(); // Calcul initial
        
        // Empêcher la double soumission
        const form = document.getElementById('step3Form');
        const submitBtn = document.getElementById('submitBtn');
        const submitBtnText = document.getElementById('submitBtnText');
        const submitBtnSpinner = document.getElementById('submitBtnSpinner');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                if (submitBtn.disabled) {
                    e.preventDefault();
                    return false;
                }
                
                submitBtn.disabled = true;
                if (submitBtnText) submitBtnText.textContent = 'Ajout en cours...';
                if (submitBtnSpinner) submitBtnSpinner.classList.remove('hidden');
            });
        }
        
        // Gestion du modal de connexion pour les invités
        const openLoginBtn = document.getElementById('open-login-modal');
        const closeLoginBtn = document.getElementById('close-login-modal');
        const loginOverlay = document.getElementById('login-modal-overlay');

        if (openLoginBtn && closeLoginBtn && loginOverlay) {
            openLoginBtn.addEventListener('click', function() {
                // Sauvegarder les données du formulaire en session avant d'ouvrir le modal
                const formData = new FormData(document.getElementById('step3Form'));
                
                // Ajouter les activités cochées
                const checkboxes = document.querySelectorAll('input[type="checkbox"][data-activite]:checked');
                checkboxes.forEach(cb => {
                    formData.append('activites[' + cb.dataset.activite + '][]', cb.value);
                });
                
                fetch('{{ route("reservation.saveToSession", $resort->numresort) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loginOverlay.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                }).catch(error => {
                    console.error('Erreur:', error);
                    loginOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            });
            closeLoginBtn.addEventListener('click', function() {
                loginOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
            loginOverlay.addEventListener('click', function(e) {
                if (e.target === loginOverlay) {
                    loginOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>

{{-- MODAL CONNEXION pour les invités --}}
@guest
    <div id="login-modal-overlay" class="fixed inset-0 bg-black/40 flex items-center justify-center z-40 hidden">
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <button id="close-login-modal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-100">✕</button>
            <div class="px-8 pt-10 pb-8">
                <h2 class="text-center text-xl font-semibold text-[#b46320] mb-4">Déjà client ?</h2>
                <p class="text-center text-gray-600 mb-6">Connectez-vous pour ajouter au panier</p>
                <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="block w-full text-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm rounded-full shadow-md transition">SE CONNECTER</a>
                <div class="mt-4 text-center">
                    <span class="text-gray-500 text-sm">Pas encore de compte ?</span>
                    <a href="{{ route('register', ['redirect_to' => url()->current()]) }}" class="text-orange-500 hover:text-orange-600 font-semibold text-sm ml-1">S'inscrire</a>
                </div>
            </div>
        </div>
    </div>
@endguest
@endsection
