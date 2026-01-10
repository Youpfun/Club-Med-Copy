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
            <div class="mb-8">
                <div class="flex items-center justify-between text-sm font-medium text-gray-500">
                    <span class="text-green-600">1. Structure (Fait)</span>
                    <span class="text-green-600">2. Hébergement (Fait)</span>
                    <span class="text-blue-600 font-bold">3. Activités & Services</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-green-500 h-2.5 rounded-full" style="width: 90%"></div>
                </div>
            </div>

            <div class="mb-6">
                <h1 class="text-3xl font-serif font-bold text-gray-900">Étape 3 : Activités</h1>
                <p class="text-gray-600">Gérez les activités pour <strong>{{ $resort->nomresort }}</strong>.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('success') }}</div>
            @endif

            <form action="{{ route('resort.storeStep3', $resort->numresort) }}" method="POST">
                @csrf

                {{-- SECTION 1 : LISTE DES ACTIVITÉS DÉJÀ PRÉSENTES --}}
                <div class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        ✅ Activités actuelles du Resort
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $resortActivities->count() }}</span>
                    </h3>
                    
                    @if($resortActivities->isEmpty())
                        <p class="text-gray-400 italic text-sm">Aucune activité configurée pour ce resort.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500">
                                    <tr>
                                        <th class="py-2 px-3 text-left">Nom</th>
                                        <th class="py-2 px-3 text-left">Type</th>
                                        <th class="py-2 px-3 text-center">Formule</th>
                                        <th class="py-2 px-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($resortActivities as $act)
                                        <tr>
                                            <td class="py-2 px-3 font-medium text-gray-900">{{ $act->nomactivite }}</td>
                                            <td class="py-2 px-3 text-gray-600">{{ $act->typeActivite->nomtypeactivite ?? '-' }}</td>
                                            <td class="py-2 px-3 text-center">
                                                @if($act->estincluse)
                                                    <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs">Inclus</span>
                                                @else
                                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs">À la carte (€)</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                <button type="submit" form="delete-form-{{ $act->numactivite }}" class="text-red-500 hover:text-red-700 font-bold bg-red-50 hover:bg-red-100 rounded px-3 py-1 transition text-xs flex items-center gap-1 ml-auto">
                                                    <span>🗑️</span> Supprimer
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- ONGLETS POUR AJOUTER --}}
                <div x-data="{ tab: 'existing' }">
                    <div class="flex gap-4 mb-4 border-b border-gray-200">
                        <button type="button" @click="tab = 'existing'" 
                                :class="tab === 'existing' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                                class="pb-2 font-bold px-4 transition">
                            📚 Ajouter depuis le Catalogue
                        </button>
                        <button type="button" @click="tab = 'new'" 
                                :class="tab === 'new' ? 'border-b-2 border-green-600 text-green-600' : 'text-gray-500 hover:text-gray-700'"
                                class="pb-2 font-bold px-4 transition">
                            ✨ Créer sur mesure
                        </button>
                    </div>

                    {{-- ONGLET CATALOGUE --}}
                    <div x-show="tab === 'existing'" class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-6">
                        <div class="mb-4">
                            <input type="text" id="searchActivity" placeholder="Rechercher une activité (ex: Yoga, Tennis)..." class="w-full rounded border-gray-300 shadow-sm" onkeyup="filterActivities()">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto" id="activitiesList">
                            @foreach($globalActivities as $act)
                                <label class="activity-item flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer transition group">
                                    <input type="checkbox" name="selected_activities[]" value="{{ $act->numactivite }}" 
                                           class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900 group-hover:text-blue-600">{{ $act->nomactivite }}</span>
                                        <span class="block text-xs text-gray-500">{{ $act->typeActivite->nomtypeactivite ?? 'Type inconnu' }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-2 italic">* Ces activités seront copiées et incluses dans votre formule.</p>
                    </div>

                    {{-- ONGLET CRÉATION --}}
                    <div x-show="tab === 'new'" class="bg-white p-6 rounded-xl shadow border border-gray-200 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-gray-700">Créer une activité spécifique</h3>
                            <button type="button" onclick="addNewRow()" class="text-sm bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold hover:bg-green-200">+ Ajouter une ligne</button>
                        </div>

                        <table class="w-full">
                            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                                <tr>
                                    <th class="text-left py-2 px-2 w-1/4">Type *</th>
                                    <th class="text-left py-2 px-2 w-1/4">Nom *</th>
                                    <th class="text-left py-2 px-2 w-1/4">Formule *</th>
                                    <th class="text-left py-2 px-2">Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="newActivitiesBody">
                                {{-- JS inserts rows here --}}
                            </tbody>
                        </table>
                        <div id="emptyState" class="text-center text-gray-400 py-8 italic">
                            Cliquez sur "+ Ajouter une ligne" pour créer une activité manuellement.
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-4 border-t flex justify-between items-center">
                    <a href="{{ route('marketing.dashboard') }}" class="text-gray-500 hover:text-gray-700 underline font-medium">Sauvegarder et quitter</a>
                    
                    <button type="submit" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-full shadow-lg transform transition hover:scale-105 flex items-center gap-2">
                        <span>Enregistrer les ajouts et Terminer</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </form>

            {{-- FORMULAIRES DE SUPPRESSION (INVISIBLES) --}}
            @foreach($resortActivities as $act)
                <form id="delete-form-{{ $act->numactivite }}" action="{{ route('resort.activity.destroy', ['id' => $resort->numresort, 'activityId' => $act->numactivite]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach

        </div>
    </main>

    <script>
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

        let newCount = 0;
        function addNewRow() {
            document.getElementById('emptyState').style.display = 'none';
            const container = document.getElementById('newActivitiesBody');
            
            let typeOptions = '<option value="">-- Type --</option>';
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
                        <input type="text" name="new_activities[${newCount}][nom]" class="w-full rounded border-gray-300 text-sm" placeholder="Ex: Cours de Salsa" required>
                    </td>
                    <td class="p-2">
                        <select name="new_activities[${newCount}][inclus]" class="w-full rounded border-gray-300 text-sm font-bold text-gray-700">
                            <option value="1" selected>✅ Inclus</option>
                            <option value="0">💰 À la carte</option>
                        </select>
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

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>