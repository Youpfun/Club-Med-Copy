<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte - Club Med</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { clubmed: { DEFAULT: '#113559' } } } } }
    </script>
</head>
<body class="bg-gray-50 text-clubmed font-sans py-10">

<div class="max-w-3xl mx-auto bg-white p-8 rounded shadow-lg border-t-4 border-clubmed">
    
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold">CRÉER UN COMPTE</h2>
        <a href="{{ route('login') }}" class="text-sm font-bold text-clubmed hover:underline">Déjà inscrit ?</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded text-sm border border-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inscription.store') }}" method="POST">
        @csrf

        <div class="mb-4 text-right">
            <p class="text-xs text-gray-500 italic">Les champs marqués d'un astérisque (*) sont obligatoires.</p>
        </div>

        <div class="mb-6 flex gap-4">
            <label class="flex items-center cursor-pointer border px-4 py-3 rounded w-1/2 hover:bg-gray-50 {{ old('genre') == 'M' ? 'border-clubmed ring-1 ring-clubmed' : 'border-gray-300' }}">
                <input type="radio" name="genre" value="M" class="mr-2" {{ old('genre') == 'M' ? 'checked' : '' }}>
                <span class="font-bold text-sm">MONSIEUR</span>
            </label>
            <label class="flex items-center cursor-pointer border px-4 py-3 rounded w-1/2 hover:bg-gray-50 {{ old('genre') == 'F' ? 'border-clubmed ring-1 ring-clubmed' : 'border-gray-300' }}">
                <input type="radio" name="genre" value="F" class="mr-2" {{ old('genre') == 'F' ? 'checked' : '' }}>
                <span class="font-bold text-sm">MADAME</span>
            </label>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-4">
            <div>
                <label class="block text-xs font-bold mb-1">Prénom*</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" class="w-full border p-2 rounded text-sm outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" required>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">Nom*</label>
                <input type="text" name="nom" value="{{ old('nom') }}" class="w-full border p-2 rounded text-sm outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-bold mb-1">Date de naissance*</label>
            <input 
                type="date" 
                name="datenaissance" 
                value="{{ old('datenaissance') }}" 
                min="{{ date('Y-m-d', strtotime('-120 years')) }}" 
                max="{{ date('Y-m-d') }}"
                class="w-full border p-2 rounded text-sm text-gray-600 outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" 
                required>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold mb-1">E-mail*</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded text-sm outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" required>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">Téléphone portable*</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" class="w-full border p-2 rounded text-sm outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" required>
            </div>
        </div>

        <hr class="my-6 border-gray-200">
        
        <div class="mb-6">
            <label class="block text-xs font-bold mb-1 text-clubmed">Recherche d'adresse automatique</label>
            <div class="relative">
                <input type="text" id="adresse-search" placeholder="Commencez à taper votre adresse (ex: 10 rue de la paix...)" class="w-full border p-3 rounded text-sm outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed bg-blue-50 placeholder-gray-500" autocomplete="off">
                
                <ul id="adresse-results" class="hidden absolute z-50 w-full bg-white border border-gray-300 rounded-b shadow-xl max-h-60 overflow-y-auto mt-0 text-sm">
                    </ul>
            </div>
            <p class="text-[10px] text-gray-500 mt-1 ml-1">Sélectionnez votre adresse dans la liste pour remplir les champs ci-dessous.</p>
        </div>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="col-span-1">
                <label class="block text-xs font-bold mb-1">N°*</label>
                <input type="number" name="numrue" id="numrue" value="{{ old('numrue') }}" class="w-full border p-2 rounded text-sm bg-gray-50 focus:bg-white transition" required>
            </div>
            <div class="col-span-3">
                <label class="block text-xs font-bold mb-1">Rue*</label>
                <input type="text" name="nomrue" id="nomrue" value="{{ old('nomrue') }}" class="w-full border p-2 rounded text-sm bg-gray-50 focus:bg-white transition" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold mb-1">Code postal*</label>
                <input type="text" name="codepostal" id="codepostal" value="{{ old('codepostal') }}" class="w-full border p-2 rounded text-sm bg-gray-50 focus:bg-white transition" required>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">Ville*</label>
                <input type="text" name="ville" id="ville" value="{{ old('ville') }}" class="w-full border p-2 rounded text-sm bg-gray-50 focus:bg-white transition" required>
            </div>
        </div>

        <div class="bg-blue-50 p-6 rounded mb-6 border border-blue-100">
            <h3 class="text-sm font-bold mb-4 text-clubmed uppercase">SÉCURITÉ DU COMPTE</h3>
            
            <p class="text-xs text-gray-600 mb-4 bg-white p-3 rounded border border-blue-100">
                <span class="font-bold">Information :</span> Un code de validation à 6 chiffres vous sera envoyé par <strong>email</strong> pour activer votre compte.
            </p>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold mb-1">Mot de passe*</label>
                    <input type="password" name="password" minlength="8" class="w-full border p-2 rounded text-sm focus:border-clubmed focus:ring-1 focus:ring-clubmed outline-none" placeholder="••••••••" required>
                    <p class="text-[10px] text-gray-500 mt-1">8 caractères minimum requis.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Confirmer mot de passe*</label>
                    <input type="password" name="password_confirmation" minlength="8" class="w-full border p-2 rounded text-sm focus:border-clubmed focus:ring-1 focus:ring-clubmed outline-none" placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-clubmed text-white py-4 rounded font-bold uppercase tracking-wide hover:bg-opacity-90 transition shadow-lg text-base">
            CRÉER MON COMPTE
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adresse-search');
        const resultsList = document.getElementById('adresse-results');

        const numInput = document.getElementById('numrue');
        const rueInput = document.getElementById('nomrue');
        const cpInput = document.getElementById('codepostal');
        const villeInput = document.getElementById('ville');

        searchInput.addEventListener('input', function() {
            const query = this.value;

            if (query.length > 3) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`)
                    .then(response => response.json())
                    .then(data => {
                        resultsList.innerHTML = '';
                        
                        if (data.features && data.features.length > 0) {
                            resultsList.classList.remove('hidden');

                            data.features.forEach(feature => {
                                const li = document.createElement('li');
                                li.classList.add('p-3', 'hover:bg-blue-100', 'cursor-pointer', 'border-b', 'last:border-b-0', 'text-gray-700');
                                li.textContent = feature.properties.label;

                                li.addEventListener('click', () => {
                                    numInput.value = feature.properties.housenumber || ''; 
                                    rueInput.value = feature.properties.street || feature.properties.name;
                                    cpInput.value = feature.properties.postcode;
                                    villeInput.value = feature.properties.city;
                                    resultsList.classList.add('hidden');
                                    searchInput.value = "";
                                    searchInput.placeholder = "Adresse sélectionnée : " + feature.properties.label;
                                });

                                resultsList.appendChild(li);
                            });
                        } else {
                            resultsList.classList.add('hidden');
                        }
                    })
                    .catch(err => console.error('Erreur API Adresse:', err));
            } else {
                resultsList.classList.add('hidden');
            }
        });
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                resultsList.classList.add('hidden');
            }
        });
    });
</script>

</body>
</html>