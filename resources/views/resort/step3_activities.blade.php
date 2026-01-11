<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer Séjour - Étape 3/4</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- PROGRESS BAR --}}
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm font-medium text-gray-500">
                    <span class="text-green-600">1. Structure (Fait)</span>
                    <span class="text-green-600">2. Hébergement (Fait)</span>
                    <span class="text-blue-600 font-bold">3. Activités</span>
                    <span>4. Tarifs</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: 75%"></div>
                </div>
            </div>

            <div class="mb-6">
                <h1 class="text-3xl font-serif font-bold text-gray-900">Étape 3 : Activités et Services</h1>
                <p class="text-gray-600">Choisissez les activités et définissez si elles sont incluses ou en supplément.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="{{ route('resort.storeStep3', $resort->numresort) }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    {{-- GAUCHE : SÉLECTION (CATALOGUE) --}}
                    <div class="lg:col-span-4 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">📚 Catalogue Global</h3>
                        <p class="text-xs text-gray-500 mb-2">Cochez pour ajouter à votre resort.</p>
                        <div class="h-[500px] overflow-y-auto pr-2 space-y-1">
                            @foreach($globalActivities as $act)
                                <label class="flex items-start p-2 rounded hover:bg-blue-50 cursor-pointer transition {{ $resortActivities->contains('numactivite', $act->numactivite) ? 'opacity-50' : '' }}">
                                    <input type="checkbox" name="selected_activities[]" value="{{ $act->numactivite }}" 
                                        class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm"
                                        {{ $resortActivities->contains('numactivite', $act->numactivite) ? 'checked disabled' : '' }}>
                                    <div class="ml-2">
                                        <span class="block text-sm font-medium text-gray-800">{{ $act->nomactivite }}</span>
                                        <span class="block text-xs text-gray-500">{{ $act->typeActivite->nomtypeactivite ?? 'Autre' }}</span>
                                    </div>
                                    @if($resortActivities->contains('numactivite', $act->numactivite))
                                        <span class="ml-auto text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Ajouté</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- DROITE : CONFIGURATION --}}
                    <div class="lg:col-span-8 space-y-6">
                        
                        {{-- LISTE DES ACTIVITÉS DU RESORT (EDITABLE) --}}
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex justify-between items-center">
                                <span>🏨 Activités du Resort</span>
                                <span class="text-xs font-normal bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $resortActivities->count() }} activités</span>
                            </h3>
                            
                            @if($resortActivities->isEmpty())
                                <div class="text-center py-10 bg-gray-50 rounded border border-dashed border-gray-300">
                                    <p class="text-gray-500">Aucune activité ajoutée pour le moment.</p>
                                    <p class="text-xs text-gray-400">Sélectionnez-en à gauche ou créez-en une ci-dessous.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Activité</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Formule</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix (€)</th>
                                                <th class="px-3 py-2"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($resortActivities as $ra)
                                                <tr x-data="{ inclus: '{{ $ra->pivot_inclus ? '1' : '0' }}' }">
                                                    <td class="px-3 py-3 text-sm font-bold text-gray-800">
                                                        {{ $ra->nomactivite }}
                                                    </td>
                                                    <td class="px-3 py-3 text-xs text-gray-500">
                                                        {{ $ra->nomtypeactivite }}
                                                    </td>
                                                    <td class="px-3 py-3">
                                                        {{-- SELECTEUR INCLUS / CARTE --}}
                                                        <select name="activities_config[{{ $ra->numactivite }}][inclus]" x-model="inclus" 
                                                                class="text-xs rounded border-gray-300 py-1 pl-2 pr-6 focus:ring-blue-500 focus:border-blue-500"
                                                                :class="inclus == '1' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200'">
                                                            <option value="1">✅ Inclus</option>
                                                            <option value="0">💰 À la carte</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-3 py-3">
                                                        {{-- INPUT PRIX (Visible seulement si À la carte) --}}
                                                        <input type="number" name="activities_config[{{ $ra->numactivite }}][prix]" 
                                                               value="{{ $ra->pivot_prix }}" 
                                                               x-show="inclus == '0'"
                                                               placeholder="20"
                                                               class="w-20 text-xs border-gray-300 rounded p-1" step="0.01">
                                                    </td>
                                                    <td class="px-3 py-3 text-right">
                                                        <button type="button" 
                                                                onclick="if(confirm('Retirer cette activité ?')) document.getElementById('del-{{ $ra->numactivite }}').submit();"
                                                                class="text-red-400 hover:text-red-600 font-bold px-2">
                                                            &times;
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- CRÉATION MANUELLE --}}
                        <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-2">✨ Créer une activité sur mesure</h3>
                            <div id="new-activities-container"></div>
                            <button type="button" onclick="addActivityRow()" class="mt-3 w-full py-2 border-2 border-dashed border-blue-300 text-blue-600 rounded-lg hover:bg-blue-100 font-bold transition">
                                + Ajouter une ligne
                            </button>
                        </div>
                    </div>
                </div>

                {{-- BOUTONS --}}
                <div class="pt-8 mt-8 border-t flex justify-between items-center">
                    <button type="submit" name="action" value="save_exit" class="text-gray-500 hover:text-gray-800 font-bold underline">
                        Sauvegarder et quitter
                    </button>
                    <button type="submit" name="action" value="next" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full shadow-lg flex items-center gap-2 transform transition hover:scale-105">
                        <span>Enregistrer et Continuer</span>
                        <span>→</span>
                    </button>
                </div>
            </form>

            {{-- FORMULAIRES SUPPRESSION --}}
            @foreach($resortActivities as $ra)
                <form id="del-{{ $ra->numactivite }}" action="{{ route('resort.activity.destroy', ['id' => $resort->numresort, 'activityId' => $ra->numactivite]) }}" method="POST" class="hidden">
                    @csrf @method('DELETE')
                </form>
            @endforeach

        </div>
    </main>

    <script>
        let newCount = 0;
        function addActivityRow() {
            const container = document.getElementById('new-activities-container');
            const html = `
                <div class="bg-white p-3 rounded shadow-sm mb-2 border border-blue-200 animate-fade-in" id="row-${newCount}">
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <div class="col-span-4">
                            <label class="block text-[10px] uppercase font-bold text-gray-500">Nom</label>
                            <input type="text" name="new_activities[${newCount}][nom]" class="w-full rounded border-gray-300 text-xs" required>
                        </div>
                        <div class="col-span-3">
                            <label class="block text-[10px] uppercase font-bold text-gray-500">Type</label>
                            <select name="new_activities[${newCount}][type]" class="w-full rounded border-gray-300 text-xs" required>
                                @foreach($typesActivites as $ta)
                                    <option value="{{ $ta->numtypeactivite }}">{{ $ta->nomtypeactivite }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-gray-500">Formule</label>
                            <select name="new_activities[${newCount}][inclus]" class="w-full rounded border-gray-300 text-xs" onchange="togglePrice(this, ${newCount})">
                                <option value="1">Inclus</option>
                                <option value="0">Carte</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-gray-500">Prix</label>
                            <input type="number" id="price-${newCount}" name="new_activities[${newCount}][prix]" class="w-full rounded border-gray-300 text-xs" placeholder="-" disabled>
                        </div>
                        <div class="col-span-1 text-right pt-4">
                            <button type="button" onclick="document.getElementById('row-${newCount}').remove()" class="text-red-500 font-bold">&times;</button>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            newCount++;
        }

        function togglePrice(select, id) {
            const input = document.getElementById(`price-${id}`);
            if (select.value === '0') {
                input.disabled = false;
                input.placeholder = "20";
            } else {
                input.disabled = true;
                input.value = "";
                input.placeholder = "-";
            }
        }
    </script>
    <style>.animate-fade-in { animation: fadeIn 0.3s ease-out; } @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }</style>
</body>
</html>