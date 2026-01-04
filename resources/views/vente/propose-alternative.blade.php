@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-4xl mx-auto">
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('vente.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
                ← Retour au tableau de bord
            </a>
            <h1 class="text-3xl font-bold mb-2">Proposer un Resort Alternatif</h1>
            <p class="text-gray-600">Le resort initial a refusé. Sélectionnez un resort alternatif à proposer au client.</p>
        </div>

        {{-- Messages de session --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>✗ Erreur!</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Informations de la réservation --}}
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">Réservation #{{ $reservation->numreservation }}</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Client</p>
                        <p class="font-semibold">{{ $reservation->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Dates du séjour</p>
                        <p class="font-semibold">{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nombre de personnes</p>
                        <p class="font-semibold">{{ $reservation->nbpersonnes }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Prix total</p>
                        <p class="font-semibold text-blue-600">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                    </div>
                </div>

                {{-- Resort refusé --}}
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">❌</span>
                        <div>
                            <p class="font-bold text-red-800">Resort ayant refusé</p>
                            <p class="text-lg">{{ $originalResort->nomresort }}</p>
                            @if($originalResort->pays)
                                <p class="text-sm text-gray-600">{{ $originalResort->pays->nompays }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaire de sélection --}}
        <form action="{{ route('vente.propose-alternative', $reservation->numreservation) }}" method="POST">
            @csrf

            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Sélectionner un Resort Alternatif</h2>
                </div>
                <div class="p-6">
                    {{-- Recherche et filtres --}}
                    <div class="mb-6">
                        <input 
                            type="text" 
                            id="search-resort" 
                            placeholder="Rechercher un resort..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        >
                    </div>

                    {{-- Liste des resorts --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto" id="resorts-list">
                        @foreach($alternativeResorts as $resort)
                            <label class="resort-item cursor-pointer block border rounded-lg p-4 hover:bg-blue-50 transition {{ $resort->codepays === $originalResort->codepays ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}" data-name="{{ strtolower($resort->nomresort) }}" data-country="{{ strtolower($resort->pays->nompays ?? '') }}">
                                <div class="flex items-start gap-3">
                                    <input 
                                        type="radio" 
                                        name="alternative_resort_id" 
                                        value="{{ $resort->numresort }}" 
                                        class="mt-1"
                                        required
                                    >
                                    <div class="flex-1">
                                        <p class="font-bold">{{ $resort->nomresort }}</p>
                                        @if($resort->pays)
                                            <p class="text-sm text-gray-600">{{ $resort->pays->nompays }}</p>
                                        @endif
                                        @if($resort->nbtridents)
                                            <p class="text-sm text-yellow-600">
                                                @for($i = 0; $i < $resort->nbtridents; $i++)
                                                    🔱
                                                @endfor
                                            </p>
                                        @endif
                                        @if($resort->codepays === $originalResort->codepays)
                                            <span class="inline-block mt-1 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                                Même pays
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('alternative_resort_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Message personnalisé --}}
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Message au client</h2>
                    <p class="text-sm text-gray-600 mt-1">Expliquez la situation et présentez l'alternative (optionnel)</p>
                </div>
                <div class="p-6">
                    <textarea 
                        name="message" 
                        rows="5" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                        placeholder="Bonjour,

Suite à l'indisponibilité du resort initialement choisi, nous avons le plaisir de vous proposer une alternative de qualité équivalente..."
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Boutons d'action --}}
            <div class="flex gap-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition"
                >
                    📧 Envoyer la proposition au client
                </button>
                <a 
                    href="{{ route('vente.dashboard') }}" 
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition text-center"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('search-resort').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        document.querySelectorAll('.resort-item').forEach(function(item) {
            const name = item.getAttribute('data-name');
            const country = item.getAttribute('data-country');
            if (name.includes(search) || country.includes(search)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
