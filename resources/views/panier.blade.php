<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier | Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold text-[#113559] mb-6">Mon panier</h1>

        @if(empty($reservations))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-600 mb-4">
                    Votre panier est vide pour le moment.
                </p>
                <a href="{{ url('/resorts') }}"
                   class="inline-flex items-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-full font-bold text-sm transition-colors shadow-md">
                    Découvrir nos resorts
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                @foreach($reservations as $reservation)
                    <div class="flex items-center justify-between border-b last:border-0 border-gray-100 py-3">
                        <div>
                            <h2 class="text-lg font-semibold text-[#113559]">
                                {{ $reservation['nom'] }}
                            </h2>
                            <p class="text-sm text-gray-500">
                                {{ $reservation['pays'] ?? 'Pays non renseigné' }} •
                                {{ $reservation['nb_chambres'] }} chambres
                            </p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-100 rounded-full">
                            Réservation à finaliser
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    @include('layouts.footer')
</body>
</html>


