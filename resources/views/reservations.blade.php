<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations | Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-[#113559] mb-6">Mes réservations</h1>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
            <p class="text-gray-600 mb-4">
                Vous n'avez pas encore de réservations finalisées.
            </p>
            <p class="text-sm text-gray-500 mb-4">
                Ajoutez des resorts à votre panier depuis leur fiche, puis finalisez votre réservation ultérieurement.
            </p>
            <a href="{{ route('cart.index') }}"
               class="inline-flex items-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-full font-bold text-sm transition-colors shadow-md">
                Voir mon panier
            </a>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>


