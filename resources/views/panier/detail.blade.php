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
                <h3 class="text-lg font-semibold mb-4">Détails du séjour</h3>
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
                    <div>
                        <p class="text-gray-500">Hébergement</p>
                        <p class="font-medium">{{ $typeChambre->nomtype ?? 'N/A' }}</p>
                        @if($typeChambre && $typeChambre->surface)
                            <p class="text-gray-500">{{ $typeChambre->surface }} m²</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-500">Transport</p>
                        <p class="font-medium">{{ $transport->nomtransport ?? 'Sans transport' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Décomposition détaillée des prix -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Décomposition détaillée des prix</h3>
                
                <!-- Section Hébergement -->
                <div class="mb-6">
                    <h4 class="font-medium text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Hébergement - {{ $typeChambre->nomtype ?? 'Chambre' }}
                    </h4>
                    <div class="bg-gray-50 rounded p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Prix par nuit</span>
                            <span>{{ number_format($prixParNuit, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nombre de nuits</span>
                            <span>× {{ $nbNuits }}</span>
                        </div>
                        <div class="flex justify-between font-medium border-t pt-2">
                            <span>Sous-total hébergement</span>
                            <span>{{ number_format($prixChambre, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
                
                <!-- Section Transport -->
                @if($transport)
                <div class="mb-6">
                    <h4 class="font-medium text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Transport - {{ $transport->nomtransport }}
                    </h4>
                    <div class="bg-gray-50 rounded p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Prix par personne</span>
                            <span>{{ number_format($prixTransportParPersonne, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Adultes ({{ $nbAdultes }} × {{ number_format($prixTransportParPersonne, 2, ',', ' ') }} €)</span>
                            <span>{{ number_format($prixTransportAdultes, 2, ',', ' ') }} €</span>
                        </div>
                        @if($nbEnfants > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Enfants ({{ $nbEnfants }} × {{ number_format($prixTransportParPersonne, 2, ',', ' ') }} €)</span>
                            <span>{{ number_format($prixTransportEnfants, 2, ',', ' ') }} €</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-medium border-t pt-2">
                            <span>Sous-total transport</span>
                            <span>{{ number_format($prixTransportTotal, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Récapitulatif Final -->
                <div class="border-t-2 border-gray-200 pt-4 mt-4">
                    <h4 class="font-medium text-gray-800 mb-3">Récapitulatif</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Hébergement</span>
                            <span>{{ number_format($prixChambre, 2, ',', ' ') }} €</span>
                        </div>
                        @if($transport)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transport</span>
                            <span>{{ number_format($prixTransportTotal, 2, ',', ' ') }} €</span>
                        </div>
                        @endif
                        <div class="flex justify-between border-t pt-2">
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
                </div>
                
                <!-- Prix par personne -->
                <div class="mt-6 bg-blue-50 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-3">Prix par voyageur</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500">Par adulte</p>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($total / $nbPersonnes, 2, ',', ' ') }} €</p>
                        </div>
                        @if($nbEnfants > 0)
                        <div class="bg-white rounded p-3">
                            <p class="text-gray-500">Par enfant</p>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($total / $nbPersonnes, 2, ',', ' ') }} €</p>
                        </div>
                        @endif
                    </div>
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
                
                <div class="mb-4 p-3 bg-yellow-100 rounded text-center">
                    <span class="text-yellow-700 font-medium">En attente de paiement</span>
                </div>

                <div class="text-sm text-gray-600 mb-4 space-y-1">
                    <div class="flex justify-between">
                        <span>Hébergement</span>
                        <span>{{ number_format($prixChambre, 2, ',', ' ') }} €</span>
                    </div>
                    @if($transport)
                    <div class="flex justify-between">
                        <span>Transport</span>
                        <span>{{ number_format($prixTransportTotal, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span>TVA</span>
                        <span>{{ number_format($tva, 2, ',', ' ') }} €</span>
                    </div>
                </div>
                
                <button class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-semibold mb-3">
                    Payer maintenant
                </button>
                
                <form action="{{ route('panier.remove', $reservation->numreservation) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full border border-red-600 text-red-600 py-2 rounded hover:bg-red-50"
                            onclick="return confirm('Supprimer cette réservation ?')">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
