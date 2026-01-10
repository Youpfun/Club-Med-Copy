<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer Séjour - Étape 4/4</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-full mx-auto">
            <div class="mb-8 max-w-6xl mx-auto">
                <div class="flex justify-between text-sm text-gray-500 font-bold">
                    <span class="text-green-600">1. Structure</span>
                    <span class="text-green-600">2. Hébergement</span>
                    <span class="text-green-600">3. Activités</span>
                    <span class="text-blue-600">4. Tarifs Saisonniers</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-serif font-bold text-gray-900">Grille Tarifaire : {{ $resort->nomresort }}</h1>
                <p class="text-gray-600">Renseignez le prix par nuit pour chaque type de chambre et chaque période.</p>
            </div>

            <form action="{{ route('resort.storeStep4', $resort->numresort) }}" method="POST">
                @csrf
                
                <div class="bg-white rounded-xl shadow overflow-x-auto border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase sticky left-0 bg-gray-50 z-10">Type Chambre</th>
                                @foreach($periodes as $p)
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase border-l">
                                        {{ $p->nomperiode }}<br>
                                        <span class="text-[10px] font-normal text-gray-400">
                                            {{ \Carbon\Carbon::parse($p->datedebutperiode)->format('d/m') }} - {{ \Carbon\Carbon::parse($p->datefinperiode)->format('d/m') }}
                                        </span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($activeRooms as $tc)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 sticky left-0 bg-white z-10 border-r">
                                        {{ $tc->nomtype }}
                                    </td>
                                    @foreach($periodes as $p)
                                        @php 
                                            $val = $existingPrices[$tc->numtype][$p->numperiode] ?? ''; 
                                        @endphp
                                        <td class="px-2 py-2 border-l text-center">
                                            <div class="relative">
                                                <input type="number" name="prix[{{ $tc->numtype }}][{{ $p->numperiode }}]" 
                                                       value="{{ $val }}" 
                                                       class="w-24 text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-right pr-6" 
                                                       placeholder="-">
                                                <span class="absolute right-2 top-2 text-gray-400 text-xs">€</span>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-8 mt-4 flex justify-between items-center border-t">
                    <button type="submit" name="action" value="save_exit" class="text-gray-500 hover:text-gray-800 font-bold underline transition">
                        Sauvegarder et quitter
                    </button>
                    <button type="submit" name="action" value="finish" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow-lg transform transition hover:scale-105 flex items-center gap-2">
                        <span>💾 Enregistrer la Grille et Terminer</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>