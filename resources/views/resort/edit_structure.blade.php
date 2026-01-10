<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Structure - {{ $resort->nomresort }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-6xl mx-auto">
            
            <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="font-serif text-3xl font-bold text-gray-900 mt-4">Modifier : {{ $resort->nomresort }}</h1>
                    <p class="text-gray-600 mt-2">Mise à jour de l'identité, localisation, photos et restauration.</p>
                </div>
                <a href="{{ route('marketing.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold mt-4 md:mt-0">
                    ← Retour Tableau de Bord
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                    <ul class="list-disc pl-5 text-red-700">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('resort.updateStructure', $resort->numresort) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-6">1. Identité & Localisation</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-800 mb-1">Pays du Resort *</label>
                            <select name="codepays" required class="w-full rounded-md border-gray-300">
                                @foreach($paysList as $p)
                                    <option value="{{ $p->codepays }}" {{ $resort->codepays == $p->codepays ? 'selected' : '' }}>{{ $p->nompays }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom du Resort *</label>
                            <input type="text" name="nomresort" required value="{{ $resort->nomresort }}" class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gamme</label>
                            <select name="nbtridents" class="w-full rounded-md border-gray-300">
                                <option value="3" {{ $resort->nbtridents == 3 ? 'selected' : '' }}>3 Tridents</option>
                                <option value="4" {{ $resort->nbtridents == 4 ? 'selected' : '' }}>4 Tridents</option>
                                <option value="5" {{ $resort->nbtridents == 5 ? 'selected' : '' }}>5 Tridents</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Groupes</label>
                            <div class="bg-gray-50 p-3 rounded-md border border-gray-200 h-32 overflow-y-auto">
                                @foreach($groupesList as $groupe)
                                    <label class="flex items-center space-x-2 mb-2">
                                        <input type="checkbox" name="groupes[]" value="{{ $groupe->numregroupement }}" 
                                               class="rounded text-blue-600"
                                               {{ $resort->regroupements->contains($groupe->numregroupement) ? 'checked' : '' }}>
                                        <span class="text-sm text-gray-700">{{ $groupe->nomregroupement }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="descriptionresort" rows="3" class="w-full rounded-md border-gray-300">{{ $resort->descriptionresort }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-6">2. Environnement & Ski</h2>
                    <div class="space-y-4">
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_ski" id="is_ski_checkbox" value="1" class="rounded border-gray-300 text-blue-600" 
                                   {{ $resort->numdomaine ? 'checked' : '' }} onclick="toggleSki()">
                            <span class="ml-3 text-gray-900 font-medium text-lg">Est-ce une station de ski ?</span>
                        </label>
                        <div id="ski_options" class="{{ $resort->numdomaine ? '' : 'hidden' }} pl-6 border-l-4 border-blue-100 bg-blue-50 p-4 rounded-r mt-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Domaine Skiable</label>
                            <select name="numdomaine" class="w-full md:w-1/2 rounded-md border-gray-300">
                                <option value="">-- Sélectionner --</option>
                                @foreach($domainesList as $d)
                                    <option value="{{ $d->numdomaine }}" {{ $resort->numdomaine == $d->numdomaine ? 'selected' : '' }}>{{ $d->nomdomaine }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div><label class="text-xs uppercase">Latitude</label><input type="text" name="latituderesort" value="{{ $resort->latituderesort }}" class="w-full rounded-md border-gray-300"></div>
                            <div><label class="text-xs uppercase">Longitude</label><input type="text" name="longituderesort" value="{{ $resort->longituderesort }}" class="w-full rounded-md border-gray-300"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 border-b pb-2 mb-4">3. Photos (Ajout)</h2>
                    <input type="file" name="photos[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="bg-white rounded-xl shadow border border-gray-200 p-6">
                    <div class="flex justify-between items-center border-b pb-2 mb-6">
                        <h2 class="text-xl font-bold text-gray-900">4. Restaurants</h2>
                        <button type="button" onclick="addRestaurantField()" class="text-sm bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold hover:bg-green-200">+ Ajouter</button>
                    </div>
                    <div id="restaurants-container" class="space-y-4">
                        @foreach($resort->restaurants as $index => $resto)
                            <div class="grid grid-cols-12 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                <div class="col-span-7">
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nom</label>
                                    <input type="text" name="restaurants[{{ $index }}][nom]" value="{{ $resto->nomrestaurant }}" class="w-full rounded border-gray-300 shadow-sm">
                                </div>
                                <div class="col-span-5">
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Type</label>
                                    <select name="restaurants[{{ $index }}][type]" class="w-full rounded border-gray-300 shadow-sm">
                                        <option value="Gourmet" {{ $resto->typerestaurant == 'Gourmet' ? 'selected' : '' }}>Gourmet</option>
                                        <option value="Buffet" {{ $resto->typerestaurant == 'Buffet' ? 'selected' : '' }}>Buffet</option>
                                        <option value="Snack" {{ $resto->typerestaurant == 'Snack' ? 'selected' : '' }}>Snack</option>
                                        <option value="Bar" {{ $resto->typerestaurant == 'Bar' ? 'selected' : '' }}>Bar</option>
                                    </select>
                                </div>
                                <div class="col-span-12">
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                                    <textarea name="restaurants[{{ $index }}][description]" rows="2" class="w-full rounded border-gray-300 shadow-sm">{{ $resto->descriptionrestaurant }}</textarea>
                                </div>
                                <div class="col-span-12 text-right">
                                    <button type="button" onclick="this.closest('.grid').remove()" class="text-white bg-red-500 rounded px-2 py-1 text-xs">Supprimer</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-6 border-t flex justify-between items-center bg-white/90 backdrop-blur-sm py-4 sticky bottom-0 z-50">
                    <button type="submit" name="action" value="save_exit" class="text-gray-500 hover:text-gray-800 font-bold underline">Sauvegarder et quitter</button>
                    <button type="submit" name="action" value="next" class="flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-full shadow-xl">
                        <span>Sauvegarder & Continuer</span>
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleSki() {
            const checkbox = document.getElementById('is_ski_checkbox');
            const options = document.getElementById('ski_options');
            if (checkbox.checked) options.classList.remove('hidden');
            else {
                options.classList.add('hidden');
                document.querySelector('[name="numdomaine"]').value = "";
            }
        }

        let restoCount = {{ $resort->restaurants->count() }};
        function addRestaurantField() {
            const container = document.getElementById('restaurants-container');
            const html = `
                <div class="grid grid-cols-12 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-200 relative mt-4">
                    <div class="col-span-7">
                        <label class="text-xs font-bold uppercase">Nom</label>
                        <input type="text" name="restaurants[${restoCount}][nom]" class="w-full rounded border-gray-300">
                    </div>
                    <div class="col-span-5">
                        <label class="text-xs font-bold uppercase">Type</label>
                        <select name="restaurants[${restoCount}][type]" class="w-full rounded border-gray-300">
                            <option value="Gourmet">Gourmet</option>
                            <option value="Buffet">Buffet</option>
                            <option value="Snack">Snack</option>
                            <option value="Bar">Bar</option>
                        </select>
                    </div>
                    <div class="col-span-12">
                        <label class="text-xs font-bold uppercase">Description</label>
                        <textarea name="restaurants[${restoCount}][description]" rows="2" class="w-full rounded border-gray-300"></textarea>
                    </div>
                    <div class="col-span-12 text-right">
                        <button type="button" onclick="this.closest('.grid').remove()" class="text-white bg-red-500 rounded px-2 py-1 text-xs">Supprimer</button>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            restoCount++;
        }
    </script>
</body>
</html>