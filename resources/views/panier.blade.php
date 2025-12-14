@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    
    {{-- En-tête --}}
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-serif font-bold text-[#113559]">Mon Panier</h1>
        <p class="text-gray-600">Validez vos séjours avant qu'ils ne soient plus disponibles.</p>
    </div>

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(isset($reservations) && $reservations->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLONNE GAUCHE : Liste des séjours --}}
            <div class="lg:col-span-2 space-y-6">
                @foreach($reservations as $reservation)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
                        {{-- Bandeau supérieur de la carte --}}
                        <div class="bg-gray-50 px-6 py-3 border-b border-gray-100 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-[#113559] flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $reservation->nomresort }}
                            </h2>
                            <span class="text-sm text-gray-500">{{ $reservation->nompays ?? '' }}</span>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                {{-- Dates --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Dates</p>
                                    <p class="text-base font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($reservation->datedebut)->format('d/m/Y') }} 
                                        <span class="text-gray-400 mx-1">➔</span> 
                                        {{ \Carbon\Carbon::parse($reservation->datefin)->format('d/m/Y') }}
                                    </p>
                                </div>
                                {{-- Voyageurs --}}
                                <div>
                                    <p class="text-xs text-gray-500 uppercase font-bold">Voyageurs</p>
                                    <p class="text-base font-semibold text-gray-800">{{ $reservation->nbpersonnes }} personne(s)</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 border-t border-gray-100 pt-3">
                                <p class="flex items-start">
                                    <svg class="w-4 h-4 mr-2 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    <span>
                                        Chambre(s) : 
                                        @foreach($reservation->chambres as $index => $chambre)
                                            <span class="font-medium">{{ $chambre->nomtype }} (×{{ $chambre->quantite }})</span>@if(!$loop->last), @endif
                                        @endforeach
                                    </span>
                                </p>
                            </div>

                            <div class="flex justify-between items-end mt-6 pt-4 border-t border-dashed border-gray-200">
                                <div class="flex gap-2">
                                    {{-- Bouton Voir Détail --}}
                                    <a href="{{ route('panier.show', $reservation->numreservation) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Détails
                                    </a>
                                    
                                    <span class="text-gray-300">|</span>

                                    {{-- Bouton Supprimer --}}
                                    <form action="{{ route('panier.remove', $reservation->numreservation) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold flex items-center transition-colors" onclick="return confirm('Voulez-vous vraiment retirer ce séjour du panier ?')">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Retirer
                                        </button>
                                    </form>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl font-bold text-[#113559]">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                                    <p class="text-xs text-gray-500">TTC</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- COLONNE DROITE : Résumé Total --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden sticky top-6">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-800">Récapitulatif de la commande</h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                            <span>Nombre de séjours</span>
                            <span class="font-medium">{{ $reservations->count() }}</span>
                        </div>
                        
                        <div class="border-t border-dashed border-gray-300 pt-4 mb-6">
                            <div class="flex justify-between items-end">
                                <span class="font-bold text-gray-800 text-lg">Total à régler</span>
                                <span class="font-bold text-blue-600 text-3xl">{{ number_format($reservations->sum('prixtotal'), 2, ',', ' ') }} €</span>
                            </div>
                            <p class="text-xs text-right text-gray-400 mt-1">TVA incluse</p>
                        </div>

                        @if($reservations->count() === 1)
                            {{-- Une seule réservation : aller directement au paiement --}}
                            <a href="{{ route('payment.page', $reservations->first()->numreservation) }}" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-700 transition-all shadow-md flex justify-center items-center">
                                Procéder au paiement
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        @else
                            {{-- Plusieurs réservations : payer une par une --}}
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600 text-center mb-3">Vous devez payer chaque séjour individuellement</p>
                                @foreach($reservations as $reservation)
                                    <a href="{{ route('payment.page', $reservation->numreservation) }}" 
                                       class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold text-sm hover:bg-green-700 transition-all flex justify-between items-center">
                                        <span>{{ $reservation->nomresort }}</span>
                                        <span>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-400 flex items-center justify-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Paiement 100% sécurisé
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Bouton continuer mes achats --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('resorts.index') }}" class="text-gray-500 hover:text-[#113559] text-sm font-semibold underline">
                        Continuer mes achats (ajouter un autre séjour)
                    </a>
                </div>
            </div>

        </div>

    @else
        {{-- PANIER VIDE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center max-w-2xl mx-auto">
            <div class="mb-6 bg-blue-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto text-blue-200">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>d
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Votre panier est vide</h2>
            <p class="text-gray-500 mb-8">Il semble que vous n'ayez pas encore sélectionné de voyage de rêve.</p>
            <a href="{{ route('resorts.index') }}" class="inline-block bg-[#113559] text-white px-8 py-3 rounded-full font-bold hover:bg-[#0e2a47] transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Explorer nos destinations
            </a>
        </div>
    @endif
</div>
@endsection