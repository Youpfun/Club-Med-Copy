<div class="bg-gray-800 shadow-lg rounded-lg p-8 border border-gray-700 flex flex-col h-full w-full">
    
    {{-- Titre : Ping vers IP --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Ping vers {{ $host }}</h2>
        {{-- Vous pouvez décommenter la ligne suivante pour afficher la dernière mise à jour --}}
        {{-- <span class="text-lg text-white">Last updated: {{ now()->diffForHumans() }}</span> --}}
    </div>

    {{-- Sous-titre : Ping Time --}}
    <div class="text-2xl font-semibold text-center text-white mb-6">
        <span>Ping Time</span>
    </div>

    {{-- La valeur du Ping (Gros et Vert) --}}
    <div class="text-4xl text-center font-mono text-white mb-4">
        @if($pingTime == 'N/A')
            <span class="text-red-500">Unable to fetch ping. Please check the server.</span>
        @else
            {{-- Affiche la valeur en vert comme sur la photo --}}
            <span class="text-green-500">{{ $pingTime }}</span>
        @endif
    </div>

    {{-- Indicateur de chargement (invisible sauf quand ça charge) --}}
    <div wire:loading class="mt-4 text-center text-lg text-white">
        <span>Fetching ping...</span>
    </div>

</div>