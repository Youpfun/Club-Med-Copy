@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-orange-50">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
        <div class="container mx-auto px-4 py-6">
            <nav class="flex items-center text-white/80 text-sm mb-2">
                <a href="/" class="hover:text-white">Accueil</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('cart.index') }}" class="hover:text-white">Panier</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white font-medium">Modifier la réservation</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold">{{ $resort->nomresort }}</h1>
            <p class="text-white/90 text-sm">Réservation #{{ $reservation->numreservation }}</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="editReservationForm" action="{{ route('reservation.update.complete', $reservation->numreservation) }}" method="POST">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="flex-1 space-y-6">
                    
                    {{-- DATES --}}
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
                                       value="{{ $reservation->datedebut }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de départ</label>
                                <input type="date" name="dateFin" id="dateFin" required 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ $reservation->datefin }}">
                            </div>
                        </div>
                    </div>

                    {{-- VOYAGEURS --}}
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
                                        <p class="text-xs text-gray-500">15 ans et plus</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeValue('nbAdultes', -1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">−</button>
                                        <input type="number" id="nbAdultes" name="nbAdultes" value="{{ $nbAdultes }}" min="1" max="20" readonly 
                                               class="w-16 text-center border-2 border-gray-200 rounded-lg font-semibold text-lg">
                                        <button type="button" onclick="changeValue('nbAdultes', 1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">Enfants</p>
                                        <p class="text-xs text-gray-500">Moins de 15 ans</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeValue('nbEnfants', -1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">−</button>
                                        <input type="number" id="nbEnfants" name="nbEnfants" value="{{ $nbEnfants }}" min="0" max="10" readonly 
                                               class="w-16 text-center border-2 border-gray-200 rounded-lg font-semibold text-lg">
                                        <button type="button" onclick="changeValue('nbEnfants', 1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INFORMATIONS DES PARTICIPANTS --}}
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
                            {{-- Généré dynamiquement --}}
                        </div>
                    </div>

                    {{-- TRANSPORT PAR PARTICIPANT --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </span>
                            Transport
                        </h2>
                        <div id="transports-forms" class="space-y-4">
                            {{-- Généré dynamiquement --}}
                        </div>
                    </div>

                    {{-- CHAMBRES --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </span>
                            Choisissez vos chambres
                        </h2>
                        <div class="space-y-4">
                            @foreach($typeChambres as $type)
                                <div class="chambre-card border-2 border-gray-200 rounded-xl p-4 transition-all" data-numtype="{{ $type->numtype }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-lg text-gray-800">{{ $type->nomtype }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ $type->surface }} m² • Max {{ $type->capacitemax }} pers.</p>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-2xl font-bold text-orange-500">{{ number_format($type->prix, 0, ',', ' ') }} €</span>
                                                <span class="text-sm text-gray-500">/nuit</span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <div class="flex items-center gap-2">
                                                <button type="button" onclick="changeChambresQty({{ $type->numtype }}, -1)" 
                                                        class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100 font-bold text-lg">−</button>
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <span class="font-bold text-xl" id="display-qty-{{ $type->numtype }}">{{ $chambresSelectionnees[$type->numtype] ?? 0 }}</span>
                                                </div>
                                                <button type="button" onclick="changeChambresQty({{ $type->numtype }}, 1)" 
                                                        class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100 font-bold text-lg">+</button>
                                            </div>
                                            <input type="hidden" name="chambres[{{ $type->numtype }}]" value="{{ $chambresSelectionnees[$type->numtype] ?? 0 }}" class="chambre-quantity" id="chambre-qty-{{ $type->numtype }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ACTIVITÉS --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-pink-100 text-pink-500 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            Activités (optionnel)
                        </h2>
                        <div id="activites-forms" class="space-y-4">
                            {{-- Généré dynamiquement --}}
                        </div>
                    </div>

                </div>

                {{-- SIDEBAR RÉCAPITULATIF --}}
                <div class="lg:w-96">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 sticky top-4">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-5 text-white">
                            <h3 class="font-bold text-lg">Récapitulatif</h3>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Dates</span>
                                <span class="text-sm font-medium" id="recap-dates">--</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Durée</span>
                                <span class="text-sm font-medium" id="recap-nuits">--</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Voyageurs</span>
                                <span class="text-sm font-medium"><span id="recap-adultes">{{ $nbAdultes }}</span> adulte(s) + <span id="recap-enfants">{{ $nbEnfants }}</span> enfant(s)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Chambre(s)</span>
                                <span class="text-sm font-medium" id="recap-chambre">--</span>
                            </div>

                            <div class="border-t pt-4 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-800 font-semibold">Total estimé</span>
                                    <span class="text-2xl font-bold text-orange-500" id="recap-total">{{ number_format($reservation->prixtotal, 0, ',', ' ') }} €</span>
                                </div>
                            </div>

                            <div class="pt-4 space-y-3 border-t">
                                <button type="submit" id="submitBtn"
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                                    <span id="submitText">Enregistrer les modifications</span>
                                    <span id="submitLoader" class="hidden">
                                        <svg class="animate-spin h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Enregistrement...
                                    </span>
                                </button>
                                <a href="{{ route('panier.show', $reservation->numreservation) }}" 
                                   class="block w-full text-center border-2 border-gray-300 text-gray-600 py-3 px-6 rounded-xl font-semibold hover:bg-gray-100 transition-all">
                                    Annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
// ==================== DONNÉES PHP ====================
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

const prixChambres = {
    @foreach($typeChambres as $type)
        {{ $type->numtype }}: {{ $type->prix }},
    @endforeach
};

const transports = @json($transports);
const activites = @json($activites);
const participantsExistants = @json($participants);
const activitesSelectionnees = @json($activitesSelectionnees ?? []);
const prixOriginal = {{ $reservation->prixtotal }};

// ==================== VARIABLES GLOBALES ====================
let personnes = [];
let nextPersonId = 1;

// ==================== INITIALISATION DES PARTICIPANTS EXISTANTS ====================
if (participantsExistants && participantsExistants.length > 0) {
    participantsExistants.forEach(p => {
        const age = p.datenaissanceparticipant ? 
            Math.floor((new Date() - new Date(p.datenaissanceparticipant)) / (365.25 * 24 * 60 * 60 * 1000)) : 18;
        
        const activitesPersonne = activitesSelectionnees[p.numparticipant] || [];
        
        personnes.push({
            id: nextPersonId++,
            type: age >= 15 ? 'adulte' : 'enfant',
            genre: p.genreparticipant || '',
            prenom: p.prenomparticipant || '',
            nom: (p.nomparticipant || '').replace(/^(Adulte|Enfant) \d+ - /, ''),
            datenaissance: p.datenaissanceparticipant || '',
            transport: p.numtransport || (transports.length > 0 ? transports[0].numtransport : null),
            activites: activitesPersonne
        });
    });
}

// ==================== DOM READY ====================
document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('dateDebut');
    const dateFin = document.getElementById('dateFin');
    const nbAdultes = document.getElementById('nbAdultes');
    const nbEnfants = document.getElementById('nbEnfants');
    
    // ==================== GESTION ADULTES/ENFANTS ====================
    window.changeValue = function(id, delta) {
        const input = document.getElementById(id);
        let newVal = parseInt(input.value) + delta;
        if (newVal >= parseInt(input.min) && newVal <= parseInt(input.max)) {
            // Vérifier les contraintes de chambres avant de modifier
            const currentAdultes = parseInt(nbAdultes.value);
            const currentEnfants = parseInt(nbEnfants.value);
            const newTotalPersonnes = (id === 'nbAdultes' ? newVal : currentAdultes) + (id === 'nbEnfants' ? newVal : currentEnfants);
            
            // Compter le nombre total de chambres
            let totalChambres = 0;
            document.querySelectorAll('.chambre-quantity').forEach(inp => {
                totalChambres += parseInt(inp.value) || 0;
            });
            
            // Si on diminue le nombre de personnes, vérifier qu'on n'a pas trop de chambres
            if (delta < 0 && totalChambres > newTotalPersonnes) {
                alert(`Vous avez ${totalChambres} chambre(s) réservée(s).\nVous ne pouvez pas avoir moins de ${totalChambres} voyageur(s).\n\nRetirez d'abord des chambres.`);
                return;
            }
            
            // Vérifier la capacité minimale
            let capaciteTotale = 0;
            document.querySelectorAll('.chambre-quantity').forEach(inp => {
                const qty = parseInt(inp.value) || 0;
                if (qty > 0) {
                    const numtype = inp.name.match(/\[(\d+)\]/)[1];
                    const capacite = capaciteChambres[numtype] || 2;
                    capaciteTotale += capacite * qty;
                }
            });
            
            // Si on augmente et que la capacité ne suffit pas
            if (delta > 0 && capaciteTotale > 0 && capaciteTotale < newTotalPersonnes) {
                alert(`Capacité insuffisante !\nVoyageurs : ${newTotalPersonnes}\nCapacité des chambres : ${capaciteTotale}\n\nAjoutez plus de chambres pour accueillir tous les voyageurs.`);
                return;
            }
            
            // IMPORTANT : Sauvegarder AVANT de modifier le tableau
            saveAllPersonnesData();
            
            input.value = newVal;
            
            if (id === 'nbAdultes') {
                if (delta > 0) {
                    personnes.push({
                        id: nextPersonId++,
                        type: 'adulte',
                        genre: '',
                        prenom: '',
                        nom: '',
                        datenaissance: '',
                        transport: transports.length > 0 ? transports[0].numtransport : null,
                        activites: []
                    });
                } else {
                    const adultes = personnes.filter(p => p.type === 'adulte');
                    if (adultes.length > 0) {
                        const lastAdulte = adultes[adultes.length - 1];
                        personnes = personnes.filter(p => p.id !== lastAdulte.id);
                    }
                }
            } else if (id === 'nbEnfants') {
                if (delta > 0) {
                    personnes.push({
                        id: nextPersonId++,
                        type: 'enfant',
                        genre: '',
                        prenom: '',
                        nom: '',
                        datenaissance: '',
                        transport: transports.length > 0 ? transports[0].numtransport : null,
                        activites: []
                    });
                } else {
                    const enfants = personnes.filter(p => p.type === 'enfant');
                    if (enfants.length > 0) {
                        const lastEnfant = enfants[enfants.length - 1];
                        personnes = personnes.filter(p => p.id !== lastEnfant.id);
                    }
                }
            }
            
            // Passer true pour ne PAS re-sauvegarder (déjà fait ci-dessus)
            updateAll(true);
        }
    };
    
    // ==================== SUPPRESSION PARTICIPANT ====================
    window.removeParticipant = function(personId) {
        const personne = personnes.find(p => p.id === personId);
        if (!personne) return;
        
        if (personne.type === 'adulte') {
            const currentAdultes = parseInt(nbAdultes.value);
            if (currentAdultes <= 1) {
                alert('Vous devez avoir au moins un adulte.');
                return;
            }
            nbAdultes.value = currentAdultes - 1;
        } else {
            const currentEnfants = parseInt(nbEnfants.value);
            if (currentEnfants <= 0) return;
            nbEnfants.value = currentEnfants - 1;
        }
        
        // IMPORTANT : Sauvegarder AVANT de supprimer
        saveAllPersonnesData();
        personnes = personnes.filter(p => p.id !== personId);
        
        // Passer true pour ne PAS re-sauvegarder
        updateAll(true);
    };
    
    // ==================== SAUVEGARDE DONNÉES ====================
    function savePersonneData(personId, recalculateType = false) {
        const personne = personnes.find(p => p.id === personId);
        if (!personne) return;
        
        const prefix = `participants[person_${personId}]`;
        personne.genre = document.querySelector(`select[name="${prefix}[genre]"]`)?.value || '';
        personne.prenom = document.querySelector(`input[name="${prefix}[prenom]"]`)?.value || '';
        personne.nom = document.querySelector(`input[name="${prefix}[nom]"]`)?.value || '';
        personne.datenaissance = document.querySelector(`input[name="${prefix}[datenaissance]"]`)?.value || '';
        
        // Ne recalculer le type que si demandé (changement de date)
        if (recalculateType && personne.datenaissance) {
            const age = Math.floor((new Date() - new Date(personne.datenaissance)) / (365.25 * 24 * 60 * 60 * 1000));
            personne.type = age >= 15 ? 'adulte' : 'enfant';
        }
        
        const transportRadio = document.querySelector(`input[name="transport_person_${personId}"]:checked`);
        if (transportRadio) {
            personne.transport = parseInt(transportRadio.value);
        }
        
        personne.activites = [];
        document.querySelectorAll(`input[name^="activites["][name$="[person_${personId}]"]:checked`).forEach(checkbox => {
            const match = checkbox.name.match(/activites\[(\d+)\]/);
            if (match) {
                personne.activites.push(parseInt(match[1]));
            }
        });
    }
    
    function saveAllPersonnesData() {
        personnes.forEach(p => savePersonneData(p.id));
    }
    
    // ==================== EVENT LISTENERS ====================
    // Écouter les changements dans les champs participants
    document.addEventListener('input', function(e) {
        // Pour les inputs texte (prénom, nom)
        if (e.target.matches('input[type="text"][name^="participants["]')) {
            const match = e.target.name.match(/participants\[person_(\d+)\]/);
            if (match) {
                const personId = parseInt(match[1]);
                savePersonneData(personId, false);
                // Mise à jour en temps réel des noms affichés
                updateTransportAndActivitesNames();
            }
        }
    });
    
    document.addEventListener('change', function(e) {
        // Pour les selects (civilité)
        if (e.target.matches('select[name^="participants["]')) {
            const match = e.target.name.match(/participants\[person_(\d+)\]/);
            if (match) {
                const personId = parseInt(match[1]);
                savePersonneData(personId, false);
            }
        }
        
        // Pour les dates de naissance - recalculer le type
        if (e.target.matches('input[type="date"][name^="participants["]')) {
            const match = e.target.name.match(/participants\[person_(\d+)\]/);
            if (match) {
                const personId = parseInt(match[1]);
                savePersonneData(personId, true);
            }
        }
    });
    
    function updateTransportAndActivitesNames() {
        // Mettre à jour uniquement les éléments qui affichent les noms (hors formulaires)
        document.querySelectorAll('[data-person-id]').forEach(el => {
            // Ne pas toucher aux formulaires participants
            if (el.closest('.participant-card')) return;
            
            const personId = parseInt(el.dataset.personId);
            const personne = personnes.find(p => p.id === personId);
            if (personne) {
                const displayName = (personne.prenom && personne.nom) 
                    ? `${personne.prenom} ${personne.nom}` 
                    : (personne.type === 'adulte' ? 'Adulte' : 'Enfant');
                el.textContent = displayName;
            }
        });
    }
    
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('transport-radio')) {
            const match = e.target.name.match(/transport_person_(\d+)/);
            if (match) {
                const personId = parseInt(match[1]);
                savePersonneData(personId);
            }
            
            const card = e.target.nextElementSibling;
            const group = e.target.closest('.transport-group');
            
            if (group) {
                group.querySelectorAll('.transport-card').forEach(c => {
                    c.classList.remove('border-purple-500', 'bg-purple-50', 'shadow-lg');
                    c.classList.add('border-gray-200');
                });
                
                if (card && e.target.checked) {
                    card.classList.add('border-purple-500', 'bg-purple-50', 'shadow-lg');
                    card.classList.remove('border-gray-200');
                }
            }
            
            calculateTotalPrice();
        }
        
        if (e.target.matches('input[name^="activites["]')) {
            const match = e.target.name.match(/activites\[(\d+)\]\[person_(\d+)\]/);
            if (match) {
                const personId = parseInt(match[2]);
                savePersonneData(personId);
            }
            calculateTotalPrice();
        }
    });
    
    // ==================== GESTION CHAMBRES ====================
    window.changeChambresQty = function(numtype, delta) {
        const input = document.getElementById('chambre-qty-' + numtype);
        const display = document.getElementById('display-qty-' + numtype);
        const card = document.querySelector('.chambre-card[data-numtype="' + numtype + '"]');
        
        let newVal = parseInt(input.value) + delta;
        if (newVal < 0) newVal = 0;
        
        // Vérifier le nombre total de personnes
        const totalPersonnes = parseInt(nbAdultes.value) + parseInt(nbEnfants.value);
        
        // Calculer le nombre total de chambres si on applique ce changement
        let totalChambres = 0;
        document.querySelectorAll('.chambre-quantity').forEach(inp => {
            if (inp.id === 'chambre-qty-' + numtype) {
                totalChambres += newVal;
            } else {
                totalChambres += parseInt(inp.value) || 0;
            }
        });
        
        // Vérification : maximum une chambre par personne
        if (totalChambres > totalPersonnes) {
            alert(`Vous ne pouvez pas réserver plus de chambres que de voyageurs.\nVoyageurs : ${totalPersonnes}\nChambres demandées : ${totalChambres}`);
            return;
        }
        
        // Vérification : capacité minimale
        let capaciteTotale = 0;
        document.querySelectorAll('.chambre-quantity').forEach(inp => {
            const qty = (inp.id === 'chambre-qty-' + numtype) ? newVal : (parseInt(inp.value) || 0);
            if (qty > 0) {
                const numtypeInput = inp.name.match(/\[(\d+)\]/)[1];
                const capacite = capaciteChambres[numtypeInput] || 2;
                capaciteTotale += capacite * qty;
            }
        });
        
        // Si on diminue et que ça ne suffit plus, vérifier
        if (delta < 0 && capaciteTotale < totalPersonnes) {
            alert(`Capacité insuffisante !\nVoyageurs : ${totalPersonnes}\nCapacité totale des chambres : ${capaciteTotale}\n\nVous devez avoir assez de place pour tous les voyageurs.`);
            return;
        }
        
        if (newVal > 10) newVal = 10;
        
        input.value = newVal;
        display.textContent = newVal;
        
        if (newVal > 0) {
            card.classList.add('border-orange-500', 'bg-orange-50', 'shadow-md');
            card.classList.remove('border-gray-200');
        } else {
            card.classList.remove('border-orange-500', 'bg-orange-50', 'shadow-md');
            card.classList.add('border-gray-200');
        }
        
        updateRecap();
        calculateTotalPrice();
    };
    
    // ==================== MISE À JOUR GÉNÉRALE ====================
    function updateAll(skipSave = false) {
        if (!skipSave) {
            saveAllPersonnesData();
        }
        generateParticipantsForms();
        generateTransportsForms();
        generateActivitesForms();
        updateRecap();
        calculateTotalPrice();
    }
    
    // ==================== RÉCAPITULATIF ====================
    function updateRecap() {
        const adultes = parseInt(nbAdultes.value) || 0;
        const enfants = parseInt(nbEnfants.value) || 0;
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
        
        let chambresTexte = [];
        document.querySelectorAll('.chambre-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const numtype = input.name.match(/\[(\d+)\]/)[1];
                const nom = nomsChambres[numtype];
                chambresTexte.push(`${qty}x ${nom}`);
            }
        });
        
        document.getElementById('recap-chambre').innerHTML = chambresTexte.length > 0 
            ? chambresTexte.join('<br>') 
            : '--';
    }
    
    // ==================== CALCUL PRIX AVEC HT/TTC ====================
    function calculateTotalPrice() {
        let totalHT = 0;
        let detailsPrix = {
            chambres: 0,
            transports: 0,
            activites: 0
        };
        
        // Nombre de nuits (correction du calcul)
        let nuits = 1;
        if (dateDebut.value && dateFin.value) {
            const d1 = new Date(dateDebut.value);
            const d2 = new Date(dateFin.value);
            nuits = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
            if (nuits < 1) nuits = 1;
        }
        
        // Prix chambres (par nuit × quantité × nombre de nuits)
        document.querySelectorAll('.chambre-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const numtype = input.name.match(/\[(\d+)\]/)[1];
                const prixParNuit = parseFloat(prixChambres[numtype]) || 0;
                const sousTotal = prixParNuit * qty * nuits;
                detailsPrix.chambres += sousTotal;
                totalHT += sousTotal;
            }
        });
        
        // Prix transports (par personne)
        document.querySelectorAll('input[name^="transport_"]:checked').forEach(radio => {
            const transportId = parseInt(radio.value);
            const transport = transports.find(t => t.numtransport === transportId);
            if (transport) {
                const prix = parseFloat(transport.prixtransport || 0);
                detailsPrix.transports += prix;
                totalHT += prix;
            }
        });
        
        // Prix activités (par personne cochée)
        document.querySelectorAll('input[name^="activites["]:checked').forEach(checkbox => {
            const match = checkbox.name.match(/activites\[(\d+)\]/);
            if (match) {
                const numactivite = parseInt(match[1]);
                const activite = activites.find(a => a.numactivite === numactivite);
                if (activite) {
                    const prix = parseFloat(activite.prixmin || 0);
                    detailsPrix.activites += prix;
                    totalHT += prix;
                }
            }
        });
        
        // Calculer TTC avec TVA 20%
        const tauxTVA = 0.20;
        const montantTVA = totalHT * tauxTVA;
        const totalTTC = totalHT + montantTVA;
        
        // Afficher avec détails
        const totalElement = document.getElementById('recap-total');
        if (totalElement) {
            let detailsHTML = '';
            if (detailsPrix.chambres > 0) {
                detailsHTML += `<div class="text-xs text-gray-500">Chambres (${nuits} nuit${nuits > 1 ? 's' : ''}): ${formatPrice(detailsPrix.chambres)}</div>`;
            }
            if (detailsPrix.transports > 0) {
                detailsHTML += `<div class="text-xs text-gray-500">Transports: ${formatPrice(detailsPrix.transports)}</div>`;
            }
            if (detailsPrix.activites > 0) {
                detailsHTML += `<div class="text-xs text-gray-500">Activités: ${formatPrice(detailsPrix.activites)}</div>`;
            }
            
            totalElement.innerHTML = `
                <div class="text-right space-y-1">
                    ${detailsHTML}
                    <div class="border-t pt-2 mt-2">
                        <div class="text-sm text-gray-600">Total HT: ${formatPrice(totalHT)}</div>
                        <div class="text-sm text-gray-600">TVA (20%): ${formatPrice(montantTVA)}</div>
                        <div class="text-2xl font-bold text-orange-500 mt-1">${formatPrice(totalTTC)}</div>
                    </div>
                </div>
            `;
        }
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(price);
    }
    
    // ==================== GÉNÉRATION FORMULAIRES ====================
    function generateParticipantsForms() {
        const container = document.getElementById('participants-forms');
        if (!container) return;
        
        container.innerHTML = '';
        
        const adultes = personnes.filter(p => p.type === 'adulte');
        const enfants = personnes.filter(p => p.type === 'enfant');
        
        adultes.forEach(personne => {
            container.innerHTML += createParticipantForm(personne);
        });
        
        enfants.forEach(personne => {
            container.innerHTML += createParticipantForm(personne);
        });
    }
    
    function createParticipantForm(personne) {
        const isAdulte = personne.type === 'adulte';
        const label = isAdulte ? 'Adulte' : 'Enfant';
        const bgColor = isAdulte ? 'bg-blue-50 border-blue-200' : 'bg-purple-50 border-purple-200';
        const iconColor = isAdulte ? 'text-blue-600' : 'text-purple-600';
        
        const genreM = personne.genre === 'M.' ? 'selected' : '';
        const genreMme = personne.genre === 'Mme' ? 'selected' : '';
        
        // Calculer les dates limites
        const today = new Date();
        const maxDateAdulte = new Date(today.getFullYear() - 15, today.getMonth(), today.getDate());
        const minDateAdulte = new Date(1920, 0, 1);
        const maxDateEnfant = today;
        const minDateEnfant = new Date(today.getFullYear() - 15, today.getMonth(), today.getDate());
        
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        const dateMin = isAdulte ? formatDate(minDateAdulte) : formatDate(minDateEnfant);
        const dateMax = isAdulte ? formatDate(maxDateAdulte) : formatDate(maxDateEnfant);
        
        return `
            <div class="${bgColor} border-2 rounded-xl p-4 participant-card" data-person-id="${personne.id}">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-bold ${iconColor} flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>${label}</span>
                    </h3>
                    <button type="button" onclick="removeParticipant(${personne.id})" 
                            class="text-red-500 hover:text-red-700 font-bold text-xl">×</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civilité *</label>
                        <select name="participants[person_${personne.id}][genre]" required 
                                class="w-full border-2 border-gray-200 rounded-lg p-2 focus:border-blue-500">
                            <option value="">Sélectionner</option>
                            <option value="M." ${genreM}>M.</option>
                            <option value="Mme" ${genreMme}>Mme</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                        <input type="text" name="participants[person_${personne.id}][prenom]" 
                               value="${personne.prenom}" required 
                               class="w-full border-2 border-gray-200 rounded-lg p-2 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="participants[person_${personne.id}][nom]" 
                               value="${personne.nom}" required 
                               class="w-full border-2 border-gray-200 rounded-lg p-2 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance *</label>
                        <input type="date" name="participants[person_${personne.id}][datenaissance]" 
                               value="${personne.datenaissance}" required 
                               min="${dateMin}" max="${dateMax}"
                               class="w-full border-2 border-gray-200 rounded-lg p-2 focus:border-blue-500">
                    </div>
                </div>
            </div>
        `;
    }
    
    function generateTransportsForms() {
        const container = document.getElementById('transports-forms');
        if (!container) return;
        
        container.innerHTML = '';
        
        const adultes = personnes.filter(p => p.type === 'adulte');
        const enfants = personnes.filter(p => p.type === 'enfant');
        
        adultes.forEach(personne => {
            container.innerHTML += createTransportForm(personne);
        });
        
        enfants.forEach(personne => {
            container.innerHTML += createTransportForm(personne);
        });
        
        setTimeout(() => {
            document.querySelectorAll('.transport-radio:checked').forEach(radio => {
                const card = radio.nextElementSibling;
                if (card && card.classList.contains('transport-card')) {
                    card.classList.add('border-purple-500', 'bg-purple-50', 'shadow-lg');
                    card.classList.remove('border-gray-200');
                }
            });
        }, 100);
    }
    
    function createTransportForm(personne) {
        const isAdulte = personne.type === 'adulte';
        const bgColor = isAdulte ? 'text-blue-600' : 'text-purple-600';
        const displayName = (personne.prenom && personne.nom) 
            ? `${personne.prenom} ${personne.nom}` 
            : (personne.type === 'adulte' ? 'Adulte' : 'Enfant');
        
        let transportOptions = '';
        transports.forEach(t => {
            const isSelected = personne.transport === t.numtransport;
            transportOptions += `
                <label class="cursor-pointer transport-label" data-transport-id="${t.numtransport}">
                    <input type="radio" name="transport_person_${personne.id}" value="${t.numtransport}" 
                           class="hidden transport-radio" ${isSelected ? 'checked' : ''}>
                    <div class="border-2 border-gray-200 rounded-lg p-3 transition-all hover:border-gray-300 transport-card">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-800 text-sm">${t.nomtransport}</span>
                            </div>
                            <span class="font-bold text-purple-600 text-sm">${t.prixtransport} €</span>
                        </div>
                    </div>
                </label>
            `;
        });
        
        return `
            <div class="border-2 border-gray-200 rounded-xl p-4">
                <h4 class="font-bold ${bgColor} mb-3" data-person-id="${personne.id}">${displayName}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 transport-group">
                    ${transportOptions}
                </div>
            </div>
        `;
    }
    
    function generateActivitesForms() {
        const container = document.getElementById('activites-forms');
        
        if (!container || activites.length === 0) {
            if (container) container.innerHTML = '<p class="text-gray-500 text-sm">Aucune activité disponible</p>';
            return;
        }
        
        container.innerHTML = '';
        
        activites.forEach(activite => {
            container.innerHTML += `
                <div class="border-2 border-purple-200 rounded-xl p-4 bg-purple-50">
                    <h4 class="font-bold text-purple-800 mb-2">${activite.nomactivite}</h4>
                    <p class="text-sm text-gray-600 mb-3">${activite.descriptionactivite || ''}</p>
                    <p class="text-sm font-semibold text-purple-600 mb-3">Prix : ${activite.prixmin} €/pers.</p>
                    <div class="space-y-2">
                        ${generateActiviteParticipants(activite.numactivite)}
                    </div>
                </div>
            `;
        });
    }
    
    function generateActiviteParticipants(numactivite) {
        let html = '<p class="text-xs font-semibold text-gray-700 mb-2">Participants :</p><div class="grid grid-cols-2 gap-2">';
        
        const adultes = personnes.filter(p => p.type === 'adulte');
        const enfants = personnes.filter(p => p.type === 'enfant');
        
        adultes.forEach(personne => {
            const displayName = (personne.prenom && personne.nom) 
                ? `${personne.prenom} ${personne.nom}` 
                : 'Adulte';
            const isChecked = personne.activites.includes(numactivite);
            
            html += `
                <label class="flex items-center space-x-2 text-sm">
                    <input type="checkbox" name="activites[${numactivite}][person_${personne.id}]" value="1" 
                           ${isChecked ? 'checked' : ''}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span data-person-id="${personne.id}">${displayName}</span>
                </label>
            `;
        });
        
        enfants.forEach(personne => {
            const displayName = (personne.prenom && personne.nom) 
                ? `${personne.prenom} ${personne.nom}` 
                : 'Enfant';
            const isChecked = personne.activites.includes(numactivite);
            
            html += `
                <label class="flex items-center space-x-2 text-sm">
                    <input type="checkbox" name="activites[${numactivite}][person_${personne.id}]" value="1" 
                           ${isChecked ? 'checked' : ''}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span data-person-id="${personne.id}">${displayName}</span>
                </label>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    // ==================== EVENT LISTENERS DATES ====================
    dateDebut.addEventListener('change', function() {
        updateRecap();
        calculateTotalPrice();
    });
    
    dateFin.addEventListener('change', function() {
        updateRecap();
        calculateTotalPrice();
    });
    
    // ==================== SOUMISSION FORMULAIRE ====================
    document.getElementById('editReservationForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoader = document.getElementById('submitLoader');
        
        submitBtn.disabled = true;
        submitText.classList.add('hidden');
        submitLoader.classList.remove('hidden');
    });
    
    // ==================== INITIALISATION ====================
    updateAll(true);
    
    document.querySelectorAll('.chambre-quantity').forEach(input => {
        if (parseInt(input.value) > 0) {
            const numtype = input.name.match(/\[(\d+)\]/)[1];
            const card = document.querySelector('.chambre-card[data-numtype="' + numtype + '"]');
            if (card) {
                card.classList.add('border-orange-500', 'bg-orange-50', 'shadow-md');
                card.classList.remove('border-gray-200');
            }
        }
    });
});
</script>
@endsection
