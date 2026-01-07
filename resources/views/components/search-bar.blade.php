{{-- ===== BARRE DE RECHERCHE DYNAMIQUE CLUB MED ===== --}}
@props(['resorts' => collect(), 'localisations' => collect()])

{{-- Barre de recherche inline (position normale) --}}
<div id="search-bar-inline" class="search-bar-wrapper">
    <div class="bg-white rounded-full shadow-lg p-2 max-w-4xl mx-auto cursor-pointer transition-all hover:shadow-xl border border-gray-100">
        <div class="flex items-center">
            {{-- Champ destination --}}
            <div class="flex-1 flex items-center px-4 py-2 border-r border-gray-200" onclick="openSearchModal('destination')">
                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-gray-500 text-sm lg:text-base" id="inline-destination-text">France, ski, Île Maurice...</span>
            </div>
            
            {{-- Dates --}}
            <div class="hidden sm:flex items-center px-4 py-2 border-r border-gray-200 cursor-pointer hover:bg-gray-50 rounded-lg transition-colors" onclick="openSearchModal('dates')">
                <span class="text-black text-sm lg:text-base font-medium" id="inline-dates-text">Sélectionner les dates</span>
                <button class="ml-2 text-gray-400 hover:text-gray-600" aria-label="Effacer dates" onclick="event.stopPropagation(); clearDates()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Voyageurs --}}
            <div class="hidden md:flex items-center px-4 py-2 cursor-pointer hover:bg-gray-50 rounded-lg transition-colors" onclick="openSearchModal('travelers')">
                <span class="text-black text-sm lg:text-base font-medium" id="inline-travelers-text">2 Adultes</span>
                <button class="ml-2 text-gray-400 hover:text-gray-600" aria-label="Modifier voyageurs" onclick="event.stopPropagation()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Bouton Rechercher --}}
            <button onclick="submitSearch()" class="ml-2 bg-clubmed-gold hover:bg-yellow-400 text-black font-semibold px-6 py-3 rounded-full transition-all flex items-center gap-2">
                <span class="hidden sm:inline">Rechercher</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

