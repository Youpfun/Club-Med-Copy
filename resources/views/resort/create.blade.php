<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau Séjour - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="font-serif text-3xl font-bold text-gray-900 mt-4">Lancement d'un nouveau Séjour</h1>
                    <p class="text-gray-600 mt-2">Étape 1 : Initialisation des données (Identité, Hébergement, Restauration)</p>
                </div>
                <a href="{{ route('resorts.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold mt-4 md:mt-0">
                    ← Annuler
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0"><span class="text-red-500 text-xl">⚠️</span></div>
                        <div class="ml-3">
                            <h3 class="text-sm leading-5 font-medium text-red-800">Erreurs :</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('resort.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-6">1. Identité & Localisation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-800 mb-1">Pays du Resort *</label>
                            <select name="codepays" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Sélectionner le pays --</option>
                                @foreach($paysList as $p)
                                    <option value="{{ $p->codepays }}" {{ old('codepays') == $p->codepays ? 'selected' : '' }}>
                                        {{ $p->nompays }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du Resort *</label>
                            <input type="text" name="nomresort" required class="w-full rounded-md border-gray-300 shadow-sm" value="{{ old('nomresort') }}" placeholder="Ex: Club Med Val Thorens">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gamme (Tridents) *</label>
                            <select name="nbtridents" required class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="3">3 Tridents</option>
                                <option value="4" selected>4 Tridents</option>
                                <option value="5">5 Tridents (Exclusive)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Groupes / Labels</label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 h-32 overflow-y-auto">
                                @foreach($groupesList as $groupe)
                                    <label class="flex items-center space-x-2 mb-2">
                                        <input type="checkbox" name="groupes[]" value="{{ $groupe->numregroupement }}" class="rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="text-sm text-gray-700">{{ $groupe->nomregroupement }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="descriptionresort" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('descriptionresort') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-6">2. Environnement & Ski</h2>
                    <div class="space-y-4">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_ski" id="is_ski_checkbox" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 h-5 w-5" onclick="toggleSki()">
                            <span class="ml-3 text-gray-900 font-medium text-lg">Est-ce une station de ski ?</span>
                        </label>

                        <div id="ski_options" class="hidden pl-6 border-l-4 border-blue-100 bg-blue-50 p-4 rounded-r transition-all mt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Domaine Skiable</label>
                            <select name="numdomaine" class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm">
                                <option value="">-- Sélectionner le domaine --</option>
                                @foreach($domainesList as $d)
                                    <option value="{{ $d->numdomaine }}">{{ $d->nomdomaine }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase">Latitude</label>
                                <input type="text" name="latituderesort" class="w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: 45.xxxx">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase">Longitude</label>
                                <input type="text" name="longituderesort" class="w-full rounded-md border-gray-300 shadow-sm" placeholder="Ex: 6.xxxx">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-4">3. Types de Chambres & Tarifs</h2>
                    <p class="text-sm text-gray-500 mb-4">Sélectionnez les chambres disponibles, la quantité et le prix de base par nuit.</p>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Activer</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type de Chambre</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Quantité</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tarif / Nuit (€)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($typesChambre as $type)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <input type="checkbox" name="chambres[{{ $type->numtype }}][active]" value="1" 
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-5 h-5 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $type->nomtype }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <input type="number" name="chambres[{{ $type->numtype }}][quantite]" placeholder="0" min="0" 
                                                   class="w-24 rounded border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="relative rounded-md shadow-sm w-32">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-gray-500 sm:text-sm">€</span>
                                                </div>
                                                <input type="number" name="chambres[{{ $type->numtype }}][prix]" placeholder="0.00" min="0" step="0.01"
                                                       class="block w-full rounded-md border-gray-300 pl-7 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <div class="flex justify-between items-center border-b pb-2 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">4. Restaurants & Bars</h2>
                        <button type="button" onclick="addRestaurantField()" class="flex items-center gap-2 text-sm bg-green-100 text-green-700 px-4 py-2 rounded-full hover:bg-green-200 font-bold transition">
                            <span>+</span> Ajouter un lieu
                        </button>
                    </div>
                    
                    <div id="restaurants-container" class="space-y-4">
                        <p class="text-sm text-gray-500 italic mb-4">Types autorisés : Gourmet, Buffet, Snack, Bar.</p>
                        
                        <div class="grid grid-cols-12 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                            <div class="col-span-7">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nom</label>
                                <input type="text" name="restaurants[0][nom]" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500" placeholder="Ex: Le Buffet Principal">
                            </div>
                            <div class="col-span-5">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Type</label>
                                <select name="restaurants[0][type]" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500">
                                    <option value="">-- Choisir --</option>
                                    <option value="Gourmet">Gourmet</option>
                                    <option value="Buffet">Buffet</option>
                                    <option value="Snack">Snack</option>
                                    <option value="Bar">Bar</option>
                                </select>
                            </div>
                            <div class="col-span-12">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                                <textarea name="restaurants[0][description]" rows="2" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500" placeholder="Cuisine internationale, vue mer..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-4">5. Photos du Séjour</h2>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition relative bg-gray-50 cursor-pointer">
                        <input type="file" name="photos[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewFiles(this)">
                        <div class="space-y-3">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-600 font-medium">Cliquez ou glissez-déposez vos images ici (Conversion automatique en WebP)</p>
                            <p class="text-xs text-gray-500">JPG, PNG (Max 4 Mo)</p>
                        </div>
                    </div>
                    <div id="file-preview" class="mt-4 text-sm text-green-600 font-bold text-center"></div>
                </div>

                <div class="pt-6 flex justify-end gap-4 sticky bottom-0 bg-white/90 backdrop-blur-sm py-4 border-t z-50">
                    <button type="submit" class="flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-full shadow-xl transform transition hover:scale-105">
                        <span>Enregistrer pour l'étape suivante</span>
                        <svg class="ml-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleSki() {
            const checkbox = document.getElementById('is_ski_checkbox');
            const options = document.getElementById('ski_options');
            if (checkbox.checked) {
                options.classList.remove('hidden');
            } else {
                options.classList.add('hidden');
                document.querySelector('[name="numdomaine"]').value = "";
            }
        }

        function previewFiles(input) {
            const preview = document.getElementById('file-preview');
            if(input.files.length > 0) {
                preview.textContent = "✅ " + input.files.length + " photo(s) prête(s)";
            } else {
                preview.textContent = "";
            }
        }

        let restoCount = 1;
        function addRestaurantField() {
            const container = document.getElementById('restaurants-container');
            const html = `
                <div class="grid grid-cols-12 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200 relative animate-fade-in mt-4">
                    <div class="col-span-7">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nom</label>
                        <input type="text" name="restaurants[${restoCount}][nom]" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500" placeholder="Nom du lieu">
                    </div>
                    <div class="col-span-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Type</label>
                        <select name="restaurants[${restoCount}][type]" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500">
                            <option value="">-- Choisir --</option>
                            <option value="Gourmet">Gourmet</option>
                            <option value="Buffet">Buffet</option>
                            <option value="Snack">Snack</option>
                            <option value="Bar">Bar</option>
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                        <textarea name="restaurants[${restoCount}][description]" rows="2" class="w-full rounded border-gray-300 shadow-sm focus:ring-blue-500" placeholder="Description..."></textarea>
                    </div>
                    <div class="col-span-12 text-right">
                        <button type="button" onclick="this.closest('.grid').remove()" class="text-white bg-red-500 hover:bg-red-600 rounded px-2 py-1 text-xs transition shadow">
                            Supprimer
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            restoCount++;
        }
    </script>
    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</body>
</html>