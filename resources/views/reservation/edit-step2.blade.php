@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('panier.show', $reservation->numreservation) }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour au détail</a>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                <h1 class="text-3xl font-bold">Modifier le transport</h1>
                <p class="mt-2 text-blue-100">Réservation #{{ $reservation->numreservation }}</p>
            </div>
        </div>

        <form action="{{ route('reservation.update.step2', $reservation->numreservation) }}" method="POST">
            @csrf

            <!-- Liste des participants avec leur transport -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800">Sélectionnez le transport pour chaque voyageur</h2>

                @foreach($participants as $index => $participant)
                <div class="mb-6 pb-6 {{ $loop->last ? '' : 'border-b' }}">
                    <div class="flex items-center mb-4">
                        @if(str_contains($participant->nomparticipant, 'Adulte'))
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        @endif
                        <h3 class="text-lg font-semibold text-gray-800">{{ $participant->nomparticipant }}</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($transports as $transport)
                        <label class="transport-option cursor-pointer">
                            <input type="radio" 
                                   name="transport[{{ $participant->numparticipant }}]" 
                                   value="{{ $transport->numtransport }}"
                                   class="transport-radio hidden"
                                   data-participant="{{ $participant->numparticipant }}"
                                   {{ $participant->numtransport == $transport->numtransport ? 'checked' : '' }}
                                   onchange="updateTransportUI({{ $participant->numparticipant }})">
                            <div class="transport-card border-2 rounded-lg p-4 transition-all {{ $participant->numtransport == $transport->numtransport ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-blue-300' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @if(str_contains(strtolower($transport->nomtransport), 'avion'))
                                            <svg class="transport-icon w-8 h-8 mr-3 {{ $participant->numtransport == $transport->numtransport ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        @elseif(str_contains(strtolower($transport->nomtransport), 'train'))
                                            <svg class="transport-icon w-8 h-8 mr-3 {{ $participant->numtransport == $transport->numtransport ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        @elseif(str_contains(strtolower($transport->nomtransport), 'bus'))
                                            <svg class="transport-icon w-8 h-8 mr-3 {{ $participant->numtransport == $transport->numtransport ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                            </svg>
                                        @else
                                            <svg class="transport-icon w-8 h-8 mr-3 {{ $participant->numtransport == $transport->numtransport ? 'text-blue-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                            </svg>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $transport->nomtransport }}</p>
                                            <p class="text-sm text-gray-500">{{ $transport->descriptiontransport ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-800">{{ number_format($transport->prixtransport, 0, ',', ' ') }} €</p>
                                        <svg class="check-icon w-5 h-5 text-blue-500 ml-auto {{ $participant->numtransport == $transport->numtransport ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-between items-center">
                <a href="{{ route('panier.show', $reservation->numreservation) }}" 
                   class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 font-semibold shadow-lg transition">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateTransportUI(participantId) {
    const radios = document.querySelectorAll(`input[name="transport[${participantId}]"]`);
    
    radios.forEach(radio => {
        const card = radio.closest('.transport-option').querySelector('.transport-card');
        const icon = radio.closest('.transport-option').querySelector('.transport-icon');
        const checkIcon = radio.closest('.transport-option').querySelector('.check-icon');
        
        if (radio.checked) {
            card.classList.remove('border-gray-300', 'hover:border-blue-300');
            card.classList.add('border-blue-500', 'bg-blue-50');
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-blue-500');
            checkIcon.classList.remove('hidden');
        } else {
            card.classList.remove('border-blue-500', 'bg-blue-50');
            card.classList.add('border-gray-300', 'hover:border-blue-300');
            icon.classList.remove('text-blue-500');
            icon.classList.add('text-gray-400');
            checkIcon.classList.add('hidden');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($participants as $participant)
        updateTransportUI({{ $participant->numparticipant }});
    @endforeach
});
</script>
@endsection
