<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chambres Indisponibles - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-serif">Chambres Bloquées</h1>
                    <p class="text-gray-600 mt-1">Liste des chambres actuellement fermées à la vente.</p>
                </div>
                <div class="mt-4 md:mt-0 space-x-2">
                    <a href="{{ route('marketing.indisponibilite.occupancy') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold shadow transition">
                        📅 Voir Planning / Occupations
                    </a>
                    <a href="{{ route('marketing.indisponibilite.select') }}" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-full font-bold shadow transition">
                        + Bloquer une chambre
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                @if($indisponibilites->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <span class="text-4xl block mb-4">✅</span>
                        Aucune chambre n'est actuellement bloquée. Tout est ouvert à la vente !
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Chambre</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dates</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($indisponibilites as $indispo)
                                    <tr class="hover:bg-red-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold">
                                                    {{ $indispo->chambre->numchambre }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        Chambre N°{{ $indispo->chambre->numchambre }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $indispo->chambre->typechambre->nomtype ?? 'Type inconnu' }}
                                                        <br>
                                                        <span class="text-xs text-gray-400">{{ $indispo->chambre->typechambre->resorts->first()->nomresort ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                Du <strong>{{ \Carbon\Carbon::parse($indispo->datedebut)->format('d/m/Y') }}</strong>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Au {{ \Carbon\Carbon::parse($indispo->datefin)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ $indispo->motif }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('marketing.indisponibilite.destroy', $indispo->numindisponibilite) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rouvrir cette chambre à la vente ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold border border-red-200 px-3 py-1 rounded hover:bg-red-50">
                                                    Libérer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>