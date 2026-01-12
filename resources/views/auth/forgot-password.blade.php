<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-clubmed-beige font-sans">

<div class="min-h-screen flex flex-col justify-center items-center px-4 py-12">
    {{-- Logo --}}
    <a href="/" class="mb-8">
        <img src="/img/logo-clubmed.png" alt="Club Med" class="h-16">
    </a>

    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl w-full max-w-md">
        
        <div class="text-center mb-6">
            <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Réinitialisation</h2>
            <p class="text-gray-500 mt-3 text-sm leading-relaxed">
                Mot de passe oublié ? Pas de souci. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien pour en choisir un nouveau.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 text-green-700 text-sm rounded-xl border border-green-100 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-700 text-sm rounded-xl border border-red-100">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-clubmed-gold focus:ring-2 focus:ring-clubmed-gold/20 outline-none transition-all" 
                       placeholder="votre@email.com"
                       required autofocus autocomplete="username">
            </div>

            <button type="submit" class="w-full bg-clubmed-blue text-white py-3.5 rounded-full font-bold text-sm uppercase tracking-wide hover:bg-clubmed-blue-dark transition-all shadow-lg hover:shadow-xl">
                Envoyer le lien
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-sm font-medium text-clubmed-blue hover:text-clubmed-blue-dark transition-colors">
                ← Retour à la connexion
            </a>
        </div>
    </div>
</div>

{{-- Chatbot BotMan --}}
@include('layouts.chatbot')
</body>
</html>