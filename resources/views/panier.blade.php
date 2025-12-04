@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Mon Panier</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if($reservations->count() > 0)
        <div class="space-y-4">
            @foreach($reservations as $reservation)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-semibold">{{ $reservation->nomresort }}</h3>
                            <p class="text-gray-500">{{ $reservation->nompays ?? '' }}</p>
                            
                            <div class="mt-3 text-sm text-gray-600">
                                <p><strong>Dates:</strong> {{ \Carbon\Carbon::parse($reservation->datedebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($reservation->datefin)->format('d/m/Y') }}</p>
                                <p><strong>Voyageurs:</strong> {{ $reservation->nbpersonnes }} personne(s)</p>
                                <p><strong>Chambre:</strong> {{ $reservation->nomtype }}</p>
                                @if($reservation->nomtransport)
                                    <p><strong>Transport:</strong> {{ $reservation->nomtransport }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                            <p class="text-sm text-gray-500">TTC</p>
                            
                            <div class="mt-4 space-x-2">
                                <a href="{{ route('panier.show', $reservation->numreservation) }}" 
                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                    Voir détail
                                </a>
                                <form action="{{ route('panier.remove', $reservation->numreservation) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm"
                                            onclick="return confirm('Supprimer cette réservation ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <span class="text-xl font-bold">Total</span>
                <span class="text-2xl font-bold text-blue-600">{{ number_format($reservations->sum('prixtotal'), 2, ',', ' ') }} €</span>
            </div>
            <button class="mt-4 w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-semibold">
                Procéder au paiement
            </button>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500 text-lg">Votre panier est vide</p>
            <a href="{{ route('resorts.index') }}" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Explorer nos resorts
            </a>
        </div>
    @endif
</div>
@endsection


