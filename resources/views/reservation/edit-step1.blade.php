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
                <span class="text-white font-medium">Modifier - Séjour</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold">{{ $resort->nomresort }}</h1>
            <p class="text-white/90 text-sm">Modification de la réservation #{{ $reservation->numreservation }}</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <form id="step1Form" action="{{ route('reservation.update.step1', $reservation->numreservation) }}" method="POST">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="flex-1 space-y-6">
                    
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
                                       max="{{ date('Y-m-d', strtotime('+3 years')) }}"
                                       value="{{ $reservation->datedebut }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date de départ</label>
                                <input type="date" name="dateFin" id="dateFin" required 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-0 transition-colors" 
                                       min="{{ date('Y-m-d') }}"
                                       max="{{ date('Y-m-d', strtotime('+3 years')) }}"
                                       value="{{ $reservation->datefin }}">
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
                                        <p class="text-xs text-gray-500">12 ans et plus</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeValue('nbAdultes', -1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">−</button>
                                        <input type="number" id="nbAdultes" name="nbAdultes" value="{{ $reservation->nbpersonnes }}" min="1" max="20" readonly 
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
                                        <p class="text-xs text-gray-500">2 à 11 ans</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" onclick="changeValue('nbEnfants', -1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">−</button>
                                        <input type="number" id="nbEnfants" name="nbEnfants" value="0" min="0" max="10" readonly 
                                               class="w-16 text-center border-2 border-gray-200 rounded-lg font-semibold text-lg">
                                        <button type="button" onclick="changeValue('nbEnfants', 1)" 
                                                class="w-10 h-10 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-100">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-500 flex items-center justify-center mr-3">
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
                                            <div class="flex items-center gap-3 mt-2">
                                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full mb-1 hidden" id="badge-promo-{{ $type->numtype }}">Prix spécial !</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-2" id="container-prix-{{ $type->numtype }}">
                                                <span class="text-gray-400 line-through text-sm hidden" id="prix-barre-{{ $type->numtype }}"></span>
                                                <span class="text-2xl font-bold text-orange-500"><span id="prix-{{ $type->numtype }}">--</span> €</span>
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

                </div>

                <!-- Sidebar -->
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
                                <span class="text-sm font-medium"><span id="recap-adultes">1</span> adulte(s) + <span id="recap-enfants">0</span> enfant(s)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Chambre(s)</span>
                                <span class="text-sm font-medium" id="recap-chambre">--</span>
                            </div>

                            <div class="border-t pt-4">
                                <div id="prix-placeholder" class="text-center text-gray-400 text-sm">
                                    Sélectionnez vos dates et chambres
                                </div>
                                <div id="prix-detail" class="hidden space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total HT</span>
                                        <span class="font-medium" id="recap-total-ht">--</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">TVA (20%)</span>
                                        <span class="font-medium" id="recap-tva">--</span>
                                    </div>
                                    <div class="border-t border-gray-300 my-2"></div>
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-gray-800 font-semibold">Total TTC</span>
                                        <span class="text-2xl font-bold text-orange-500" id="recap-total">--</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 border-t border-gray-100 space-y-3">
                                <button type="submit" 
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:from-blue-600 hover:to-blue-700 transition-all">
                                    Enregistrer
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
        {{ $type->numtype }}: {{ $type->prix ?? 0 }},
    @endforeach
};

document.addEventListener('DOMContentLoaded', function() {
    const dateDebut = document.getElementById('dateDebut');
    const dateFin = document.getElementById('dateFin');
    const nbAdultes = document.getElementById('nbAdultes');
    const nbEnfants = document.getElementById('nbEnfants');
    
    // Afficher les prix au chargement
    @foreach($typeChambres as $type)
        document.getElementById('prix-{{ $type->numtype }}').textContent = {{ $type->prix ?? 0 }};
    @endforeach

    function changeValue(id, delta) {
        const input = document.getElementById(id);
        let newVal = parseInt(input.value) + delta;
        if (newVal >= input.min && newVal <= input.max) {
            input.value = newVal;
            updateRecap();
        }
    }
    window.changeValue = changeValue;
    
    function changeChambresQty(numtype, delta) {
        const input = document.getElementById('chambre-qty-' + numtype);
        const display = document.getElementById('display-qty-' + numtype);
        const card = document.querySelector('.chambre-card[data-numtype="' + numtype + '"]');
        
        let newVal = parseInt(input.value) + delta;
        if (newVal < 0) newVal = 0;
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
    }
    window.changeChambresQty = changeChambresQty;

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
    
    dateDebut.addEventListener('change', function() {
        if (dateFin.value && new Date(dateFin.value) <= new Date(dateDebut.value)) dateFin.value = '';
        dateFin.min = dateDebut.value;
        updateRecap();
    });
    
    dateFin.addEventListener('change', function() {
        if (dateDebut.value && new Date(dateFin.value) <= new Date(dateDebut.value)) {
            alert('Date de départ invalide');
            dateFin.value = '';
            return;
        }
        updateRecap();
    });
    
    nbAdultes.addEventListener('change', updateRecap);
    nbEnfants.addEventListener('change', updateRecap);

    updateRecap();
});
</script>
@endsection
