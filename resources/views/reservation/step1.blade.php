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
                    <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-sm">1</div>
                    <span class="ml-2 font-semibold text-orange-500 hidden sm:inline">Séjour</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">2</div>
                    <span class="ml-2 text-gray-400 hidden sm:inline">Transport</span>
                </div>
                <div class="w-8 md:w-16 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-300 text-gray-500 flex items-center justify-center font-bold text-sm">3</div>
                    <span class="ml-2 text-gray-400 hidden sm:inline">Activités</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <form id="step1Form" action="{{ route('reservation.step2', $resort->numresort) }}" method="GET">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Formulaire principal (gauche) -->
                <div class="flex-1 space-y-6">
                    
                    <!-- Section Dates -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            Dates du séjour
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date d'arrivée</label>
                                <input type="date" name="dateDebut" id="dateDebut" required 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ request('dateDebut') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de départ</label>
                                <input type="date" name="dateFin" id="dateFin" required 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ request('dateFin') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Section Voyageurs -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </span>
                            Voyageurs
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Adultes -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Adultes</p>
                                        <p class="text-sm text-gray-500">12 ans et plus</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="changeValue('nbAdultes', -1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500 transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="nbAdultes" id="nbAdultes" value="{{ request('nbAdultes', 2) }}" min="1" max="10" required 
                                               class="w-14 text-center text-xl font-bold border-0 bg-transparent focus:ring-0">
                                        <button type="button" onclick="changeValue('nbAdultes', 1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500 transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Enfants -->
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Enfants</p>
                                        <p class="text-sm text-gray-500">2 à 11 ans</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="changeValue('nbEnfants', -1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500 transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="nbEnfants" id="nbEnfants" value="{{ request('nbEnfants', 0) }}" min="0" max="10" required 
                                               class="w-14 text-center text-xl font-bold border-0 bg-transparent focus:ring-0">
                                        <button type="button" onclick="changeValue('nbEnfants', 1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-orange-500 hover:text-orange-500 transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Chambres -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </span>
                            Type d'hébergement
                        </h2>
                        <div class="space-y-4" id="chambres-container">
                            @foreach($typeChambres as $index => $type)
                                @php
                                    $isSelected = request('numtype') ? (request('numtype') == $type->numtype) : ($index === 0);
                                @endphp
                                <div class="chambre-card cursor-pointer rounded-xl border-2 border-gray-200 transition-all duration-200 hover:border-gray-300 hover:shadow-sm {{ $isSelected ? 'selected border-orange-500 bg-orange-50 shadow-md' : '' }}"
                                     data-numtype="{{ $type->numtype }}" onclick="selectChambre(this, {{ $type->numtype }})">
                                    <input type="radio" name="numtype" value="{{ $type->numtype }}" 
                                           id="chambre-{{ $type->numtype }}" class="hidden" required {{ $isSelected ? 'checked' : '' }}>
                                    <div class="p-5">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-800 text-lg">{{ $type->nomtype }}</h3>
                                                <div class="mt-2 flex flex-wrap gap-3">
                                                    @if($type->surface)
                                                        <span class="inline-flex items-center text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                                            </svg>
                                                            {{ $type->surface }} m²
                                                        </span>
                                                    @endif
                                                    @if($type->capacitemax)
                                                        <span class="inline-flex items-center text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            </svg>
                                                            Max {{ $type->capacitemax }} pers.
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <div class="text-2xl font-bold text-orange-500" id="prix-{{ $type->numtype }}">--</div>
                                                <div class="text-sm text-gray-500">€ / nuit</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif (droite) -->
                <div class="lg:w-96">
                    <div class="sticky top-4">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                            <!-- Header du récap -->
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-5 text-white">
                                <h3 class="font-bold text-lg">Récapitulatif</h3>
                                <p class="text-orange-100 text-sm">{{ $resort->nomresort }}</p>
                            </div>
                            
                            <!-- Contenu du récap -->
                            <div class="p-5 space-y-4">
                                <!-- Dates -->
                                <div class="flex items-center text-gray-600" id="recap-dates">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-400 italic">Sélectionnez vos dates</span>
                                </div>
                                
                                <!-- Nuits -->
                                <div class="flex items-center justify-between py-3 border-t border-gray-100">
                                    <span class="text-gray-600">Nombre de nuits</span>
                                    <span class="font-bold text-gray-800" id="recap-nuits">--</span>
                                </div>

                                <!-- Voyageurs -->
                                <div class="py-3 border-t border-gray-100">
                                    <div class="flex items-center text-gray-600 mb-2">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Voyageurs
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 ml-7">
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                            <span class="text-lg font-bold text-gray-800" id="recap-adultes">{{ request('nbAdultes', 2) }}</span>
                                            <p class="text-xs text-gray-500">Adultes</p>
                                        </div>
                                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-center">
                                            <span class="text-lg font-bold text-gray-800" id="recap-enfants">{{ request('nbEnfants', 0) }}</span>
                                            <p class="text-xs text-gray-500">Enfants</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chambre sélectionnée -->
                                <div class="py-3 border-t border-gray-100">
                                    <div class="flex items-center text-gray-600 mb-1">
                                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        Hébergement
                                    </div>
                                    <p class="font-semibold text-gray-800 ml-7" id="recap-chambre">{{ $typeChambres->first()->nomtype ?? '--' }}</p>
                                    <p class="text-sm text-orange-600 ml-7 mt-1 hidden" id="recap-nb-chambres"></p>
                                </div>
                                <input type="hidden" name="nbChambres" id="nbChambres" value="1">
                            </div>

                            <!-- Section Prix -->
                            <div class="bg-gray-50 p-5 border-t border-gray-100">
                                <!-- Message si pas de dates -->
                                <div id="prix-placeholder" class="text-center py-4">
                                    <p class="text-gray-400 text-sm">Sélectionnez vos dates pour voir le prix</p>
                                </div>
                                
                                <!-- Détail des prix (caché par défaut) -->
                                <div id="prix-detail" class="hidden">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-gray-600">Total</span>
                                        <span class="text-2xl font-bold text-orange-500" id="recap-total">--</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>Par personne</span>
                                        <span id="recap-prix-personne">--</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton -->
                            <div class="p-5 border-t border-gray-100">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl font-bold text-lg
                                               hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl
                                               transform hover:-translate-y-0.5 flex items-center justify-center">
                                    Continuer
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Badge réassurance -->
                        <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Annulation gratuite jusqu'à 48h avant
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Capacités des chambres
const capaciteChambres = {
    @foreach($typeChambres as $type)
        {{ $type->numtype }}: {{ $type->capacitemax ?? 2 }},
    @endforeach
};

