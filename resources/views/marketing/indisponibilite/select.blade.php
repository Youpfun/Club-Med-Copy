<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les indisponibilités - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 font-serif">Gestion des Indisponibilités</h1>
            <p class="mb-8 text-gray-600">Sélectionnez le resort dans lequel vous souhaitez déclarer un incident ou des travaux.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($resorts as $resort)
                    <a href="{{ route('marketing.indisponibilite.create', $resort->numresort) }}" class="block bg-white p-6 rounded-xl shadow-sm hover:shadow-lg transition border border-gray-200 group">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-blue-900 group-hover:text-blue-700">{{ $resort->nomresort }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $resort->pays->nompays ?? '' }}</p>
                            </div>
                            <span class="text-2xl">🏨</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </main>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>