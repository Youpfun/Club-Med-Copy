<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
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
        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inscription.store') }}" method="POST">
        @csrf

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
            <input type="date" name="datenaissance" value="{{ old('datenaissance') }}" class="w-full border p-2 rounded text-sm text-gray-600 outline-none focus:border-clubmed focus:ring-1 focus:ring-clubmed" required>
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

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div class="col-span-1">
                <label class="block text-xs font-bold mb-1">N°</label>
                <input type="number" name="numrue" value="{{ old('numrue') }}" class="w-full border p-2 rounded text-sm">
            </div>
            <div class="col-span-3">
                <label class="block text-xs font-bold mb-1">Rue</label>
                <input type="text" name="nomrue" value="{{ old('nomrue') }}" class="w-full border p-2 rounded text-sm">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold mb-1">Code postal</label>
                <input type="text" name="codepostal" value="{{ old('codepostal') }}" class="w-full border p-2 rounded text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">Ville</label>
                <input type="text" name="ville" value="{{ old('ville') }}" class="w-full border p-2 rounded text-sm">
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
                    <input type="password" name="password" class="w-full border p-2 rounded text-sm" placeholder="••••••••" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Confirmer mot de passe*</label>
                    <input type="password" name="password_confirmation" class="w-full border p-2 rounded text-sm" placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-clubmed text-white py-4 rounded font-bold uppercase tracking-wide hover:bg-opacity-90 transition shadow-lg text-base">
            CRÉER MON COMPTE
        </button>
    </form>
</div>

</body>
</html>