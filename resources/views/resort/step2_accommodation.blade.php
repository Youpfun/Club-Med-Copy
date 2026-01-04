<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer Séjour - Étape 2/3</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm font-medium text-gray-500">
                    <span class="text-green-600">1. Structure & Identité (Fait)</span>
                    <span class="text-blue-600 font-bold">2. Hébergement</span>
                    <span>3. Activités</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 66%"></div>
                </div>
            </div>

            <div class="mb-6">
                <h1 class="text-3xl font-serif font-bold text-gray-900">Étape 2 : Hébergements</h1>
                <p class="text-gray-600">Définissez les capacités pour le resort : <strong>{{ $resort->nomresort }}</strong></p>
            </div>

            <form action="{{ route('resort.storeStep2', $resort->numresort) }}" method="POST" class="bg-white p-6 rounded-xl shadow border border-gray-200">
                @csrf

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Activer</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type de Chambre</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Quantité Dispo</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tarif / Nuit (€)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($typesChambre as $type)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="chambres[{{ $type->numtype }}][active]" value="1" class="w-5 h-5 text-blue-600 rounded">
                                    </td>
                                    <td class="px-4 py-4 font-medium text-gray-900">{{ $type->nomtype }}</td>
                                    <td class="px-4 py-4">
                                        <input type="number" name="chambres[{{ $type->numtype }}][quantite]" class="w-24 rounded border-gray-300" placeholder="0">
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" name="chambres[{{ $type->numtype }}][prix]" class="w-32 rounded border-gray-300" placeholder="0.00" step="0.01">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-6 mt-6 border-t flex justify-between">
                    <a href="{{ route('marketing.dashboard') }}" class="text-gray-500 hover:text-gray-700 underline">Sauvegarder et quitter</a>
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg">
                        Suivant : Configurer les Activités →
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>