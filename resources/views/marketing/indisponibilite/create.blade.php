<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloquer une chambre - {{ $resort->nomresort }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('marketing.indisponibilite.select') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">← Retour à la liste</a>
                <h1 class="text-3xl font-bold text-gray-900 mt-2 font-serif">Déclarer une indisponibilité</h1>
                <p class="text-gray-600">Resort : <strong>{{ $resort->nomresort }}</strong></p>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0"><span class="text-red-500">⚠️</span></div>
                            <div class="ml-3">
                                <h3 class="text-sm leading-5 font-medium text-red-800">Erreur :</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('marketing.indisponibilite.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Chambre concernée</label>
                        <select name="idchambre" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" required>
                            <option value="">-- Choisir une chambre --</option>
                            @foreach($types as $type)
                                <optgroup label="{{ $type->nomtype }}">
                                    @foreach($type->chambres as $chambre)
                                        <option value="{{ $chambre->idchambre }}">
                                            Chambre N°{{ $chambre->numchambre }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1 italic">Si la liste est vide, aucune chambre physique n'a encore été créée pour ce resort.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                            <input type="date" name="datedebut" required min="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                            <input type="date" name="datefin" required min="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motif / Raison</label>
                        <textarea name="motif" rows="3" required placeholder="Ex: Rénovation peinture, Dégâts des eaux..." class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-full shadow-md transition transform hover:scale-105">
                            Valider l'indisponibilité
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>