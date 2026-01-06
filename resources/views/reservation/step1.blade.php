@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-clubmed-beige">
    <div class="bg-clubmed-blue text-white">
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
            <h1 class="text-2xl md:text-3xl font-bold font-serif">{{ $resort->nomresort }}</h1>
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

    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-center space-x-4 md:space-x-8">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-clubmed-gold text-white flex items-center justify-center font-bold text-sm">1</div>
                    <span class="ml-2 font-semibold text-clubmed-gold hidden sm:inline">Séjour</span>
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
        <form id="step1Form" action="{{ route('reservation.step2', $resort->numresort) }}" method="GET" onsubmit="return validateForm()">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="flex-1 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center font-serif">
                            <span class="w-8 h-8 rounded-full bg-clubmed-beige text-clubmed-gold flex items-center justify-center mr-3">
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
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d', strtotime('+3 years')) }}"
                                       value="{{ request('dateDebut') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de départ</label>
                                <input type="date" name="dateFin" id="dateFin" required 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d', strtotime('+3 years')) }}"
                                       value="{{ request('dateFin') }}">
                            </div>
                        </div>
                    </div>

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
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Adultes</p>
                                        <p class="text-sm text-gray-500">12 ans et plus</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="changeValue('nbAdultes', -1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="nbAdultes" id="nbAdultes" value="{{ request('nbAdultes', 2) }}" min="1" max="10" required 
                                               class="w-14 text-center text-xl font-bold border-0 bg-transparent focus:ring-0">
                                        <button type="button" onclick="changeValue('nbAdultes', 1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Enfants</p>
                                        <p class="text-sm text-gray-500">2 à 11 ans</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <button type="button" onclick="changeValue('nbEnfants', -1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="nbEnfants" id="nbEnfants" value="{{ request('nbEnfants', 0) }}" min="0" max="10" required 
                                               class="w-14 text-center text-xl font-bold border-0 bg-transparent focus:ring-0">
                                        <button type="button" onclick="changeValue('nbEnfants', 1)" 
                                                class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informations des participants --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100" id="participants-section">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-green-100 text-green-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            Informations des voyageurs
                        </h2>
                        <div id="participants-forms" class="space-y-4">
                            {{-- Les formulaires seront générés dynamiquement --}}
                        </div>
                    </div>

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
                                <div class="chambre-card rounded-xl border-2 border-gray-200 transition-all duration-200 hover:border-gray-300 hover:shadow-sm"
                                     data-numtype="{{ $type->numtype }}">
                                    
                                    <input type="hidden" name="chambres[{{ $type->numtype }}]" value="0" id="chambre-qty-{{ $type->numtype }}" class="chambre-quantity">
                                    
                                    <div class="p-5">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-800 text-lg">{{ $type->nomtype }}</h3>
                                                <div class="mt-2 flex flex-wrap gap-3">
                                                    @if($type->surface)
                                                        <span class="inline-flex items-center text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                                            {{ $type->surface }} m²
                                                        </span>
                                                    @endif
                                                    @if($type->capacitemax)
                                                        <span class="inline-flex items-center text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                                            Max {{ $type->capacitemax }} pers.
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="text-right ml-4 flex flex-col items-end">
                                                <span id="badge-promo-{{ $type->numtype }}" class="hidden bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full mb-1">
                                                    PROMO
                                                </span>

                                                <div id="prix-barre-{{ $type->numtype }}" class="hidden text-xs text-gray-400 line-through mb-0.5">
                                                    --
                                                </div>

                                                <div class="text-2xl font-bold text-clubmed-gold transition-colors duration-200" id="container-prix-{{ $type->numtype }}">
                                                    <span id="prix-{{ $type->numtype }}">--</span>
                                                </div>
                                                <div class="text-sm text-gray-500">€ / nuit</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Sélecteur de quantité -->
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-gray-700">Nombre de chambres :</span>
                                                <div class="flex items-center space-x-3">
                                                    <button type="button" onclick="changeChambresQty({{ $type->numtype }}, -1)" 
                                                            class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                        </svg>
                                                    </button>
                                                    <span class="w-12 text-center text-xl font-bold text-gray-800" id="display-qty-{{ $type->numtype }}">0</span>
                                                    <button type="button" onclick="changeChambresQty({{ $type->numtype }}, 1)" 
                                                            class="w-9 h-9 rounded-full bg-white border-2 border-gray-300 text-gray-600 hover:border-clubmed-gold hover:text-clubmed-gold transition-colors flex items-center justify-center">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:w-96">
                    <div class="sticky top-4">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-clubmed-blue p-5 text-white">
                                <h3 class="font-bold text-lg font-serif">Récapitulatif</h3>
                                <p class="text-clubmed-beige text-sm">{{ $resort->nomresort }}</p>
                            </div>
                            
                            <div class="p-5 space-y-4">
                                <div class="flex items-center text-gray-600" id="recap-dates">
                                    <span class="text-gray-400 italic">Sélectionnez vos dates</span>
                                </div>
                                
                                <div class="flex items-center justify-between py-3 border-t border-gray-100">
                                    <span class="text-gray-600">Nombre de nuits</span>
                                    <span class="font-bold text-gray-800" id="recap-nuits">--</span>
                                </div>

                                <div class="py-3 border-t border-gray-100">
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

                                <div class="py-3 border-t border-gray-100">
                                    <div class="flex items-center text-gray-600 mb-1">
                                        Hébergement
                                    </div>
                                    <p class="font-semibold text-gray-800 ml-7" id="recap-chambre">{{ $typeChambres->first()->nomtype ?? '--' }}</p>
                                </div>
                                <input type="hidden" name="nbChambres" id="nbChambres" value="1">
                            </div>

                            <div class="bg-gray-50 p-5 border-t border-gray-100">
                                <div id="prix-placeholder" class="text-center py-4">
                                    <p class="text-gray-400 text-sm">Sélectionnez vos dates pour voir le prix</p>
                                </div>
                                
                                <div id="prix-detail" class="hidden space-y-3">
                                    <div class="flex justify-between items-center text-gray-600">
                                        <span>Total HT</span>
                                        <span class="font-semibold" id="recap-total-ht">--</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>TVA (20%)</span>
                                        <span id="recap-tva">--</span>
                                    </div>
                                    <div class="border-t border-gray-300 my-2"></div>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-gray-800 font-semibold">Total TTC</span>
                                        <span class="text-2xl font-bold text-clubmed-gold" id="recap-total">--</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 border-t border-gray-100">
                                <button type="submit" 
                                        class="w-full bg-clubmed-blue text-white py-4 rounded-xl font-bold text-lg hover:bg-yellow-400 transition-all">
                                    Continuer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const capaciteChambres = {
    @foreach($typeChambres as $type)
        {{ $type->numtype }}: {{ $type->capacitemax ?? 2 }},
    @endforeach
};

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
    
    // GESTION AFFICHAGE PROMO (Avec gestion de la couleur verte)
    function afficherPromo(numtype, data) {
        const badge = document.getElementById('badge-promo-' + numtype);
        const prixBarre = document.getElementById('prix-barre-' + numtype);
        const prixContainer = document.getElementById('container-prix-' + numtype);
        const prixActuel = document.getElementById('prix-' + numtype);

        // Si Promo
        if (data.hasPromo && parseFloat(data.prixStandard) > parseFloat(data.prixParNuit)) {
            if(badge) {
                badge.classList.remove('hidden');
                // Force le vert
                badge.className = "bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full mb-1";
            }
            
            if(prixBarre) {
                prixBarre.textContent = parseFloat(data.prixStandard).toFixed(0) + ' €';
                prixBarre.classList.remove('hidden');
            }
            
            if(prixContainer) {
                prixContainer.classList.remove('text-clubmed-gold', 'text-clubmed-blue');
                prixContainer.classList.add('text-green-600'); // PRIX EN VERT
            }
            
            if(prixActuel) prixActuel.textContent = parseFloat(data.prixParNuit).toFixed(0);
        } else {
            // Pas de Promo
            if(badge) badge.classList.add('hidden');
            if(prixBarre) prixBarre.classList.add('hidden');
            
            if(prixContainer) {
                prixContainer.classList.remove('text-green-600', 'text-red-600');
                prixContainer.classList.add('text-clubmed-gold'); // Retour au clubmed-gold
            }
            
            if(prixActuel && data.prixParNuit) {
                prixActuel.textContent = parseFloat(data.prixParNuit).toFixed(0);
            }
        }
    }

    function changeValue(id, delta) {
        const input = document.getElementById(id);
        let newVal = parseInt(input.value) + delta;
        if (newVal >= input.min && newVal <= input.max) {
            input.value = newVal;
            updateRecap();
            generateParticipantsForms(); // Générer les formulaires des participants
            // Quand on change le nb de personnes, on met à jour les prix
            updateAllPrices(); 
        }
    }
    window.changeValue = changeValue;
    
    // Générer les formulaires pour chaque participant
    function generateParticipantsForms() {
        const nbAdultes = parseInt(document.getElementById('nbAdultes').value) || 0;
        const nbEnfants = parseInt(document.getElementById('nbEnfants').value) || 0;
        const container = document.getElementById('participants-forms');
        
        container.innerHTML = '';
        
        // Générer les formulaires pour les adultes
        for (let i = 1; i <= nbAdultes; i++) {
            container.innerHTML += createParticipantForm('adulte', i);
        }
        
        // Générer les formulaires pour les enfants
        for (let i = 1; i <= nbEnfants; i++) {
            container.innerHTML += createParticipantForm('enfant', i);
        }
    }
    
    function createParticipantForm(type, index) {
        const isAdulte = type === 'adulte';
        const label = isAdulte ? `Adulte ${index}` : `Enfant ${index}`;
        const minAge = isAdulte ? 15 : 0;
        const maxAge = isAdulte ? 120 : 14;
        const maxDate = new Date();
        maxDate.setFullYear(maxDate.getFullYear() - minAge);
        const minDate = new Date();
        minDate.setFullYear(minDate.getFullYear() - maxAge - 1);
        
        return `
            <div class="border-2 border-gray-200 rounded-xl p-4 bg-gray-50">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-${isAdulte ? 'blue' : 'purple'}-100 text-${isAdulte ? 'blue' : 'purple'}-600 flex items-center justify-center mr-2 text-sm">
                        ${index}
                    </span>
                    ${label} ${isAdulte ? '(15 ans et plus)' : '(moins de 15 ans)'}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civilité *</label>
                        <select name="participants[${type}_${index}][genre]" required 
                                class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0">
                            <option value="">-- Choisir --</option>
                            <option value="M.">Monsieur</option>
                            <option value="Mme">Madame</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="participants[${type}_${index}][nom]" required 
                               placeholder="Nom de famille"
                               class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="participants[${type}_${index}][prenom]" required 
                               placeholder="Prénom"
                               class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                        <input type="date" name="participants[${type}_${index}][datenaissance]" required 
                               min="${isAdulte ? '1920-01-01' : (() => { const d = new Date(); d.setFullYear(d.getFullYear() - 15); return d.toISOString().split('T')[0]; })()}"
                               max="${isAdulte ? (() => { const d = new Date(); d.setFullYear(d.getFullYear() - 15); return d.toISOString().split('T')[0]; })() : new Date().toISOString().split('T')[0]}"
                               onchange="validateAge(this, '${type}')"
                               class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-clubmed-gold focus:ring-clubmed-gold focus:ring-0">
                        <p class="text-xs text-gray-500 mt-1">${isAdulte ? 'Né(e) entre 1920 et il y a 15 ans' : 'Moins de 15 ans'}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Validation de l'âge
    function validateAge(input, type) {
        const dateNaissance = new Date(input.value);
        const aujourd = new Date();
        const age = Math.floor((aujourd - dateNaissance) / (365.25 * 24 * 60 * 60 * 1000));
        
        if (type === 'adulte' && age < 15) {
            alert('Un adulte doit avoir au moins 15 ans. Veuillez corriger la date de naissance ou sélectionner "Enfant".');
            input.value = '';
            input.classList.add('border-red-500');
            return false;
        }
        
        if (type === 'enfant' && age >= 15) {
            alert('Un enfant doit avoir moins de 15 ans. Veuillez corriger la date de naissance ou sélectionner "Adulte".');
            input.value = '';
            input.classList.add('border-red-500');
            return false;
        }
        
        input.classList.remove('border-red-500');
        return true;
    }
    window.validateAge = validateAge;
    
    function changeChambresQty(numtype, delta) {
        const input = document.getElementById('chambre-qty-' + numtype);
        const display = document.getElementById('display-qty-' + numtype);
        const card = document.querySelector('.chambre-card[data-numtype="' + numtype + '"]');
        
        let newVal = parseInt(input.value) + delta;
        if (newVal < 0) newVal = 0;
        if (newVal > 10) newVal = 10; // Limite max de 10 chambres par type
        
        input.value = newVal;
        display.textContent = newVal;
        
        // Styling de la carte selon la quantité
        if (newVal > 0) {
            card.classList.add('border-clubmed-gold', 'bg-clubmed-beige', 'shadow-md');
            card.classList.remove('border-gray-200');
        } else {
            card.classList.remove('border-clubmed-gold', 'bg-clubmed-beige', 'shadow-md');
            card.classList.add('border-gray-200');
        }
        
        updateRecap();
        updateSummaryPrice(); 
    }
    window.changeChambresQty = changeChambresQty;
    
    function calculerNbChambres() {
        let total = 0;
        document.querySelectorAll('.chambre-quantity').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        return total;
    }
    
    // ECOUTEURS D'EVENEMENTS : MISE A JOUR DE TOUS LES PRIX
    dateDebut.addEventListener('change', function() {
        if (dateFin.value && new Date(dateFin.value) <= new Date(dateDebut.value)) dateFin.value = '';
        dateFin.min = dateDebut.value;
        updateRecap();
        updateAllPrices(); // <--- ICI : Met à jour TOUTES les cartes
    });
    
    dateFin.addEventListener('change', function() {
        if (dateDebut.value && new Date(dateFin.value) <= new Date(dateDebut.value)) {
            alert('Date de départ invalide');
            dateFin.value = '';
            return;
        }
        updateRecap();
        updateAllPrices(); // <--- ICI : Met à jour TOUTES les cartes
    });
    
    nbAdultes.addEventListener('change', function() { updateRecap(); updateAllPrices(); });
    nbEnfants.addEventListener('change', function() { updateRecap(); updateAllPrices(); });

    function updateRecap() {
        const adultes = parseInt(nbAdultes.value) || 0;
        const enfants = parseInt(nbEnfants.value) || 0;
        const totalPersonnes = adultes + enfants;
        document.getElementById('recap-adultes').textContent = adultes;
        document.getElementById('recap-enfants').textContent = enfants;
        
        if (dateDebut.value && dateFin.value) {
            const d1 = new Date(dateDebut.value);
            const d2 = new Date(dateFin.value);
            const nuits = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
            
            const options = { day: 'numeric', month: 'short' };
            const dateStr = d1.toLocaleDateString('fr-FR', options) + ' - ' + d2.toLocaleDateString('fr-FR', options);
            document.getElementById('recap-dates').innerHTML = `<span class="font-medium text-gray-800">${dateStr}</span>`;
            document.getElementById('recap-nuits').textContent = nuits + ' nuit' + (nuits > 1 ? 's' : '');
        }
        
        // Afficher les chambres sélectionnées et calculer la capacité
        let chambresTexte = [];
        let capaciteTotale = 0;
        let nbChambresTotal = 0;
        document.querySelectorAll('.chambre-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const numtype = input.name.match(/\[(\d+)\]/)[1];
                const nom = nomsChambres[numtype];
                const capacite = capaciteChambres[numtype] || 2;
                capaciteTotale += capacite * qty;
                nbChambresTotal += qty;
                chambresTexte.push(`${qty}x ${nom}`);
            }
        });
        
        document.getElementById('recap-chambre').innerHTML = chambresTexte.length > 0 
            ? chambresTexte.join('<br>') 
            : '--';
        
        nbChambresInput.value = calculerNbChambres();
        
        // Validation de la capacité
        validateCapacity(totalPersonnes, capaciteTotale, nbChambresTotal);
    }
    
    // NOUVELLE FONCTION : Met à jour toutes les cartes (badges, prix barrés...)
    function updateAllPrices() {
        if (!dateDebut.value || !dateFin.value) return;

        const chambres = document.querySelectorAll('.chambre-card');
        chambres.forEach(card => {
            const numtype = card.dataset.numtype;
            fetch('/api/prix', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    numresort: {{ $resort->numresort }},
                    numtype: numtype,
                    dateDebut: dateDebut.value,
                    dateFin: dateFin.value
                })
            })
            .then(r => r.json())
            .then(data => {
                afficherPromo(numtype, data);
            })
            .catch(err => console.error('Erreur updateAllPrices:', err));
        });

        // Met aussi à jour le résumé à droite
        updateSummaryPrice();
    }

    // Calcul du prix total pour le résumé à droite
    function updateSummaryPrice() {
        const prixPlaceholder = document.getElementById('prix-placeholder');
        const prixDetail = document.getElementById('prix-detail');
        
        if (!dateDebut.value || !dateFin.value) {
            prixPlaceholder.classList.remove('hidden');
            prixDetail.classList.add('hidden');
            return;
        }
        
        const d1 = new Date(dateDebut.value);
        const d2 = new Date(dateFin.value);
        const nuits = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
        if (nuits <= 0) return;

        // Récupérer toutes les chambres avec quantité > 0
        const chambresSelectionnees = [];
        document.querySelectorAll('.chambre-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const numtype = input.name.match(/\[(\d+)\]/)[1];
                chambresSelectionnees.push({ numtype: numtype, qty: qty });
            }
        });

        if (chambresSelectionnees.length === 0) {
            prixPlaceholder.classList.remove('hidden');
            prixDetail.classList.add('hidden');
            return;
        }

        // Calculer le prix total pour toutes les chambres
        let prixTotalChambres = 0;
        let hasPromo = false;
        let compteur = chambresSelectionnees.length;

        chambresSelectionnees.forEach(chambre => {
            fetch('/api/prix', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    numresort: {{ $resort->numresort }},
                    numtype: chambre.numtype,
                    dateDebut: dateDebut.value,
                    dateFin: dateFin.value
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.prixParNuit) {
                    const prixNuitUnitaire = parseFloat(data.prixParNuit);
                    prixTotalChambres += prixNuitUnitaire * chambre.qty * nuits;
                    if (data.hasPromo) hasPromo = true;
                }

                compteur--;
                if (compteur === 0) {
                    // Toutes les requêtes sont terminées, afficher le total
                    const tva = prixTotalChambres * 0.2;
                    const ht = prixTotalChambres - tva;

                    prixPlaceholder.classList.add('hidden');
                    prixDetail.classList.remove('hidden');
                    
                    document.getElementById('recap-total-ht').textContent = ht.toFixed(2) + ' €';
                    document.getElementById('recap-tva').textContent = tva.toFixed(2) + ' €';
                    
                    const totalEl = document.getElementById('recap-total');
                    totalEl.textContent = prixTotalChambres.toFixed(2) + ' €';
                    
                    if (hasPromo) {
                        totalEl.classList.remove('text-clubmed-gold');
                        totalEl.classList.add('text-green-600');
                    } else {
                        totalEl.classList.remove('text-green-600');
                        totalEl.classList.add('text-clubmed-gold');
                    }
                }
            });
        });
    }
    
    // Initialisation
    generateParticipantsForms(); // Générer les formulaires des participants au chargement
    updateRecap();
    updateAllPrices(); // Charge les prix au démarrage
});

function validateCapacity(totalPersonnes, capaciteTotale, nbChambresTotal) {
    const submitBtn = document.querySelector('button[type="submit"]');
    const form = document.getElementById('step1Form');
    
    // Supprimer les anciens messages d'erreur
    const oldError = document.getElementById('capacity-error');
    if (oldError) oldError.remove();
    
    let errorMessage = '';
    
    if (nbChambresTotal > 0) {
        if (capaciteTotale < totalPersonnes) {
            errorMessage = `⚠️ Capacité insuffisante : vos chambres peuvent accueillir ${capaciteTotale} personne(s) mais vous êtes ${totalPersonnes}. Veuillez ajouter ou modifier vos chambres.`;
        } else if (nbChambresTotal > totalPersonnes) {
            errorMessage = `⚠️ Trop de chambres : vous avez sélectionné ${nbChambresTotal} chambre(s) pour ${totalPersonnes} personne(s). Maximum 1 chambre par personne.`;
        }
    } else if (totalPersonnes > 0) {
        errorMessage = '⚠️ Veuillez sélectionner au moins une chambre.';
    }
    
    if (errorMessage) {
        const errorDiv = document.createElement('div');
        errorDiv.id = 'capacity-error';
        errorDiv.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg';
        errorDiv.innerHTML = `<p class="font-semibold">${errorMessage}</p>`;
        form.insertBefore(errorDiv, form.firstChild);
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        return false;
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return true;
    }
}

function calculerNbChambres() {
    let total = 0;
    document.querySelectorAll('.chambre-quantity').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    return total;
}

function validateForm() {
    const nbAdultes = parseInt(document.getElementById('nbAdultes').value) || 0;
    const nbEnfants = parseInt(document.getElementById('nbEnfants').value) || 0;
    const totalPersonnes = nbAdultes + nbEnfants;
    
    const nbChambresTotal = calculerNbChambres();
    
    let capaciteTotale = 0;
    document.querySelectorAll('.chambre-quantity').forEach(input => {
        const qty = parseInt(input.value) || 0;
        if (qty > 0) {
            const numtype = input.name.match(/\[(\d+)\]/)[1];
            const capacite = capaciteChambres[numtype] || 2;
            capaciteTotale += capacite * qty;
        }
    });
    
    if (nbChambresTotal === 0) {
        alert('Veuillez sélectionner au moins une chambre avant de continuer.');
        return false;
    }
    
    if (capaciteTotale < totalPersonnes) {
        alert(`La capacité des chambres sélectionnées (${capaciteTotale} personnes) est insuffisante pour ${totalPersonnes} voyageurs.\nVeuillez ajouter ou modifier vos chambres.`);
        return false;
    }
    
    if (nbChambresTotal > totalPersonnes) {
        alert(`Vous avez sélectionné ${nbChambresTotal} chambres pour ${totalPersonnes} personnes.\nMaximum 1 chambre par personne.`);
        return false;
    }
    
    // Vérifier que tous les participants ont rempli leurs informations
    const participantInputs = document.querySelectorAll('#participants-forms input[required], #participants-forms select[required]');
    let missingInfo = false;
    
    participantInputs.forEach(input => {
        if (!input.value) {
            missingInfo = true;
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    if (missingInfo) {
        alert('Veuillez remplir toutes les informations des voyageurs (civilité, nom, prénom, date de naissance).');
        document.getElementById('participants-section').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    
    // Vérifier que toutes les dates de naissance sont valides
    const datesNaissance = document.querySelectorAll('input[name*="[datenaissance]"]');
    for (let input of datesNaissance) {
        if (input.value) {
            const type = input.name.includes('adulte') ? 'adulte' : 'enfant';
            if (!validateAge(input, type)) {
                return false;
            }
        }
    }
    
    return true;
}
</script>
@endsection