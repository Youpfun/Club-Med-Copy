@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-clubmed-beige">
    <!-- Header -->
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
                    <div class="w-8 h-8 rounded-full bg-clubmed-gold text-white flex items-center justify-center font-bold text-sm">2</div>
                    <span class="ml-2 font-semibold text-clubmed-gold hidden sm:inline">Transport</span>
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
            @foreach($chambres as $numtype => $qty)
                @if($qty > 0)
                    <input type="hidden" name="chambres[{{ $numtype }}]" value="{{ $qty }}">
                @endif
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
                    
                    <!-- Section choix transport par participant -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-clubmed-blue px-6 py-4">
                            <h2 class="text-xl font-bold font-serif text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Choisissez le transport pour chaque voyageur
                            </h2>
                            <p class="text-white/80 text-sm mt-1">Sélectionnez le mode de transport adapté pour chaque participant</p>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            @for($i = 1; $i <= $nbAdultes; $i++)
                                @php
                                    $participantKey = 'adulte_' . $i;
                                    $participantInfo = $participants[$participantKey] ?? [];
                                    $nom = $participantInfo['nom'] ?? '';
                                    $prenom = $participantInfo['prenom'] ?? '';
                                    $displayName = trim($prenom . ' ' . $nom) ?: "Adulte $i";
                                @endphp
                                <div class="border-2 border-gray-200 rounded-xl p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-lg text-gray-800">{{ $displayName }}</h3>
                                                <p class="text-sm text-gray-500">Adulte - 15 ans et plus</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($transports as $transport)
                                            <label class="cursor-pointer transport-option">
                                                <input type="radio" name="transport_adulte_{{ $i }}" value="{{ $transport->numtransport }}" 
                                                       class="hidden transport-radio" {{ $loop->first ? 'checked' : '' }}
                                                       data-prix="{{ $transport->prixtransport }}" 
                                                       data-participant="adulte_{{ $i }}">
                                                <div class="border-2 border-gray-200 rounded-lg p-3 transition-all hover:border-gray-300 transport-card">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            @if(str_contains(strtolower($transport->nomtransport), 'avion'))
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                                </svg>
                                                            @elseif(str_contains(strtolower($transport->nomtransport), 'train'))
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                            @endif
                                                            <span class="font-medium text-gray-800 text-sm">{{ $transport->nomtransport }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="font-bold text-purple-600 text-sm">{{ number_format($transport->prixtransport, 0, ',', ' ') }} €</span>
                                                            <svg class="w-5 h-5 text-purple-500 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endfor
                            
                            @for($i = 1; $i <= $nbEnfants; $i++)
                                @php
                                    $participantKey = 'enfant_' . $i;
                                    $participantInfo = $participants[$participantKey] ?? [];
                                    $nom = $participantInfo['nom'] ?? '';
                                    $prenom = $participantInfo['prenom'] ?? '';
                                    $displayName = trim($prenom . ' ' . $nom) ?: "Enfant $i";
                                @endphp
                                <div class="border-2 border-gray-200 rounded-xl p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="font-bold text-lg text-gray-800">{{ $displayName }}</h3>
                                                <p class="text-sm text-gray-500">Enfant - Moins de 15 ans</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($transports as $transport)
                                            <label class="cursor-pointer transport-option">
                                                <input type="radio" name="transport_enfant_{{ $i }}" value="{{ $transport->numtransport }}" 
                                                       class="hidden transport-radio" {{ $loop->first ? 'checked' : '' }}
                                                       data-prix="{{ $transport->prixtransport }}" 
                                                       data-participant="enfant_{{ $i }}">
                                                <div class="border-2 border-gray-200 rounded-lg p-3 transition-all hover:border-gray-300 transport-card">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-2">
                                                            @if(str_contains(strtolower($transport->nomtransport), 'avion'))
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                                </svg>
                                                            @elseif(str_contains(strtolower($transport->nomtransport), 'train'))
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-gray-400 transport-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                            @endif
                                                            <span class="font-medium text-gray-800 text-sm">{{ $transport->nomtransport }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="font-bold text-purple-600 text-sm">{{ number_format($transport->prixtransport, 0, ',', ' ') }} €</span>
                                                            <svg class="w-5 h-5 text-purple-500 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endfor
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
                                <div class="w-10 h-10 bg-clubmed-beige rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-clubmed-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm text-gray-500 mb-1">Dates du séjour</p>
                                    <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
                                    @php
                                        $nuits = \Carbon\Carbon::parse($dateDebut)->diffInDays(\Carbon\Carbon::parse($dateFin));
                                    @endphp
                                    <p class="text-sm text-clubmed-gold font-medium">{{ $nuits }} nuit(s)</p>
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

                            <!-- Transport Total -->
                            <div class="border-t pt-5 mt-5">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 flex items-center">
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Prix transport total
                                    </span>
                                    <span class="font-bold text-gray-800" id="recap-prix-transport">0 €</span>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="p-6 bg-gray-50 border-t space-y-3">
                            <button type="submit" class="w-full bg-clubmed-blue text-white py-4 px-6 rounded-xl font-bold text-lg hover:bg-blue-900 transition-all shadow-lg hover:shadow-xl flex items-center justify-center">
                                Continuer
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                            <a href="{{ route('reservation.step1', ['numresort' => $resort->numresort]) }}" 
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
    const nbAdultes = {{ $nbAdultes }};
    const nbEnfants = {{ $nbEnfants }};
    
    function updateTransportUI() {
        // Mettre à jour l'apparence de tous les transports
        document.querySelectorAll('.transport-option').forEach(option => {
            const radio = option.querySelector('.transport-radio');
            const card = option.querySelector('.transport-card');
            const icon = option.querySelector('.transport-icon');
            const checkIcon = option.querySelector('.check-icon');
            
            if (radio.checked) {
                card.classList.add('border-purple-500', 'bg-purple-50');
                card.classList.remove('border-gray-200');
                if (icon) {
                    icon.classList.remove('text-gray-400');
                    icon.classList.add('text-purple-500');
                }
                if (checkIcon) {
                    checkIcon.classList.remove('hidden');
                }
            } else {
                card.classList.remove('border-purple-500', 'bg-purple-50');
                card.classList.add('border-gray-200');
                if (icon) {
                    icon.classList.add('text-gray-400');
                    icon.classList.remove('text-purple-500');
                }
                if (checkIcon) {
                    checkIcon.classList.add('hidden');
                }
            }
        });
    }
    
    function updateRecap() {
        let totalTransport = 0;
        
        // Calculer le total pour les adultes
        for (let i = 1; i <= nbAdultes; i++) {
            const selected = document.querySelector('input[name="transport_adulte_' + i + '"]:checked');
            if (selected) {
                const prix = parseFloat(selected.dataset.prix) || 0;
                totalTransport += prix;
            }
        }
        
        // Calculer le total pour les enfants
        for (let i = 1; i <= nbEnfants; i++) {
            const selected = document.querySelector('input[name="transport_enfant_' + i + '"]:checked');
            if (selected) {
                const prix = parseFloat(selected.dataset.prix) || 0;
                totalTransport += prix;
            }
        }
        
        // Mettre à jour l'affichage
        const recapPrixTransport = document.getElementById('recap-prix-transport');
        if (recapPrixTransport) {
            recapPrixTransport.textContent = new Intl.NumberFormat('fr-FR').format(totalTransport) + ' €';
        }
        
        updateTransportUI();
    }
    
    // Ajouter des écouteurs sur tous les radios
    document.addEventListener('DOMContentLoaded', function() {
        const allRadios = document.querySelectorAll('input[type="radio"][name^="transport_"]');
        allRadios.forEach(radio => {
            radio.addEventListener('change', updateRecap);
        });
        
        updateRecap(); // Calcul initial et UI
    });
</script>
@endsection
