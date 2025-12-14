@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('panier.show', $reservation->numreservation) }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour au détail</a>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                <h1 class="text-3xl font-bold">Modifier les activités</h1>
                <p class="mt-2 text-blue-100">Réservation #{{ $reservation->numreservation }}</p>
            </div>
        </div>

        <form action="{{ route('reservation.update.step3', $reservation->numreservation) }}" method="POST" id="activitesForm">
            @csrf

            <!-- Liste des activités disponibles -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800">Sélectionnez les activités pour vos voyageurs</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($activites as $activite)
                    <div class="border-2 border-purple-200 rounded-lg p-5 bg-purple-50 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-purple-900">{{ $activite->nomactivite }}</h3>
                                <p class="text-sm text-purple-700 italic mt-1">{{ $activite->descriptionactivite }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-xl font-bold text-purple-800">{{ number_format($activite->prixmin, 0, ',', ' ') }} €</p>
                                <p class="text-xs text-purple-600">par personne</p>
                            </div>
                        </div>

                        <div class="border-t border-purple-300 pt-4">
                            <p class="text-sm font-semibold text-purple-800 mb-3">Participants :</p>
                            <div class="space-y-2">
                                @foreach($participants as $participant)
                                <label class="flex items-center cursor-pointer hover:bg-white p-2 rounded transition">
                                    <input type="checkbox" 
                                           name="activites[{{ $participant->numparticipant }}][]" 
                                           value="{{ $activite->numactivite }}"
                                           class="w-5 h-5 text-purple-600 border-purple-300 rounded focus:ring-purple-500"
                                           {{ isset($activitesSelectionnees[$participant->numparticipant]) && in_array($activite->numactivite, $activitesSelectionnees[$participant->numparticipant]) ? 'checked' : '' }}>
                                    <div class="ml-3 flex items-center">
                                        @if(str_contains($participant->nomparticipant, 'Adulte'))
                                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                        <span class="font-medium text-gray-700">{{ $participant->nomparticipant }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(count($activites) == 0)
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-lg">Aucune activité disponible pour ce resort</p>
                </div>
                @endif
            </div>

            <!-- Récapitulatif des sélections -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800">Récapitulatif</h3>
                <div id="recap" class="space-y-2 text-gray-600">
                    <p>Sélectionnez des activités pour voir le récapitulatif</p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between items-center">
                <a href="{{ route('panier.show', $reservation->numreservation) }}" 
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition">
                    Annuler
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold shadow-lg transition">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('activitesForm');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    const recap = document.getElementById('recap');
    
    function updateRecap() {
        const selections = {};
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const activiteName = checkbox.closest('.border-2').querySelector('h3').textContent.trim();
                const participantName = checkbox.closest('label').querySelector('span').textContent.trim();
                const prix = parseFloat(checkbox.closest('.border-2').querySelector('.text-xl').textContent.replace(/[^0-9]/g, ''));
                
                if (!selections[activiteName]) {
                    selections[activiteName] = {
                        participants: [],
                        prix: prix
                    };
                }
                selections[activiteName].participants.push(participantName);
            }
        });
        
        if (Object.keys(selections).length === 0) {
            recap.innerHTML = '<p class="text-gray-600">Sélectionnez des activités pour voir le récapitulatif</p>';
            return;
        }
        
        let html = '';
        let total = 0;
        
        for (const [activite, data] of Object.entries(selections)) {
            const sousTotal = data.prix * data.participants.length;
            total += sousTotal;
            
            html += `
                <div class="border-b pb-2 mb-2">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-purple-900">${activite}</p>
                            <p class="text-sm text-gray-600">${data.participants.join(', ')}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold">${sousTotal.toLocaleString('fr-FR')} €</p>
                            <p class="text-xs text-gray-500">${data.participants.length} × ${data.prix.toLocaleString('fr-FR')} €</p>
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += `
            <div class="pt-3 border-t-2 border-purple-300">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-purple-900">Total activités</span>
                    <span class="text-2xl font-bold text-purple-700">${total.toLocaleString('fr-FR')} €</span>
                </div>
            </div>
        `;
        
        recap.innerHTML = html;
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateRecap);
    });
    
    // Initial update
    updateRecap();
    
    // Prevent double submit
    form.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="inline-flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Enregistrement...</span>';
    });
});
</script>
@endsection