// Noms des chambres
const nomsChambres = {
    @foreach($typeChambres as $type)
        {{ $type->numtype }}: "{{ $type->nomtype }}",
    @endforeach
};

document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('dateDebut');
    const dateFin = document.getElementById('dateFin');
    const nbAdultes = document.getElementById('nbAdultes');
    const nbEnfants = document.getElementById('nbEnfants');
    const nbChambresInput = document.getElementById('nbChambres');
    const radios = document.querySelectorAll('input[name="numtype"]');
    
    function changeValue(id, delta) {
        const input = document.getElementById(id);
        const min = parseInt(input.min) || 0;
        const max = parseInt(input.max) || 99;
        let newVal = parseInt(input.value) + delta;
        if (newVal >= min && newVal <= max) {
            input.value = newVal;
            updateRecap();
            updatePrix();
        }
    }
    window.changeValue = changeValue;
    
    // Sélection de chambre
    function selectChambre(card, numtype) {
        // Retirer la sélection de toutes les cartes
        document.querySelectorAll('.chambre-card').forEach(c => {
            c.classList.remove('selected', 'border-orange-500', 'bg-orange-50', 'shadow-md');
            c.classList.add('border-gray-200');
        });
        
        // Ajouter la sélection à la carte cliquée
        card.classList.add('selected', 'border-orange-500', 'bg-orange-50', 'shadow-md');
        card.classList.remove('border-gray-200');
        
        // Cocher le radio button
        document.getElementById('chambre-' + numtype).checked = true;
        
        updateRecap();
        updatePrix();
    }
    window.selectChambre = selectChambre;
    
    // Calcul du nombre de chambres nécessaires
    function calculerNbChambres() {
        const selectedType = document.querySelector('input[name="numtype"]:checked');
        if (!selectedType) return 1;
        
        const numtype = parseInt(selectedType.value);
        const capacite = capaciteChambres[numtype] || 2;
        const adultes = parseInt(nbAdultes.value) || 0;
        const enfants = parseInt(nbEnfants.value) || 0;
        const totalPersonnes = adultes + enfants;
        
        return Math.ceil(totalPersonnes / capacite);
    }
    
    // Validation dates
    dateDebut.addEventListener('change', function() {
        if (dateFin.value && new Date(dateFin.value) <= new Date(dateDebut.value)) {
            dateFin.value = '';
        }
        dateFin.min = dateDebut.value;
        updateRecap();
        updatePrix();
    });
    
    dateFin.addEventListener('change', function() {
        if (dateDebut.value && new Date(dateFin.value) <= new Date(dateDebut.value)) {
            alert('La date de départ doit être après la date d\'arrivée');
            dateFin.value = '';
            return;
        }
        updateRecap();
        updatePrix();
    });
    
    nbAdultes.addEventListener('change', function() { updateRecap(); updatePrix(); });
    nbEnfants.addEventListener('change', function() { updateRecap(); updatePrix(); });
    radios.forEach(radio => radio.addEventListener('change', function() { updateRecap(); updatePrix(); }));
    
    function updateRecap() {
        // Voyageurs
        const adultes = parseInt(nbAdultes.value) || 0;
        const enfants = parseInt(nbEnfants.value) || 0;
        document.getElementById('recap-adultes').textContent = adultes;
        document.getElementById('recap-enfants').textContent = enfants;
        
        // Dates et nuits
        if (dateDebut.value && dateFin.value) {
            const d1 = new Date(dateDebut.value);
            const d2 = new Date(dateFin.value);
            const nuits = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
            
            const options = { day: 'numeric', month: 'short' };
            const dateStr = d1.toLocaleDateString('fr-FR', options) + ' - ' + d2.toLocaleDateString('fr-FR', options);
            document.getElementById('recap-dates').innerHTML = `
                <svg class="w-5 h-5 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium text-gray-800">${dateStr}</span>
            `;
            document.getElementById('recap-nuits').textContent = nuits + ' nuit' + (nuits > 1 ? 's' : '');
        }
        
        // Chambre sélectionnée et nombre de chambres
        const selectedRadio = document.querySelector('input[name="numtype"]:checked');
        if (selectedRadio) {
            const numtype = parseInt(selectedRadio.value);
            const nomChambre = nomsChambres[numtype] || '--';
            const nbChambres = calculerNbChambres();
            
            document.getElementById('recap-chambre').textContent = nomChambre;
            nbChambresInput.value = nbChambres;
            
            const recapNbChambres = document.getElementById('recap-nb-chambres');
            if (nbChambres > 1) {
                recapNbChambres.textContent = `× ${nbChambres} chambres nécessaires`;
                recapNbChambres.classList.remove('hidden');
            } else {
                recapNbChambres.classList.add('hidden');
            }
        }
    }
    
    function updatePrix() {
        const selectedType = document.querySelector('input[name="numtype"]:checked');
        const prixPlaceholder = document.getElementById('prix-placeholder');
        const prixDetail = document.getElementById('prix-detail');
        
        if (!selectedType || !dateDebut.value || !dateFin.value) {
            // Afficher le placeholder si pas de dates
            prixPlaceholder.classList.remove('hidden');
            prixDetail.classList.add('hidden');
            return;
        }
        
        const adultes = parseInt(nbAdultes.value) || 0;
        const enfants = parseInt(nbEnfants.value) || 0;
        const totalPersonnes = adultes + enfants;
        const nbChambres = calculerNbChambres();
        
        const d1 = new Date(dateDebut.value);
        const d2 = new Date(dateFin.value);
        const nuits = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
        
        if (nuits <= 0) return;
        
        fetch('/api/prix', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                numtype: selectedType.value,
                dateDebut: dateDebut.value,
                dateFin: dateFin.value,
                nbAdultes: adultes,
                nbEnfants: enfants
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.prixParNuit) {
                const prixNuitUnitaire = parseFloat(data.prixParNuit);
                const prixNuitTotal = prixNuitUnitaire * nbChambres;
                const prixTotal = prixNuitTotal * nuits;
                const prixParPersonne = totalPersonnes > 0 ? Math.round(prixTotal / totalPersonnes) : 0;
                
                // Afficher le détail des prix
                prixPlaceholder.classList.add('hidden');
                prixDetail.classList.remove('hidden');
                
                // Mettre à jour le prix sur la carte de la chambre
                document.getElementById('prix-' + selectedType.value).textContent = prixNuitUnitaire.toFixed(0);
                
                // Mettre à jour le récapitulatif
                document.getElementById('recap-prix-personne').textContent = prixParPersonne + ' €';
                document.getElementById('recap-total').textContent = prixTotal.toFixed(0) + ' €';
            }
        })
        .catch(err => {
            console.error('Erreur prix:', err);
        });
    }
    
    // Init
    updateRecap();
    updatePrix();
    
    // Charger les prix de toutes les chambres au démarrage
    function chargerTousLesPrix() {
        const chambres = document.querySelectorAll('.chambre-card');
        chambres.forEach(card => {
            const numtype = card.dataset.numtype;
            fetch('/api/prix', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ numtype: numtype })
            })
            .then(r => r.json())
            .then(data => {
                if (data.prixParNuit) {
                    document.getElementById('prix-' + numtype).textContent = parseFloat(data.prixParNuit).toFixed(0);
                }
            })
            .catch(err => console.error('Erreur chargement prix:', err));
        });
    }
    chargerTousLesPrix();
});
</script>
@endsection
