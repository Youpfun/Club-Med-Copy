<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Club Med</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clubmed: { DEFAULT: '#113559' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-clubmed">

<div class="min-h-screen flex flex-col justify-center items-center px-4">
    <div class="bg-white p-8 rounded-sm shadow-lg w-full max-w-md border-t-4 border-clubmed">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold uppercase tracking-wider">Espace Client</h2>
            <p class="text-sm text-gray-500 mt-2">Connectez-vous pour accéder à vos réservations</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm border-l-4 border-red-500">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">E-mail ou Login</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 border p-3 text-sm focus:border-clubmed focus:ring-1 focus:ring-clubmed outline-none" required autofocus>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Mot de passe</label>
                <input type="password" name="password" class="w-full border-gray-300 border p-3 text-sm focus:border-clubmed focus:ring-1 focus:ring-clubmed outline-none" required>
            </div>

            <button type="submit" class="w-full bg-clubmed text-white p-3 rounded font-bold uppercase tracking-wide hover:bg-opacity-90 transition shadow-lg">
                Se connecter
            </button>
        </form>

        <div class="mt-6 text-center border-t border-gray-100 pt-4">
            <p class="text-sm text-gray-600">Pas encore de compte ?</p>
            <a href="{{ route('inscription.create') }}" class="text-clubmed font-bold text-sm hover:underline mt-1 block">
                Créer un compte maintenant
            </a>
        </div>
        
        <div class="mt-4 text-center">
             <a href="/" class="text-xs text-gray-400 hover:text-gray-600">← Retour à l'accueil</a>
        </div>
    </div>
</div>

</body>
</html>