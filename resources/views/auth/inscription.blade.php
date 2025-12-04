<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte - Club Med</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clubmed: {
                            DEFAULT: '#113559',
                            light: '#006298',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-clubmed">

<div class="min-h-screen flex flex-col justify-center items-center py-10 px-4">
    
    <div class="bg-white p-8 rounded-sm shadow-lg w-full max-w-2xl border-t-4 border-clubmed">
        
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h2 class="text-2xl font-bold">Voyageur principal</h2>
                <p class="text-sm text-gray-500 mt-1">Qui est le voyageur principal pour ce compte ?</p>
            </div>
            <a href="{{ url('/login') }}" class="bg-clubmed text-white text-xs font-bold py-2 px-4 uppercase tracking-wider hover:bg-opacity-90 transition">
                Me connecter
            </a>
        </div>

        <div class="text-right text-xs text-gray-500 mb-4">* Champs obligatoires</div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                <p class="font-bold">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc pl-5 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inscription.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <div class="flex gap-4">
                    <label class="flex items-center cursor-pointer border px-4 py-3 rounded w-1/2 hover:bg-gray-50 {{ old('genre') == 'M' ? 'border-clubmed bg-blue-50' : 'border-gray-300' }} @error('genre') border-red-500 @enderror">
                        <input type="radio" name="genre" value="M" class="mr-2 text-clubmed focus:ring-clubmed" {{ old('genre') == 'M' ? 'checked' : '' }}>
                        <span class="font-bold text-sm">MONSIEUR</span>
                    </label>
                    <label class="flex items-center cursor-pointer border px-4 py-3 rounded w-1/2 hover:bg-gray-50 {{ old('genre') == 'F' ? 'border-clubmed bg-blue-50' : 'border-gray-300' }} @error('genre') border-red-500 @enderror">
                        <input type="radio" name="genre" value="F" class="mr-2 text-clubmed focus:ring-clubmed" {{ old('genre') == 'F' ? 'checked' : '' }}>
                        <span class="font-bold text-sm">MADAME</span>
                    </label>
                </div>
                @error('genre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Prénom* <span class="text-gray-400 font-normal">Identique au passeport/CNI</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('prenom') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nom* <span class="text-gray-400 font-normal">Identique au passeport/CNI</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('nom') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 mb-1">Date de naissance*</label>
                <input type="date" name="datenaissance" value="{{ old('datenaissance') }}" 
                       class="w-full border p-2 text-sm outline-none text-gray-600 focus:ring-1 focus:ring-clubmed {{ $errors->has('datenaissance') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">E-mail*</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Téléphone portable*</label>
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('telephone') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" placeholder="0612345678" maxlength="10">
                </div>
            </div>

            <hr class="border-gray-200 my-6">

            <div class="grid grid-cols-3 gap-6 mb-4">
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Numéro*</label>
                    <input type="number" name="numrue" value="{{ old('numrue') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('numrue') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nom de rue*</label>
                    <input type="text" name="nomrue" value="{{ old('nomrue') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('nomrue') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Code postal*</label>
                    <input type="text" name="codepostal" value="{{ old('codepostal') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('codepostal') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" maxlength="6">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Ville*</label>
                    <input type="text" name="ville" value="{{ old('ville') }}" 
                           class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('ville') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}">
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded mb-6 border border-blue-100">
                <h3 class="text-sm font-bold mb-3 text-clubmed">Sécurité du compte</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Mot de passe*</label>
                        <input type="password" name="password" 
                               class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Confirmer mot de passe*</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full border p-2 text-sm outline-none focus:ring-1 focus:ring-clubmed {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300 focus:border-clubmed' }}" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-clubmed text-white p-3 rounded font-bold uppercase tracking-wide hover:bg-opacity-90 transition shadow-lg mt-2">
                Créer mon compte
            </button>
        </form>
    </div>
</div>

</body>
</html>