{{-- Modal de recherche (plein écran avec overlay) --}}
<div id="search-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
    {{-- Overlay --}}
    <div class="search-modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeSearchModal()"></div>
    
    {{-- Contenu du modal --}}
    <div class="search-modal-content fixed top-0 left-0 right-0 bg-white shadow-2xl transform transition-transform duration-300 ease-out">
        {{-- Header avec barre de recherche --}}
        <div class="border-b border-gray-200">
            <div class="max-w-6xl mx-auto px-4 py-4">
                <div class="flex items-center gap-4">
                    {{-- Barre de recherche active --}}
                    <div class="flex-1 flex items-center bg-gray-50 rounded-full border border-gray-200 p-2">
                        {{-- Champ de recherche --}}
                        <div class="flex-1 flex items-center px-4 cursor-pointer" onclick="switchTab('destination')">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   id="search-input" 
                                   class="w-full bg-transparent border-none focus:ring-0 text-black placeholder-gray-500 text-base"
                                   placeholder="France, ski, Île Maurice..."
                                   autocomplete="off"
                                   onfocus="switchTab('destination')"
                                   oninput="filterSearchResults(this.value)">
                        </div>
                        
                        {{-- Séparateur --}}
                        <div class="hidden sm:block h-8 w-px bg-gray-300 mx-2"></div>
                        
                        {{-- Dates --}}
                        <div class="hidden sm:flex items-center px-4 cursor-pointer hover:bg-gray-100 rounded-full py-2 relative" 
                             id="dates-tab-btn"
                             onclick="switchTab('dates')">
                            <span class="text-black text-sm font-medium" id="selected-dates">Sélectionner les dates</span>
                            <button class="ml-2 text-gray-400 hover:text-gray-600" onclick="event.stopPropagation(); clearDates()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Séparateur --}}
                        <div class="hidden md:block h-8 w-px bg-gray-300 mx-2"></div>
                        
                        {{-- Voyageurs --}}
                        <div class="hidden md:flex items-center px-4 cursor-pointer hover:bg-gray-100 rounded-full py-2 relative"
                             id="travelers-tab-btn"
                             onclick="switchTab('travelers')">
                            <span class="text-black text-sm font-medium" id="selected-travelers">2 Adultes</span>
                            <button class="ml-2 text-gray-400 hover:text-gray-600" onclick="event.stopPropagation()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Bouton Rechercher --}}
                        <button onclick="submitSearch()" class="ml-2 bg-clubmed-gold hover:bg-yellow-400 text-black font-semibold px-6 py-3 rounded-full transition-all flex items-center gap-2">
                            Rechercher
                        </button>
                    </div>
                    
                    {{-- Bouton fermer --}}
                    <button onclick="closeSearchModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors" aria-label="Fermer">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- ===== CONTENU ONGLET DESTINATION ===== --}}
        <div id="tab-destination" class="tab-content">
            <div class="max-w-6xl mx-auto px-4 py-6 max-h-[70vh] overflow-y-auto">
                {{-- Recherches populaires --}}
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Recherches populaires</h3>
                    <div class="flex flex-wrap gap-3" id="popular-searches">
                        @php
                            $popularSearches = ['ski', 'île maurice', 'france', 'martinique', 'maroc', 'maldives', 'seychelles'];
                        @endphp
                        @foreach($popularSearches as $search)
                            <button onclick="setSearchQuery('{{ $search }}')" 
                                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-full hover:border-gray-400 hover:shadow-sm transition-all text-sm text-gray-700">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                {{ $search }}
                            </button>
                        @endforeach
                    </div>
                </div>
                
                {{-- Indicateur de chargement --}}
                <div id="search-loading" class="hidden mb-8">
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-clubmed-gold"></div>
                        <span class="ml-3 text-gray-600">Recherche en cours...</span>
                    </div>
                </div>
                
                {{-- Section Resorts (résultats de recherche) --}}
                <div class="mb-8" id="resorts-section">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4" id="resorts-section-title">Resorts</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="resorts-grid">
                        @if($resorts->count() > 0)
                            @foreach($resorts->take(6) as $resort)
                                <a href="javascript:void(0)" 
                                   onclick="goToResortReservation({{ $resort->numresort }})"
                                   class="resort-item flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group"
                                   data-numresort="{{ $resort->numresort }}"
                                   data-name="{{ strtolower($resort->nomresort) }}"
                                   data-country="{{ strtolower($resort->pays->nompays ?? '') }}">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 relative">
                                        @php
                                            $photo = $resort->photos->first();
                                            $hasImage = $photo && $photo->urlphoto;
                                        @endphp
                                        @if($hasImage)
                                            <img src="{{ asset('img/ressort/' . $photo->urlphoto) }}" 
                                                 alt="{{ $resort->nomresort }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-clubmed-blue to-clubmed-blue-dark">
                                                <span class="text-white text-xl">🏝️</span>
                                            </div>
                                        @endif
                                        @if($resort->nbtridents >= 4)
                                            <div class="absolute top-1 left-1">
                                                <span class="bg-black text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Luxe</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 group-hover:text-clubmed-blue transition-colors truncate">
                                            {{ $resort->nomresort }}
                                        </h4>
                                        <p class="text-sm text-gray-500 truncate">{{ $resort->pays->nompays ?? 'Destination Club Med' }}</p>
                                        @if($resort->moyenneavis)
                                            <div class="flex items-center gap-1 mt-1">
                                                <span class="text-sm font-medium text-gray-700">{{ $resort->moyenneavis }}/5</span>
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= round($resort->moyenneavis) ? 'text-green-500' : 'text-gray-300' }} text-xs">●</span>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Bouton Réserver --}}
                                    <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="inline-flex items-center px-3 py-1.5 bg-clubmed-gold text-black text-xs font-semibold rounded-full">
                                            Réserver →
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="col-span-3 text-center text-gray-500 py-8">
                                Aucun resort disponible
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Types de vacances --}}
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Types de vacances</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @php
                            $vacationTypes = [
                                ['name' => 'Ski & Montagne', 'icon' => '🏔️', 'url' => url('/resorts?typeclub=1'), 'color' => 'bg-blue-50 border-blue-200'],
                                ['name' => 'Mer & Plage', 'icon' => '🏖️', 'url' => url('/resorts?typeclub=2'), 'color' => 'bg-yellow-50 border-yellow-200'],
                                ['name' => 'Circuits & Escapades', 'icon' => '🗺️', 'url' => url('/resorts'), 'color' => 'bg-green-50 border-green-200'],
                                ['name' => 'Gamme Luxe', 'icon' => '💎', 'url' => url('/resorts'), 'color' => 'bg-purple-50 border-purple-200'],
                            ];
                        @endphp
                        @foreach($vacationTypes as $type)
                            <a href="{{ $type['url'] }}" 
                               class="flex items-center gap-3 p-4 rounded-xl border {{ $type['color'] }} hover:shadow-md transition-all group">
                                <span class="text-2xl">{{ $type['icon'] }}</span>
                                <span class="font-medium text-gray-900 group-hover:text-clubmed-blue transition-colors text-sm">{{ $type['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                
                {{-- Destinations populaires --}}
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Destinations populaires</h3>
                    <div class="flex flex-wrap gap-2">
                        @if($localisations && count($localisations) > 0)
                            @foreach($localisations as $numLoc => $nomLoc)
                                <a href="{{ url('/resorts?localisation=' . $numLoc) }}" 
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-700 transition-colors">
                                    {{ $nomLoc }}
                                </a>
                            @endforeach
                        @else
                            @php
                                $defaultLocations = ['Les Alpes', 'Caraïbes', 'Asie', 'Afrique', 'Europe', 'Amérique'];
                            @endphp
                            @foreach($defaultLocations as $loc)
                                <a href="{{ url('/resorts') }}" 
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-700 transition-colors">
                                    {{ $loc }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- ===== CONTENU ONGLET DATES ===== --}}
        <div id="tab-dates" class="tab-content hidden">
            <div class="max-w-4xl mx-auto px-4 py-6">
                <div class="bg-clubmed-beige rounded-3xl p-6 shadow-lg">
                    {{-- Navigation mois --}}
                    <div class="flex items-center justify-between mb-6">
                        <button onclick="previousMonth()" class="p-2 rounded-full hover:bg-white transition-colors" id="prev-month-btn">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
                            {{-- Mois 1 --}}
                            <div class="text-center">
                                <h4 class="text-lg font-bold text-gray-900 mb-4" id="calendar-month-1"></h4>
                                <div id="calendar-grid-1" class="calendar-grid"></div>
                            </div>
                            
                            {{-- Mois 2 --}}
                            <div class="text-center hidden md:block">
                                <h4 class="text-lg font-bold text-gray-900 mb-4" id="calendar-month-2"></h4>
                                <div id="calendar-grid-2" class="calendar-grid"></div>
                            </div>
                        </div>
                        
                        <button onclick="nextMonth()" class="p-2 rounded-full bg-black text-white hover:bg-gray-800 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    
                    {{-- Boutons d'action --}}
                    <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-gray-300/50">
                        <button onclick="resetDates()" class="text-gray-700 font-medium hover:text-black transition-colors underline">
                            Réinitialiser
                        </button>
                        <button onclick="validateDates()" class="bg-clubmed-gold hover:bg-yellow-400 text-black font-semibold px-8 py-3 rounded-full transition-all flex items-center gap-2">
                            Valider
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- ===== CONTENU ONGLET VOYAGEURS ===== --}}
        <div id="tab-travelers" class="tab-content hidden">
            <div class="max-w-md mx-auto px-4 py-6">
                <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100">
                    {{-- Adultes --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div>
                            <h4 class="font-semibold text-gray-900 text-lg">Adultes</h4>
                        </div>
                        <div class="flex items-center gap-4">
                            <button onclick="updateTravelers('adults', -1)" 
                                    class="w-10 h-10 rounded-full bg-clubmed-gold hover:bg-yellow-400 text-black font-bold text-xl flex items-center justify-center transition-all"
                                    id="adults-minus">
                                −
                            </button>
                            <span class="w-8 text-center text-xl font-semibold text-gray-900" id="adults-count">2</span>
                            <button onclick="updateTravelers('adults', 1)" 
                                    class="w-10 h-10 rounded-full bg-clubmed-gold hover:bg-yellow-400 text-black font-bold text-xl flex items-center justify-center transition-all"
                                    id="adults-plus">
                                +
                            </button>
                        </div>
                    </div>
                    
                    {{-- Enfants --}}
                    <div class="flex items-center justify-between py-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 text-lg">Enfants</h4>
                            <p class="text-sm text-gray-500">Moins de 18 ans</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button onclick="updateTravelers('children', -1)" 
                                    class="w-10 h-10 rounded-full bg-clubmed-gold hover:bg-yellow-400 text-black font-bold text-xl flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    id="children-minus">
                                −
                            </button>
                            <span class="w-8 text-center text-xl font-semibold text-gray-900" id="children-count">0</span>
                            <button onclick="updateTravelers('children', 1)" 
                                    class="w-10 h-10 rounded-full bg-clubmed-gold hover:bg-yellow-400 text-black font-bold text-xl flex items-center justify-center transition-all"
                                    id="children-plus">
                                +
                            </button>
                        </div>
                    </div>
                    
                    {{-- Boutons d'action --}}
                    <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t border-gray-200">
                        <button onclick="resetTravelers()" class="text-gray-700 font-medium hover:text-black transition-colors underline">
                            Réinitialiser
                        </button>
                        <button onclick="validateTravelers()" class="bg-clubmed-gold hover:bg-yellow-400 text-black font-semibold px-8 py-3 rounded-full transition-all flex items-center gap-2">
                            Continuer
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles CSS --}}
<style>
    /* Animation du modal */
    .search-modal-content {
        transform: translateY(-100%);
    }
    
    #search-modal.active .search-modal-content {
        transform: translateY(0);
    }
    
    #search-modal.active .search-modal-overlay {
        opacity: 1;
    }
    
    .search-modal-overlay {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }
    
    /* Tabs */
    .tab-content {
        animation: fadeIn 0.2s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Calendrier */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }
    
    .calendar-day-header {
        padding: 8px 4px;
        text-align: center;
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
    }
    
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        border-radius: 50%;
        transition: all 0.15s ease;
        position: relative;
    }
    
    .calendar-day:hover:not(.disabled):not(.past) {
        background-color: #ffc72c;
    }
    
    .calendar-day.disabled, .calendar-day.past {
        color: #d1d5db;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    
    .calendar-day.selected {
        background-color: #ffc72c;
        font-weight: 600;
    }
    
    .calendar-day.in-range {
        background-color: rgba(255, 199, 44, 0.3);
        border-radius: 0;
    }
    
    .calendar-day.range-start {
        border-radius: 50% 0 0 50%;
        background-color: #ffc72c;
    }
    
    .calendar-day.range-end {
        border-radius: 0 50% 50% 0;
        background-color: #ffc72c;
    }
    
    .calendar-day.today {
        border: 2px solid #000;
    }
    
    /* Animation des résultats */
    .resort-item {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.2s ease-out;
    }
    
    .resort-item.hidden {
        display: none;
    }
    
    /* Onglet actif */
    .tab-btn-active {
        background-color: rgba(255, 199, 44, 0.2);
        border-radius: 9999px;
    }
    
    /* Scrollbar personnalisée */
    .max-h-\[70vh\]::-webkit-scrollbar {
        width: 6px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .max-h-\[70vh\]::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>

{{-- JavaScript --}}
<script>
    // État de la recherche
    let searchState = {
        query: '',
        startDate: null,
        endDate: null,
        adults: 2,
        children: 0,
        currentTab: 'destination',
        calendarMonth: new Date().getMonth(),
        calendarYear: new Date().getFullYear(),
        selectedResort: null // Resort sélectionné pour redirection après sélection des dates
    };
    
    const monthNames = ['Janv.', 'Févr.', 'Mars', 'Avr.', 'Mai', 'Juin', 'Juil.', 'Août', 'Sept.', 'Oct.', 'Nov.', 'Déc.'];
    const dayNames = ['lu', 'ma', 'me', 'je', 've', 'sa', 'di'];
    
    // Ouvrir le modal de recherche
    function openSearchModal(tab = 'destination') {
        const modal = document.getElementById('search-modal');
        
        modal.classList.remove('hidden');
        modal.offsetHeight; // Force reflow
        modal.classList.add('active');
        
        switchTab(tab);
        
        if (tab === 'destination') {
            setTimeout(() => document.getElementById('search-input').focus(), 300);
        }
        
        document.body.style.overflow = 'hidden';
    }
    
    // Fermer le modal
    function closeSearchModal() {
        const modal = document.getElementById('search-modal');
        modal.classList.remove('active');
        setTimeout(() => modal.classList.add('hidden'), 300);
        document.body.style.overflow = '';
    }
    
    // Changer d'onglet
    function switchTab(tab) {
        searchState.currentTab = tab;
        
        // Cacher tous les onglets
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        
        // Afficher l'onglet sélectionné
        const tabContent = document.getElementById('tab-' + tab);
        if (tabContent) {
            tabContent.classList.remove('hidden');
        }
        
        // Mettre à jour les styles des boutons
        document.querySelectorAll('[id$="-tab-btn"]').forEach(el => el.classList.remove('tab-btn-active'));
        const tabBtn = document.getElementById(tab + '-tab-btn');
        if (tabBtn) tabBtn.classList.add('tab-btn-active');
        
        // Initialiser le calendrier si nécessaire
        if (tab === 'dates') {
            renderCalendars();
        }
    }
    
    // ===== CALENDRIER =====
    function renderCalendars() {
        renderCalendar(1, searchState.calendarYear, searchState.calendarMonth);
        
        let nextMonth = searchState.calendarMonth + 1;
        let nextYear = searchState.calendarYear;
        if (nextMonth > 11) {
            nextMonth = 0;
            nextYear++;
        }
        renderCalendar(2, nextYear, nextMonth);
        
        // Désactiver bouton précédent si on est au mois actuel
        const now = new Date();
        const prevBtn = document.getElementById('prev-month-btn');
        if (searchState.calendarYear === now.getFullYear() && searchState.calendarMonth === now.getMonth()) {
            prevBtn.classList.add('opacity-30', 'cursor-not-allowed');
        } else {
            prevBtn.classList.remove('opacity-30', 'cursor-not-allowed');
        }
    }
    
    function renderCalendar(calNum, year, month) {
        const titleEl = document.getElementById('calendar-month-' + calNum);
        const gridEl = document.getElementById('calendar-grid-' + calNum);
        
        if (!titleEl || !gridEl) return;
        
        titleEl.textContent = monthNames[month] + ' ' + year;
        
        // Jours de la semaine
        let html = dayNames.map(d => `<div class="calendar-day-header">${d}</div>`).join('');
        
        // Premier jour du mois (lundi = 0)
        const firstDay = new Date(year, month, 1);
        let startDay = firstDay.getDay() - 1;
        if (startDay < 0) startDay = 6;
        
        // Nombre de jours dans le mois
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Date d'aujourd'hui
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Cases vides avant
        for (let i = 0; i < startDay; i++) {
            html += '<div class="calendar-day disabled"></div>';
        }
        
        // Jours du mois
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateStr = formatDate(date);
            const isPast = date < today;
            
            let classes = 'calendar-day';
            if (isPast) classes += ' past';
            if (date.getTime() === today.getTime()) classes += ' today';
            
            // Sélection
            if (searchState.startDate && dateStr === searchState.startDate) {
                classes += ' selected range-start';
            } else if (searchState.endDate && dateStr === searchState.endDate) {
                classes += ' selected range-end';
            } else if (searchState.startDate && searchState.endDate) {
                const start = parseDate(searchState.startDate);
                const end = parseDate(searchState.endDate);
                if (date > start && date < end) {
                    classes += ' in-range';
                }
            }
            
            const onclick = isPast ? '' : `onclick="selectDate('${dateStr}')"`;
            html += `<div class="${classes}" ${onclick}>${day}</div>`;
        }
        
        gridEl.innerHTML = html;
    }
    
    function previousMonth() {
        const now = new Date();
        if (searchState.calendarYear === now.getFullYear() && searchState.calendarMonth === now.getMonth()) {
            return;
        }
        
        searchState.calendarMonth--;
        if (searchState.calendarMonth < 0) {
            searchState.calendarMonth = 11;
            searchState.calendarYear--;
        }
        renderCalendars();
    }
    
    function nextMonth() {
        searchState.calendarMonth++;
        if (searchState.calendarMonth > 11) {
            searchState.calendarMonth = 0;
            searchState.calendarYear++;
        }
        renderCalendars();
    }
    
    function selectDate(dateStr) {
        if (!searchState.startDate || (searchState.startDate && searchState.endDate)) {
            // Première sélection ou reset
            searchState.startDate = dateStr;
            searchState.endDate = null;
        } else {
            // Deuxième sélection
            const start = parseDate(searchState.startDate);
            const selected = parseDate(dateStr);
            
            if (selected < start) {
                searchState.endDate = searchState.startDate;
                searchState.startDate = dateStr;
            } else {
                searchState.endDate = dateStr;
            }
        }
        
        renderCalendars();
        updateDatesDisplay();
    }
    
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${year}-${month}-${day}`;
    }
    
    function parseDate(dateStr) {
        const [year, month, day] = dateStr.split('-').map(Number);
        return new Date(year, month - 1, day);
    }
    
    function formatDisplayDate(dateStr) {
        const date = parseDate(dateStr);
        const day = date.getDate();
        const month = monthNames[date.getMonth()].replace('.', '');
        const year = date.getFullYear();
        return `${day} ${month.toLowerCase()}. ${year}`;
    }
    
    function updateDatesDisplay() {
        let text = 'Sélectionner les dates';
        
        if (searchState.startDate && searchState.endDate) {
            text = formatDisplayDate(searchState.startDate) + ' - ' + formatDisplayDate(searchState.endDate);
        } else if (searchState.startDate) {
            text = formatDisplayDate(searchState.startDate) + ' - ...';
        }
        
        document.getElementById('selected-dates').textContent = text;
        document.getElementById('inline-dates-text').textContent = text;
    }
    
    function resetDates() {
        searchState.startDate = null;
        searchState.endDate = null;
        renderCalendars();
        updateDatesDisplay();
    }
    
    function clearDates() {
        resetDates();
    }
    
    function validateDates() {
        // Si un resort était sélectionné et qu'on a maintenant les dates, rediriger
        if (searchState.selectedResort && searchState.startDate && searchState.endDate) {
            goToResortReservation(searchState.selectedResort);
            return;
        }
        
        // Sinon, passer à l'onglet voyageurs
        switchTab('travelers');
    }
    
    // ===== VOYAGEURS =====
    function updateTravelers(type, delta) {
        if (type === 'adults') {
            searchState.adults = Math.max(1, Math.min(10, searchState.adults + delta));
            document.getElementById('adults-count').textContent = searchState.adults;
        } else {
            searchState.children = Math.max(0, Math.min(10, searchState.children + delta));
            document.getElementById('children-count').textContent = searchState.children;
        }
        
        updateTravelersDisplay();
        updateTravelersButtons();
    }
    
    function updateTravelersButtons() {
        const adultsMinus = document.getElementById('adults-minus');
        const childrenMinus = document.getElementById('children-minus');
        
        if (!adultsMinus || !childrenMinus) return;
        
        adultsMinus.disabled = searchState.adults <= 1;
        childrenMinus.disabled = searchState.children <= 0;
        
        if (searchState.adults <= 1) {
            adultsMinus.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            adultsMinus.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        if (searchState.children <= 0) {
            childrenMinus.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            childrenMinus.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    function updateTravelersDisplay() {
        let text = searchState.adults + ' Adulte' + (searchState.adults > 1 ? 's' : '');
        if (searchState.children > 0) {
            text += ', ' + searchState.children + ' Enfant' + (searchState.children > 1 ? 's' : '');
        }
        
        document.getElementById('selected-travelers').textContent = text;
        document.getElementById('inline-travelers-text').textContent = text;
    }
    
    function resetTravelers() {
        searchState.adults = 2;
        searchState.children = 0;
        document.getElementById('adults-count').textContent = 2;
        document.getElementById('children-count').textContent = 0;
        updateTravelersDisplay();
        updateTravelersButtons();
    }
    
    function validateTravelers() {
        closeSearchModal();
    }
    
    // ===== RECHERCHE DESTINATION =====
    let searchTimeout = null;
    let originalResortsHtml = null;
    
    function filterSearchResults(query) {
        searchState.query = query.trim();
        
        // Annuler la recherche précédente
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Si la recherche est vide, restaurer les resorts originaux
        if (searchState.query === '') {
            restoreOriginalResorts();
            document.getElementById('resorts-section-title').textContent = 'Resorts';
            document.getElementById('search-loading').classList.add('hidden');
            return;
        }
        
        // Attendre 300ms avant de lancer la recherche (debounce)
        searchTimeout = setTimeout(() => {
            searchResorts(searchState.query);
        }, 300);
    }
    
    function searchResorts(query) {
        const loadingEl = document.getElementById('search-loading');
        const resortsGrid = document.getElementById('resorts-grid');
        const titleEl = document.getElementById('resorts-section-title');
        
        // Sauvegarder les resorts originaux si pas encore fait
        if (!originalResortsHtml) {
            originalResortsHtml = resortsGrid.innerHTML;
        }
        
        // Afficher le chargement
        loadingEl.classList.remove('hidden');
        
        // Requête AJAX
        fetch('/api/resorts/search?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(resorts => {
                loadingEl.classList.add('hidden');
                
                if (resorts.length === 0) {
                    resortsGrid.innerHTML = `
                        <div class="col-span-3 text-center py-8">
                            <div class="text-4xl mb-3">🔍</div>
                            <p class="text-gray-500">Aucun resort trouvé pour "<strong>${query}</strong>"</p>
                            <p class="text-sm text-gray-400 mt-2">Essayez avec un autre terme de recherche</p>
                        </div>
                    `;
                    titleEl.textContent = 'Résultats de recherche';
                    return;
                }
                
                titleEl.textContent = `Résultats pour "${query}" (${resorts.length})`;
                
                // Générer le HTML des résultats
                let html = '';
                resorts.forEach(resort => {
                    const photoHtml = resort.photo 
                        ? `<img src="${resort.photo}" alt="${resort.nomresort}" class="w-full h-full object-cover">`
                        : `<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600 to-blue-800"><span class="text-white text-xl">🏝️</span></div>`;
                    
                    const luxeBadge = resort.nbtridents >= 4 
                        ? `<div class="absolute top-1 left-1"><span class="bg-black text-white text-[10px] px-1.5 py-0.5 rounded-full font-medium">Luxe</span></div>`
                        : '';
                    
                    const ratingHtml = resort.moyenneavis 
                        ? `<div class="flex items-center gap-1 mt-1">
                               <span class="text-sm font-medium text-gray-700">${resort.moyenneavis}/5</span>
                               <div class="flex">${generateRatingDots(resort.moyenneavis)}</div>
                           </div>`
                        : '';
                    
                    html += `
                        <a href="javascript:void(0)" 
                           onclick="goToResortReservation(${resort.numresort})"
                           class="resort-item flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group"
                           data-numresort="${resort.numresort}"
                           data-name="${resort.nomresort.toLowerCase()}"
                           data-country="${resort.pays.toLowerCase()}">
                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 relative">
                                ${photoHtml}
                                ${luxeBadge}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 group-hover:text-clubmed-blue transition-colors truncate">
                                    ${resort.nomresort}
                                </h4>
                                <p class="text-sm text-gray-500 truncate">${resort.pays}</p>
                                ${ratingHtml}
                            </div>
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="inline-flex items-center px-3 py-1.5 bg-clubmed-gold text-black text-xs font-semibold rounded-full">
                                    Réserver →
                                </span>
                            </div>
                        </a>
                    `;
                });
                
                resortsGrid.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur de recherche:', error);
                loadingEl.classList.add('hidden');
                resortsGrid.innerHTML = `
                    <div class="col-span-3 text-center py-8 text-red-500">
                        Erreur lors de la recherche. Veuillez réessayer.
                    </div>
                `;
            });
    }
    
    function generateRatingDots(rating) {
        let dots = '';
        for (let i = 1; i <= 5; i++) {
            dots += `<span class="${i <= Math.round(rating) ? 'text-green-500' : 'text-gray-300'} text-xs">●</span>`;
        }
        return dots;
    }
    
    function restoreOriginalResorts() {
        if (originalResortsHtml) {
            document.getElementById('resorts-grid').innerHTML = originalResortsHtml;
        }
    }
    
    function setSearchQuery(query) {
        const input = document.getElementById('search-input');
        input.value = query;
        filterSearchResults(query);
        input.focus();
    }
    
    // ===== REDIRECTION VERS RÉSERVATION =====
    function goToResortReservation(numresort) {
        // Si les dates ne sont pas sélectionnées, demander à l'utilisateur de les sélectionner
        if (!searchState.startDate || !searchState.endDate) {
            // Stocker le resort sélectionné pour redirection après sélection des dates
            searchState.selectedResort = numresort;
            
            // Afficher un message et basculer vers l'onglet dates
            switchTab('dates');
            
            // Afficher une notification
            showSearchNotification('Veuillez sélectionner vos dates de séjour');
            return;
        }
        
        let url = '/reservation/' + numresort + '/step1?';
        
        // Ajouter les dates
        url += 'dateDebut=' + searchState.startDate + '&';
        url += 'dateFin=' + searchState.endDate + '&';
        
        // Ajouter le nombre de voyageurs
        url += 'nbAdultes=' + searchState.adults + '&';
        url += 'nbEnfants=' + searchState.children;
        
        window.location.href = url;
    }
    
    // Notification temporaire dans la barre de recherche
    function showSearchNotification(message) {
        // Supprimer l'ancienne notification si elle existe
        const oldNotif = document.getElementById('search-notification');
        if (oldNotif) oldNotif.remove();
        
        const notif = document.createElement('div');
        notif.id = 'search-notification';
        notif.className = 'fixed top-24 left-1/2 transform -translate-x-1/2 bg-clubmed-gold text-black px-6 py-3 rounded-full shadow-lg font-semibold text-sm z-[200] animate-bounce';
        notif.textContent = message;
        document.body.appendChild(notif);
        
        setTimeout(() => {
            notif.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }
    
    function submitSearch() {
        const query = document.getElementById('search-input').value.trim();
        let url = '/resorts?';
        
        if (query) url += 'search=' + encodeURIComponent(query) + '&';
        if (searchState.startDate) url += 'date_debut=' + searchState.startDate + '&';
        if (searchState.endDate) url += 'date_fin=' + searchState.endDate + '&';
        url += 'adultes=' + searchState.adults + '&';
        url += 'enfants=' + searchState.children;
        
        window.location.href = url;
    }
    
    // Événements clavier
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSearchModal();
        if (e.key === 'Enter' && document.getElementById('search-modal').classList.contains('active')) {
            if (searchState.currentTab === 'destination') submitSearch();
        }
    });
    
    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        updateTravelersButtons();
    });
</script>
