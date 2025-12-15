@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('cart.index') }}" class="text-blue-600 hover:underline mb-4 inline-block">← Retour au panier</a>
    
    <h1 class="text-2xl font-bold mb-6">Détail de la réservation #{{ $reservation->numreservation }}</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Infos Resort -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">{{ $reservation->nomresort }}</h2>
                <p class="text-gray-500">{{ $reservation->nompays ?? '' }}</p>
                
                @php
                    $imageName = strtolower(str_replace(' ', '', $reservation->nomresort)) . '.webp';
                    $imagePath = 'img/ressort/' . $imageName;
                    $fullPath = public_path($imagePath);
                @endphp
                
                @if(file_exists($fullPath))
                    <img src="{{ asset($imagePath) }}" 
                         alt="{{ $reservation->nomresort }}" class="w-full h-48 object-cover rounded mt-4">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 rounded mt-4">
                        <span>Aucune image disponible</span>
                    </div>
                @endif
            </div>
            
            <!-- Détails séjour -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Détails du séjour</h3>
                    <a href="{{ route('reservation.edit.step1', $reservation->numreservation) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Dates</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($reservation->datedebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->datefin)->format('d/m/Y') }}</p>
                        <p class="text-blue-600">{{ $nbNuits }} nuit(s)</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Voyageurs</p>
                        <p class="font-medium">{{ $nbAdultes }} adulte(s)</p>
                        @if($nbEnfants > 0)
                            <p class="font-medium">{{ $nbEnfants }} enfant(s)</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Chambres sélectionnées -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hébergement
                    </h3>
                    <a href="{{ route('reservation.edit.step1', $reservation->numreservation) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($chambres as $chambre)
                        <div class="border-2 border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $chambre->nomtype }}</p>
                                    <p class="text-sm text-gray-500">{{ $chambre->surface }} m² • Max {{ $chambre->capacitemax }} pers.</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600">× {{ $chambre->quantite }}</p>
                                    <p class="text-xs text-gray-500">chambre(s)</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Transport par participant -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Transport des participants
                    </h3>
                    <a href="{{ route('reservation.edit.step2', $reservation->numreservation) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
                <div class="space-y-2">
                    @foreach($participants as $participant)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <div class="flex items-center">
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
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">{{ $participant->nomtransport ?? 'Sans transport' }}</p>
                                @if($participant->prixtransport)
                                    <p class="text-xs text-gray-500">{{ number_format($participant->prixtransport, 0, ',', ' ') }} €</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between pt-3 border-t-2 border-gray-300 font-semibold">
                        <span>Total transport</span>
                        <span class="text-lg">{{ number_format($prixTransportTotal, 0, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>
            
            <!-- Activités par participant -->
            @if(!empty($activites) && count($activites) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activités sélectionnées
                    </h3>
                    <a href="{{ route('reservation.edit.step3', $reservation->numreservation) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                </div>
                <div class="space-y-4">
                    @foreach($activites as $activite)
                        <div class="border-2 border-purple-200 bg-purple-50 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <p class="font-semibold text-purple-900">{{ $activite['nomactivite'] }}</p>
                                    <p class="text-xs text-purple-700 italic">{{ $activite['descriptionactivite'] }}</p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-sm font-bold text-purple-800">{{ number_format($activite['prixmin'], 0, ',', ' ') }} €<span class="text-xs">/pers</span></p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-purple-300">
                                <p class="text-xs font-semibold text-purple-800 mb-2">Participants :</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($activite['participants'] as $participantNom)
                                        <span class="inline-flex items-center px-2 py-1 bg-white border border-purple-300 rounded-full text-xs font-medium text-purple-700">
                                            @if(str_contains($participantNom, 'Adulte'))
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                            {{ $participantNom }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="mt-3 flex justify-between text-sm font-semibold text-purple-900">
                                    <span>Sous-total ({{ $activite['nbParticipants'] }} participant(s))</span>
                                    <span>{{ number_format($activite['prixmin'] * $activite['nbParticipants'], 0, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between pt-3 border-t-2 border-purple-300 font-semibold text-purple-900">
                        <span>Total activités</span>
                        <span class="text-lg">{{ number_format($prixActivites, 0, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Décomposition détaillée des prix -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Récapitulatif des prix</h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Hébergement ({{ $nbNuits }} nuits)</span>
                        <span class="font-medium">{{ number_format($prixChambre, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transport ({{ $nbAdultes + $nbEnfants }} pers.)</span>
                        <span class="font-medium">{{ number_format($prixTransportTotal, 2, ',', ' ') }} €</span>
                    </div>
                    @if(!empty($activites) && count($activites) > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Activités ({{ count($activites) }} activité(s))</span>
                        <span class="font-medium">{{ number_format($prixActivites, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t pt-3">
                        <span class="text-gray-600">Sous-total HT</span>
                        <span>{{ number_format($sousTotal, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">TVA (20%)</span>
                        <span>{{ number_format($tva, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t-2 pt-3 mt-2">
                        <span>Total TTC</span>
                        <span class="text-blue-600">{{ number_format($total, 2, ',', ' ') }} €</span>
                    </div>
                </div>
                
                <!-- Prix par personne -->
                <div class="mt-6 bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-2">Prix par voyageur</p>
                    @if(($nbAdultes + $nbEnfants) > 0)
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($total / ($nbAdultes + $nbEnfants), 2, ',', ' ') }} €</p>
                    @else
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($total / max(1, $reservation->nbpersonnes), 2, ',', ' ') }} €</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                <div class="text-center mb-4">
                    <p class="text-gray-500">Prix total TTC</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($total, 2, ',', ' ') }} €</p>
                    <p class="text-sm text-gray-500 mt-1">pour {{ $nbPersonnes }} personne(s)</p>
                    <p class="text-sm text-gray-400">({{ $nbAdultes }} adulte(s){{ $nbEnfants > 0 ? ', ' . $nbEnfants . ' enfant(s)' : '' }})</p>
                </div>
                
                @if(in_array($reservation->statut, ['Validée','Confirmée','confirmee']))
                    <div class="mb-4 p-3 bg-green-100 rounded text-center">
                        <span class="text-green-700 font-medium">✓ Réservation validée</span>
                    </div>
                @elseif($reservation->statut === 'En attente')
                    <div class="mb-4 p-3 bg-yellow-100 rounded text-center">
                        <span class="text-yellow-700 font-medium">En attente de paiement</span>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-gray-100 rounded text-center">
                        <span class="text-gray-700 font-medium">{{ $reservation->statut }}</span>
                    </div>
                @endif

                <div class="text-sm text-gray-600 mb-4 space-y-1">
                    <div class="flex justify-between">
                        <span>Hébergement</span>
                        <span>{{ number_format($prixChambre, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Transport</span>
                        <span>{{ number_format($prixTransportTotal, 2, ',', ' ') }} €</span>
                    </div>
                    @if(!empty($activites) && count($activites) > 0)
                    <div class="flex justify-between text-purple-700">
                        <span>Activités ({{ count($activites) }})</span>
                        <span>{{ number_format($prixActivites, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t pt-1 mt-1">
                        <span>TVA (20%)</span>
                        <span>{{ number_format($tva, 2, ',', ' ') }} €</span>
                    </div>
                </div>
                
                @if(in_array($reservation->statut, ['Validée','Confirmée','confirmee','Terminée']))
                    <a href="{{ route('reservations.index') }}" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 font-semibold mb-3 block text-center">
                        Voir mes réservations
                    </a>
                @elseif($reservation->statut === 'En attente')
                    <a href="{{ route('payment.page', $reservation->numreservation) }}" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-semibold mb-3 block text-center">
                        Payer maintenant
                    </a>
                @else
                    <a href="{{ route('reservations.index') }}" class="w-full bg-gray-600 text-white py-3 rounded hover:bg-gray-700 font-semibold mb-3 block text-center">
                        Voir mes réservations
                    </a>
                    
                    <form action="{{ route('panier.remove', $reservation->numreservation) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full border border-red-600 text-red-600 py-2 rounded hover:bg-red-50"
                                onclick="return confirm('Supprimer cette réservation ?')">
                            Supprimer
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
