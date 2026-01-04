@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-orange-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-[#113559] to-blue-800 text-white">
        <div class="container mx-auto px-4 py-6">
            <nav class="flex items-center text-white/80 text-sm mb-2">
                <a href="{{ route('reservations.index') }}" class="hover:text-white">Mes réservations</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('reservation.show', $reservation->numreservation) }}" class="hover:text-white">Réservation #{{ $reservation->numreservation }}</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-white font-medium">Ajouter des activités</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-bold">Ajouter des activités</h1>
            <p class="text-white/90">{{ $reservation->resort->nomresort }} - {{ $reservation->resort->pays->nompays ?? '' }}</p>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container mx-auto px-4 py-8">
        <form action="{{ route('activities.checkout', $reservation->numreservation) }}" method="POST" id="activitiesForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Colonne principale -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Section Activités -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-[#113559] px-6 py-4">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Activités disponibles
                            </h2>
                            <p class="text-white/80 text-sm mt-1">Sélectionnez les participants pour chaque activité supplémentaire</p>
                        </div>
                        
                        <div class="p-6">
                            @if($activites->count() > 0)
                                <div class="space-y-6">
                                    @foreach($activites as $activite)
                                        <div class="border-2 border-gray-200 rounded-xl p-5 hover:border-[#113559] transition-colors">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex items-start space-x-4">
                                                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-bold text-lg text-gray-800">{{ $activite->nomactivite }}</h3>
                                                        <p class="text-gray-500 text-sm mt-1">{{ $activite->descriptionactivite }}</p>
                                                        <div class="mt-2">
                                                            <span class="text-2xl font-bold text-[#113559]">{{ number_format($activite->prixmin, 0, ',', ' ') }} €</span>
                                                            <span class="text-sm text-gray-500"> / personne</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Sélection des participants -->
                                            <div class="border-t border-gray-200 pt-4 mt-4">
                                                <p class="text-sm font-medium text-gray-700 mb-3">Qui participe à cette activité ?</p>
                                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                    @foreach($reservation->participants as $participant)
                                                        @php
                                                            $dejaReservee = isset($activitesReservees[$participant->numparticipant]) && 
                                                                          in_array($activite->numactivite, $activitesReservees[$participant->numparticipant]);
                                                            $age = \Carbon\Carbon::parse($participant->datenaissanceparticipant)->age;
                                                            $isAdulte = $age >= 15;
                                                        @endphp
                                                        <label class="flex items-center space-x-2 cursor-pointer group {{ $dejaReservee ? 'opacity-60' : '' }}">
                                                            <input type="checkbox" 
                                                                   name="activites[{{ $activite->numactivite }}][]" 
                                                                   value="{{ $participant->numparticipant }}" 
                                                                   class="w-5 h-5 text-[#113559] border-gray-300 rounded focus:ring-[#113559] activity-checkbox"
                                                                   data-prix="{{ $activite->prixmin }}"
                                                                   data-activite="{{ $activite->numactivite }}"
                                                                   {{ $dejaReservee ? 'checked disabled' : '' }}>
                                                            <span class="text-sm text-gray-700 group-hover:text-[#113559] flex items-center">
                                                                <svg class="w-4 h-4 inline mr-1 {{ $isAdulte ? 'text-blue-500' : 'text-pink-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                                {{ $participant->prenomparticipant }} {{ $participant->nomparticipant }}
                                                                @if($dejaReservee)
                                                                    <span class="ml-1 text-xs text-green-600 font-semibold">(✓ Déjà réservée)</span>
                                                                @endif
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucune activité disponible</h3>
                                    <p class="text-gray-500">Toutes les activités ont déjà été réservées pour ce resort.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Colonne récapitulatif -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
                        <div class="bg-[#113559] px-6 py-4">
                            <h3 class="font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Nouvelles activités
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div id="activites-selectionnees" class="text-sm text-gray-500 italic">
                                Aucune nouvelle activité sélectionnée
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Sous-total HT</span>
                                    <span class="font-medium text-gray-800" id="sousTotal">0,00 €</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">TVA (20%)</span>
                                    <span class="font-medium text-gray-800" id="tva">0,00 €</span>
                                </div>
                                <div class="border-t border-dashed border-gray-300 pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-800 text-base">Total TTC</span>
                                        <span class="font-bold text-[#113559] text-2xl" id="totalTTC">0,00 €</span>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" id="btnPayer" disabled
                                    class="w-full bg-[#ffc000] hover:bg-[#e0a800] disabled:bg-gray-300 disabled:cursor-not-allowed text-[#113559] font-bold py-3 px-6 rounded-lg transition-colors shadow-md disabled:shadow-none">
                                Payer les activités supplémentaires
                            </button>
                            
                            <a href="{{ route('reservation.show', $reservation->numreservation) }}" 
                               class="block w-full text-center border border-gray-300 hover:border-[#113559] text-gray-700 hover:text-[#113559] font-semibold py-3 px-6 rounded-lg transition-colors">
                                Retour aux détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.activity-checkbox:not([disabled])');
    const btnPayer = document.getElementById('btnPayer');
    const sousTotalEl = document.getElementById('sousTotal');
    const tvaEl = document.getElementById('tva');
    const totalTTCEl = document.getElementById('totalTTC');
    const activitesSelectionnees = document.getElementById('activites-selectionnees');

    function updatePricing() {
        let total = 0;
        let selectedActivities = {};
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const prix = parseFloat(checkbox.dataset.prix);
                const activiteId = checkbox.dataset.activite;
                const participantLabel = checkbox.nextElementSibling.textContent.trim().replace(/\(✓ Déjà réservée\)/, '').trim();
                
                total += prix;
                
                if (!selectedActivities[activiteId]) {
                    selectedActivities[activiteId] = {
                        nom: checkbox.closest('.border-2').querySelector('h3').textContent,
                        participants: [],
                        prix: prix
                    };
                }
                selectedActivities[activiteId].participants.push(participantLabel);
            }
        });

        const sousTotal = total;
        const tva = sousTotal * 0.20;
        const totalTTC = sousTotal + tva;

        sousTotalEl.textContent = sousTotal.toFixed(2).replace('.', ',') + ' €';
        tvaEl.textContent = tva.toFixed(2).replace('.', ',') + ' €';
        totalTTCEl.textContent = totalTTC.toFixed(2).replace('.', ',') + ' €';

        // Afficher les activités sélectionnées
        if (Object.keys(selectedActivities).length > 0) {
            let html = '<div class="space-y-3">';
            for (let activiteId in selectedActivities) {
                const act = selectedActivities[activiteId];
                html += `
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="font-semibold text-sm text-[#113559] mb-1">${act.nom}</p>
                        <p class="text-xs text-gray-600">${act.participants.join(', ')}</p>
                        <p class="text-sm font-bold text-[#113559] mt-1">${(act.prix * act.participants.length).toFixed(0)} €</p>
                    </div>
                `;
            }
            html += '</div>';
            activitesSelectionnees.innerHTML = html;
            btnPayer.disabled = false;
        } else {
            activitesSelectionnees.innerHTML = '<p class="text-sm text-gray-500 italic">Aucune nouvelle activité sélectionnée</p>';
            btnPayer.disabled = true;
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePricing);
    });

    updatePricing();
});
</script>
@endsection
