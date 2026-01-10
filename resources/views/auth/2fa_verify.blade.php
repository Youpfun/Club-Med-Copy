<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-clubmed-blue">

<div class="min-h-screen flex flex-col justify-center items-center py-10 px-4">
    
    <div class="bg-white p-8 rounded-sm shadow-lg w-full max-w-md border-t-4 border-clubmed-blue">
        
        <div class="flex justify-center mb-6">
           <h2 class="text-2xl font-bold">Vérification Requise</h2>
        </div>

        <p class="text-sm text-gray-500 text-center mb-6">
            Nous avons envoyé un code de vérification à votre adresse email. Veuillez le saisir ci-dessous pour finaliser votre inscription.
        </p>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('message'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm text-center">
                {{ session('message') }}
            </div>
        @endif

        <form action="{{ route('2fa.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-700 mb-2 text-center">CODE DE SÉCURITÉ</label>
                <input type="text" name="two_factor_code" 
                       class="w-full border p-3 text-center text-2xl tracking-[0.5em] font-bold outline-none focus:ring-1 focus:ring-clubmed-blue border-gray-300 rounded" 
                       placeholder="123456" maxlength="6" autofocus required>
            </div>

            <button type="submit" class="w-full bg-clubmed-blue text-white p-3 rounded font-bold uppercase tracking-wide hover:bg-opacity-90 transition shadow-lg">
                Confirmer
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500 mb-2">Vous n'avez pas reçu le code ?</p>
            <form action="{{ route('2fa.resend') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-bold text-clubmed-blue-light hover:underline">
                    Renvoyer le code
                </button>
            </form>
        </div>

    </div>
</div>

{{-- Chatbot BotMan --}}
@include('layouts.chatbot')
</body>
</html>