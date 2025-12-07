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
                    <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-sm">2</div>
                    <span class="ml-2 font-semibold text-orange-500 hidden sm:inline">Transport</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">3</div>
                    <span class="ml-2 text-gray-400 hidden sm:inline">Activités</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('reservation.step3', $resort->numresort) }}" method="GET" id="transportForm">
            <input type="hidden" name="dateDebut" value="{{ $dateDebut }}">
            <input type="hidden" name="dateFin" value="{{ $dateFin }}">
            <input type="hidden" name="numtype" value="{{ $numtype }}">
            <input type="hidden" name="nbAdultes" value="{{ $nbAdultes }}">
            <input type="hidden" name="nbEnfants" value="{{ $nbEnfants }}">
            <input type="hidden" name="numtransport" id="numtransport" value="{{ $numtransport ?? '' }}">
            <input type="hidden" name="lieuDepart" id="lieuDepartInput" value="">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Section choix transport -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Comment souhaitez-vous voyager ?
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <!-- Option Sans Transport -->
                            <div class="transport-option border-2 border-orange-500 bg-orange-50 rounded-xl p-5 cursor-pointer transition-all hover:border-orange-300 hover:shadow-md"
                                 onclick="selectTransportOption('sans')" id="option-sans">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-800">Je m'organise moi-même</h3>
                                            <p class="text-gray-500 text-sm">Vous gérez votre propre transport jusqu'au resort</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold">Inclus</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Option Avec Transport -->
                            <div class="transport-option border-2 border-gray-200 rounded-xl p-5 cursor-pointer transition-all hover:border-orange-300 hover:shadow-md"
                                 onclick="selectTransportOption('avec')" id="option-avec">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-800">Avec transport inclus</h3>
                                            <p class="text-gray-500 text-sm">Choisissez votre mode de transport et lieu de départ</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-orange-500 font-bold" id="prix-indication">À partir de {{ number_format($transports->where('prixtransport', '>', 0)->min('prixtransport') ?? 50, 0, ',', ' ') }} €/pers</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section détails transport (cachée par défaut) -->
                    <div id="transport-details" class="hidden space-y-6">
                        <!-- Choix du lieu de départ -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    D'où partez-vous ?
                                </h2>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="lieux-depart">
                                    @php
                                        $lieuxDepart = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Bordeaux', 'Nantes', 'Strasbourg', 'Lille'];
                                    @endphp
                                    @foreach($lieuxDepart as $index => $lieu)
                                        <div class="lieu-option border-2 {{ $index === 0 ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }} rounded-xl p-4 cursor-pointer transition-all hover:border-blue-300 hover:bg-blue-50 text-center"
                                             onclick="selectLieu('{{ $lieu }}')" id="lieu-{{ Str::slug($lieu) }}">
                                            <svg class="w-6 h-6 mx-auto mb-2 {{ $index === 0 ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                            <span class="font-medium {{ $index === 0 ? 'text-blue-700' : 'text-gray-700' }}">{{ $lieu }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Choix du type de transport -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                                <h2 class="text-xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Choisissez votre transport
                                </h2>
                            </div>
                            
                            <div class="p-6 space-y-4" id="transports-list">
                                @foreach($transports as $index => $transport)
                                    @if($transport->prixtransport > 0)
                                    @php
                                        $totalVoyageurs = $nbAdultes + $nbEnfants;
                                        $prixTotal = $transport->prixtransport * $totalVoyageurs;
                                    @endphp
                                    <div class="type-transport-option border-2 {{ $loop->first ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }} rounded-xl p-5 cursor-pointer transition-all hover:border-purple-300 hover:shadow-md"
                                         onclick="selectTypeTransport({{ $transport->numtransport }}, {{ $transport->prixtransport }}, '{{ $transport->nomtransport }}')" 
                                         id="transport-{{ $transport->numtransport }}"
                                         data-prix="{{ $transport->prixtransport }}"
                                         data-nom="{{ $transport->nomtransport }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-5">
                                                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                                                    @if(str_contains(strtolower($transport->nomtransport), 'avion'))
                                                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                        </svg>
                                                    @elseif(str_contains(strtolower($transport->nomtransport), 'train'))
                                                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="ml-2">
                                                    <h3 class="font-bold text-lg text-gray-800">{{ $transport->nomtransport }}</h3>
                                                    <p class="text-gray-500 text-sm">Départ de <span class="lieu-depart-affiche font-medium">Paris</span></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-purple-600">{{ number_format($transport->prixtransport, 0, ',', ' ') }} €</div>
                                                <div class="text-sm text-gray-500">par personne</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Détail prix par voyageur -->
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex flex-wrap justify-between items-center text-sm gap-2">
                                                <div class="flex flex-wrap items-center gap-4">
                                                    <div class="flex items-center text-gray-600">
                                                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                        </svg>
                                                        <span><strong>{{ $nbAdultes }}</strong> adulte(s) × {{ number_format($transport->prixtransport, 0, ',', ' ') }} € = <strong>{{ number_format($transport->prixtransport * $nbAdultes, 0, ',', ' ') }} €</strong></span>
                                                    </div>
                                                    @if($nbEnfants > 0)
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span><strong>{{ $nbEnfants }}</strong> enfant(s) × {{ number_format($transport->prixtransport, 0, ',', ' ') }} € = <strong>{{ number_format($transport->prixtransport * $nbEnfants, 0, ',', ' ') }} €</strong></span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-bold">
                                                    Total : {{ number_format($prixTotal, 0, ',', ' ') }} €
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Colonne récapitulative -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-4">
                        <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">Récapitulatif</h3>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Dates -->
                            <div class="flex items-start space-x-5 pb-2">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Dates du séjour</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
                                    @php
                                        $nuits = \Carbon\Carbon::parse($dateDebut)->diffInDays(\Carbon\Carbon::parse($dateFin));
                                    @endphp
                                    <p class="text-sm text-orange-500 font-medium">{{ $nuits }} nuit(s)</p>
                                </div>
                            </div>

                            <!-- Voyageurs -->
                            <div class="flex items-start space-x-5 pb-2">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 ml-2">
                                    <p class="text-sm text-gray-500 mb-2">Voyageurs</p>
                                    <div class="grid grid-cols-2 gap-3 mt-2">
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
                            <div class="flex items-start space-x-5 pb-2">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Hébergement</p>
                                    <p class="font-semibold text-gray-800">{{ $typeChambre->nomtype ?? 'Non sélectionné' }}</p>
                                </div>
                            </div>

                            <!-- Transport -->
                            <div class="flex items-start space-x-5 pb-2">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Transport</p>
                                    <p class="font-semibold text-gray-800" id="recap-transport">Sans transport</p>
                                    <p class="text-sm text-gray-500" id="recap-lieu-depart"></p>
                                </div>
                            </div>

                            <div class="border-t pt-5 mt-5">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Prix transport</span>
                                    <span class="font-bold text-gray-800" id="recap-prix-transport">Inclus</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="p-6 bg-gray-50 border-t space-y-3">
                            <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 px-6 rounded-xl font-bold text-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                Continuer
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <a href="{{ route('reservation.step1', ['numresort' => $resort->numresort, 'dateDebut' => $dateDebut, 'dateFin' => $dateFin, 'numtype' => $numtype, 'nbAdultes' => $nbAdultes, 'nbEnfants' => $nbEnfants]) }}" 
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
    let transportOption = 'sans';
    let selectedTransport = null;
    let selectedTransportPrix = 0;
    let selectedTransportNom = '';
    let selectedLieu = 'Paris';
    const nbAdultes = {{ $nbAdultes }};
    const nbEnfants = {{ $nbEnfants }};
    const totalVoyageurs = nbAdultes + nbEnfants;

    function selectTransportOption(option) {
        transportOption = option;
        
        // Reset styles
        document.querySelectorAll('.transport-option').forEach(el => {
            el.classList.remove('border-orange-500', 'bg-orange-50');
            el.classList.add('border-gray-200');
        });
        
        // Apply selected style
        const selectedEl = document.getElementById('option-' + option);
        selectedEl.classList.remove('border-gray-200');
        selectedEl.classList.add('border-orange-500', 'bg-orange-50');
        
        // Show/hide transport details
        const detailsEl = document.getElementById('transport-details');
        if (option === 'avec') {
            detailsEl.classList.remove('hidden');
            // Select first transport by default
            @if($transports->where('prixtransport', '>', 0)->count() > 0)
                if (!selectedTransport) {
                    @php $firstTransport = $transports->where('prixtransport', '>', 0)->first(); @endphp
                    selectTypeTransport({{ $firstTransport->numtransport }}, {{ $firstTransport->prixtransport }}, '{{ $firstTransport->nomtransport }}');
                }
            @endif
        } else {
            detailsEl.classList.add('hidden');
            selectedTransport = null;
            selectedTransportPrix = 0;
            selectedTransportNom = '';
            document.getElementById('numtransport').value = '';
            document.getElementById('lieuDepartInput').value = '';
            updateRecap();
        }
    }

    function selectLieu(lieu) {
        selectedLieu = lieu;
        
        // Reset styles
        document.querySelectorAll('.lieu-option').forEach(el => {
            el.classList.remove('border-blue-500', 'bg-blue-50');
            el.classList.add('border-gray-200');
            el.querySelector('svg').classList.remove('text-blue-500');
            el.querySelector('svg').classList.add('text-gray-400');
            el.querySelector('span').classList.remove('text-blue-700');
            el.querySelector('span').classList.add('text-gray-700');
        });
        
        // Apply selected style
        const slug = lieu.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, '-');
        const selectedEl = document.getElementById('lieu-' + slug);
        if (selectedEl) {
            selectedEl.classList.remove('border-gray-200');
            selectedEl.classList.add('border-blue-500', 'bg-blue-50');
            selectedEl.querySelector('svg').classList.remove('text-gray-400');
            selectedEl.querySelector('svg').classList.add('text-blue-500');
            selectedEl.querySelector('span').classList.remove('text-gray-700');
            selectedEl.querySelector('span').classList.add('text-blue-700');
        }
        
        // Update lieu input
        document.getElementById('lieuDepartInput').value = lieu;
        
        // Update display in transport cards
        document.querySelectorAll('.lieu-depart-affiche').forEach(el => {
            el.textContent = lieu;
        });
        
        updateRecap();
    }

    function selectTypeTransport(numtransport, prix, nom) {
        selectedTransport = numtransport;
        selectedTransportPrix = prix;
        selectedTransportNom = nom;
        
        // Reset styles
        document.querySelectorAll('.type-transport-option').forEach(el => {
            el.classList.remove('border-purple-500', 'bg-purple-50');
            el.classList.add('border-gray-200');
        });
        
        // Apply selected style
        const selectedEl = document.getElementById('transport-' + numtransport);
        selectedEl.classList.remove('border-gray-200');
        selectedEl.classList.add('border-purple-500', 'bg-purple-50');
        
        // Update hidden input
        document.getElementById('numtransport').value = numtransport;
        document.getElementById('lieuDepartInput').value = selectedLieu;
        
        updateRecap();
    }

    function updateRecap() {
        const recapTransport = document.getElementById('recap-transport');
        const recapLieuDepart = document.getElementById('recap-lieu-depart');
        const recapPrixTransport = document.getElementById('recap-prix-transport');
        
        if (transportOption === 'sans') {
            recapTransport.textContent = 'Sans transport';
            recapLieuDepart.textContent = '';
            recapPrixTransport.textContent = 'Inclus';
        } else {
            recapTransport.textContent = selectedTransportNom;
            recapLieuDepart.textContent = 'Départ de ' + selectedLieu;
            const totalPrix = selectedTransportPrix * totalVoyageurs;
            recapPrixTransport.textContent = new Intl.NumberFormat('fr-FR').format(totalPrix) + ' €';
        }
    }

    // Initialize - check if transport was pre-selected
    document.addEventListener('DOMContentLoaded', function() {
        @if($numtransport)
            // Pre-select the transport option
            @php 
                $selectedTransport = $transports->firstWhere('numtransport', $numtransport);
            @endphp
            @if($selectedTransport)
                selectTransportOption('avec');
                selectTypeTransport({{ $selectedTransport->numtransport }}, {{ $selectedTransport->prixtransport }}, '{{ $selectedTransport->nomtransport }}');
            @endif
        @endif
    });
</script>
@endsection
