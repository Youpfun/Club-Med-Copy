<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte - Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-clubmed-beige font-sans py-10">

<div class="max-w-3xl mx-auto px-4">
    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="/">
            <img src="/img/logo-clubmed.png" alt="Club Med" class="h-14 mx-auto">
        </a>
    </div>

    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl">
    
        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-6">
            <h2 class="font-serif text-3xl font-bold text-clubmed-blue">Créer un compte</h2>
            <a href="{{ route('login') }}" class="text-sm font-bold text-clubmed-blue hover:text-clubmed-gold transition-colors">Déjà inscrit ?</a>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-100">
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
                <label class="flex items-center cursor-pointer border-2 px-5 py-4 rounded-xl w-1/2 hover:border-clubmed-gold transition-all {{ old('genre') == 'M' ? 'border-clubmed-gold bg-clubmed-beige' : 'border-gray-200' }}">
                    <input type="radio" name="genre" value="M" class="mr-3 text-clubmed-gold focus:ring-clubmed-gold" {{ old('genre') == 'M' ? 'checked' : '' }}>
                    <span class="font-bold text-sm text-clubmed-blue">Monsieur</span>
                </label>
                <label class="flex items-center cursor-pointer border-2 px-5 py-4 rounded-xl w-1/2 hover:border-clubmed-gold transition-all {{ old('genre') == 'F' ? 'border-clubmed-gold bg-clubmed-beige' : 'border-gray-200' }}">
                    <input type="radio" name="genre" value="F" class="mr-3 text-clubmed-gold focus:ring-clubmed-gold" {{ old('genre') == 'F' ? 'checked' : '' }}>
                    <span class="font-bold text-sm text-clubmed-blue">Madame</span>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Prénom*</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom*</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date de naissance*</label>
                <input 
                    type="date" 
                    name="datenaissance" 
                    value="{{ old('datenaissance') }}" 
                    min="{{ date('Y-m-d', strtotime('-120 years')) }}" 
                    max="{{ date('Y-m-d') }}"
                    class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm text-gray-600 outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" 
                    required>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail*</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Téléphone portable*</label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
            </div>

            <hr class="my-8 border-gray-100">
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-clubmed-blue mb-2">Recherche d'adresse automatique</label>
                <div class="relative">
                    <input type="text" id="adresse-search" placeholder="Commencez à taper votre adresse (ex: 10 rue de la paix...)" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm outline-none focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 bg-clubmed-beige placeholder-gray-500 transition-all" autocomplete="off">
                    
                    <ul id="adresse-results" class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded-b-xl shadow-xl max-h-60 overflow-y-auto mt-0 text-sm">
                    </ul>
                </div>
                <p class="text-xs text-gray-500 mt-2">Sélectionnez votre adresse dans la liste pour remplir les champs ci-dessous.</p>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-5">
                <div class="col-span-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">N°*</label>
                    <input type="number" name="numrue" id="numrue" value="{{ old('numrue') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rue*</label>
                    <input type="text" name="nomrue" id="nomrue" value="{{ old('nomrue') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Code postal*</label>
                    <input type="text" name="codepostal" id="codepostal" value="{{ old('codepostal') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ville*</label>
                    <input type="text" name="ville" id="ville" value="{{ old('ville') }}" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm bg-gray-50 focus:bg-white focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 transition-all" required>
                </div>
            </div>

            <div class="bg-clubmed-beige p-6 rounded-2xl mb-8">
                <h3 class="text-sm font-bold mb-4 text-clubmed-blue uppercase tracking-wide">Sécurité du compte</h3>
                
                <p class="text-sm text-gray-600 mb-5 bg-white p-4 rounded-xl border border-gray-100">
                    <span class="font-bold">Information :</span> Un code de validation à 6 chiffres vous sera envoyé par <strong>email</strong> pour activer votre compte.
                </p>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mot de passe*</label>
                        <input type="password" name="password" minlength="8" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 outline-none bg-white transition-all" placeholder="••••••••" required>
                        <p class="text-xs text-gray-500 mt-1">8 caractères minimum requis.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmer*</label>
                        <input type="password" name="password_confirmation" minlength="8" class="w-full border-2 border-gray-200 px-4 py-3 rounded-xl text-sm focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 outline-none bg-white transition-all" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-clubmed-blue text-white py-4 rounded-full font-bold uppercase tracking-wide hover:bg-clubmed-blue-dark transition-all shadow-lg hover:shadow-xl text-sm">
                Créer mon compte
            </button>
        </form>
        
        <div class="mt-6 text-center">
             <a href="/" class="text-sm text-gray-400 hover:text-clubmed-blue transition-colors">← Retour à l'accueil</a>
        </div>
    </div>
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
                                li.classList.add('p-3', 'hover:bg-clubmed-beige', 'cursor-pointer', 'border-b', 'last:border-b-0', 'text-gray-700', 'transition-colors');
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

{{-- Chatbot BotMan --}}
@include('layouts.chatbot')
</body>
</html>