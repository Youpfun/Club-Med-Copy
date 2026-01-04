<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer Séjour - Étape 3/3</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            
            {{-- Message d'erreur visible si la validation échoue --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0 text-red-500">⚠️</div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-red-800">Il y a des erreurs dans le formulaire :</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <h1 class="text-3xl font-serif font-bold text-gray-900">Étape 3 : Activités</h1>
                <p class="text-gray-600">Configurez les activités pour <strong>{{ $resort->nomresort }}</strong>.</p>
            </div>

            <form action="{{ route('resort.storeStep3', $resort->numresort) }}" method="POST" x-data="{ tab: 'existing' }">
                @csrf

                <div class="flex gap-4 mb-6 border-b border-gray-200">
                    <button type="button" @click="tab = 'existing'" 
                            :class="tab === 'existing' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="pb-2 font-bold px-4 transition">
                        📚 Sélectionner existantes
                    </button>
                    <button type="button" @click="tab = 'new'" 
                            :class="tab === 'new' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500 hover:text-gray-700'"
                            class="pb-2 font-bold px-4 transition">
                        ✨ Créer nouvelles
                    </button>
                </div>

                <div x-show="tab === 'existing'" class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-6">
                    <div class="mb-4">
                        <input type="text" id="searchActivity" placeholder="Filtrer les activités..." class="w-full rounded border-gray-300 shadow-sm" onkeyup="filterActivities()">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto" id="activitiesList">
                        @foreach($allActivities as $act)
                            <label class="activity-item flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer transition">
                                {{-- ATTENTION : Le nom est bien selected_activities[] --}}
                                <input type="checkbox" name="selected_activities[]" value="{{ $act->numactivite }}" 
                                       class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                       {{ in_array($act->numtypeactivite, $currentTypeIds) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="block font-medium text-gray-900">{{ $act->nomactivite }}</span>
                                    <span class="block text-xs text-gray-500">{{ $act->typeActivite->nomtypeactivite ?? 'Type inconnu' }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-show="tab === 'new'" class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-700">Ajouter des activités spécifiques</h3>
                        <button type="button" onclick="addNewRow()" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold hover:bg-green-200">+ Ajouter une ligne</button>
                    </div>

                    <table class="w-full">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="text-left py-2 px-2">Type *</th>
                                <th class="text-left py-2 px-2">Nom *</th>
                                <th class="text-left py-2 px-2">Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="newActivitiesBody">
                            </tbody>
                    </table>
                    <div id="emptyState" class="text-center text-gray-400 py-8 italic">
                        Cliquez sur "+ Ajouter une ligne" si vous ne trouvez pas votre bonheur dans le catalogue.
                    </div>
                </div>

                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('marketing.dashboard') }}" class="text-gray-500 hover:text-gray-700 underline">Passer cette étape</a>
                    <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow-lg transform transition hover:scale-105">
                        Valider et Terminer
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // FILTRE JS
        function filterActivities() {
            const input = document.getElementById('searchActivity');
            const filter = input.value.toLowerCase();
            const list = document.getElementById('activitiesList');
            const labels = list.getElementsByClassName('activity-item');
            for (let i = 0; i < labels.length; i++) {
                const txtValue = labels[i].textContent || labels[i].innerText;
                labels[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            }
        }

        // GESTION AJOUT LIGNES
        let newCount = 0;
        function addNewRow() {
            document.getElementById('emptyState').style.display = 'none';
            const container = document.getElementById('newActivitiesBody');
            
            // On prépare les options du select TYPE
            let typeOptions = '<option value="">-- Choisir --</option>';
            @foreach($typesActivites as $t)
                typeOptions += `<option value="{{ $t->numtypeactivite }}">{{ $t->nomtypeactivite }}</option>`;
            @endforeach

            const html = `
                <tr class="border-b border-gray-100 animate-fade-in" id="new-row-${newCount}">
                    <td class="p-2">
                        <select name="new_activities[${newCount}][type]" class="w-full rounded border-gray-300 text-sm" required>
                            ${typeOptions}
                        </select>
                    </td>
                    <td class="p-2">
                        <input type="text" name="new_activities[${newCount}][nom]" class="w-full rounded border-gray-300 text-sm" placeholder="Ex: Yoga Plage" required>
                    </td>
                    <td class="p-2">
                        <input type="text" name="new_activities[${newCount}][description]" class="w-full rounded border-gray-300 text-sm" placeholder="Optionnel">
                    </td>
                    <td class="p-2 text-center">
                        <button type="button" onclick="document.getElementById('new-row-${newCount}').remove()" class="text-red-500 hover:text-red-700 font-bold">✖</button>
                    </td>
                </tr>
            `;
            container.insertAdjacentHTML('beforeend', html);
            newCount++;
        }
    </script>
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>