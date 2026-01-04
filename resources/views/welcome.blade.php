<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Séjours tout compris ou voyage all-inclusive | Club Med</title>
    <meta name="description" content="Trouvez la destination de vos rêves pour vos prochaines vacances parmi près de 80 Resorts Club Med en Europe, Asie, Amérique ou dans les Caraïbes.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <style>
        :root {
            --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-family-serif: 'Newsreader', Georgia, serif;
        }
        body { font-family: var(--font-family-sans); }
        .font-serif { font-family: var(--font-family-serif); }
    </style>
</head>
<body class="bg-white">
    @include('layouts.header')


    <main id="main" role="main" tabindex="-1">
        <section class="relative isolate overflow-hidden bg-gradient-to-b from-blue-50 to-white">
            <div class="px-4 lg:px-8 xl:px-16 py-16 lg:py-20">
                <div class="mx-auto max-w-7xl">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="space-y-6">
                            <h1 class="font-serif text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                                That's L'Esprit Libre
                            </h1>
                            <p class="text-base lg:text-lg text-gray-700 leading-relaxed">
                                Club Med, pionnier des vacances tout inclus, réinvente l'art de l'évasion pour faire de chaque instant une promesse de liberté, de partage et d'émotion. Vivez vos vacances à votre rythme, sans compromis et avec l'Esprit Libre.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                                <a href="{{ url('/resorts') }}" class="inline-flex items-center justify-center px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-base transition-colors">
                                    Découvrir nos Resorts
                                </a>
                                @auth
                                    @if(Auth::user() && strpos(strtolower(Auth::user()->role ?? ''), 'vente') !== false)
                                        <a href="{{ route('vente.dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-semibold text-base transition-colors">
                                            📊 Tableau de Bord Vente
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                        <div class="relative aspect-square rounded-lg overflow-hidden bg-gray-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                <span class="text-white text-6xl font-serif">🏖️</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 lg:px-8 xl:px-16 py-12 lg:py-16 bg-white">
            <div class="mx-auto max-w-7xl">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 lg:p-8">
                    <h2 class="font-serif text-2xl lg:text-3xl font-bold text-gray-900 mb-6 text-center">
                        Trouvez vos vacances idéales
                    </h2>
                    <form action="{{ url('/resorts') }}" method="GET" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="localisation" class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
                                <select name="localisation" id="localisation" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                    <option value="">Où souhaitez-vous partir ?</option>
                                </select>
                            </div>
                            <div>
                                <label for="typeclub" class="block text-sm font-medium text-gray-700 mb-2">Type de club</label>
                                <select name="typeclub" id="typeclub" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                    <option value="">Type de séjour</option>
                                </select>
                            </div>
                            <div>
                                <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                                <select name="pays" id="pays" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                    <option value="">Tous les pays</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-center">
                            <button type="submit" class="px-10 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-base transition-colors">
                                Voir tous les produits
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="px-4 lg:px-8 xl:px-16 py-12 lg:py-20 bg-gray-50">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12">
                    <h2 class="font-serif text-3xl lg:text-4xl font-bold text-gray-900 mb-12">
                        Plus de 80 <span class="text-blue-600">destinations de rêve</span>
                    </h2>
                    <p class="text-base text-gray-700 max-w-xl">
                        Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article class="group relative isolate overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="aspect-horizontal bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                            <span class="text-white text-5xl">🏝️</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-serif text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                Europe & Méditerranée
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Découvrez nos resorts en Europe et Méditerranée
                            </p>
                            <a href="{{ url('/resorts') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700">
                                Découvrir →
                            </a>
                        </div>
                    </article>

                    <article class="group relative isolate overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="aspect-horizontal bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                            <span class="text-white text-5xl">🏔️</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-serif text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                Alpes
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Nos resorts de ski dans les Alpes
                            </p>
                            <a href="{{ url('/resorts') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700">
                                Découvrir →
                            </a>
                        </div>
                    </article>

                    <article class="group relative isolate overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="aspect-horizontal bg-gradient-to-br from-teal-400 to-cyan-600 flex items-center justify-center">
                            <span class="text-white text-5xl">🌴</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-serif text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                Océan Indien
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Paradis tropicaux et plages de rêve
                            </p>
                            <a href="{{ url('/resorts') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700">
                                Découvrir →
                            </a>
                        </div>
                    </article>
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ url('/resorts') }}" class="inline-flex items-center justify-center px-8 py-3 border border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white rounded-full font-semibold text-sm transition-colors">
                        Voir plus
                    </a>
                </div>
            </div>
        </section>

        <section class="px-4 lg:px-8 xl:px-16 py-12 lg:py-20 bg-white">
            <div class="mx-auto max-w-7xl">
                <h2 class="font-serif text-3xl lg:text-4xl font-bold text-gray-900 mb-12 text-center">
                    Club Med, créateur du tout compris et du lâcher prise
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-4xl">🏖️</span>
                        </div>
                        <h3 class="font-serif text-xl font-semibold text-gray-900 mb-3">
                            60+ destinations mer & montagne d’exception
                        </h3>
                        <p class="text-sm text-gray-600">
                            Des destinations uniques partout dans le monde
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-yellow-100 flex items-center justify-center">
                            <span class="text-4xl">👨‍👩‍👧‍👦</span>
                        </div>
                        <h3 class="font-serif text-xl font-semibold text-gray-900 mb-3">
                            Bonheur & complicité en famille
                        </h3>
                        <p class="text-sm text-gray-600">
                            Des activités pour tous les âges
                        </p>
                    </div>

                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                            <span class="text-4xl">🎾</span>
                        </div>
                        <h3 class="font-serif text-xl font-semibold text-gray-900 mb-3">
                            Évasion sportive & bien-être
                        </h3>
                        <p class="text-sm text-gray-600">
                            Sports et spa pour se ressourcer
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 lg:px-8 xl:px-16 py-12 bg-gray-100">
            <div class="mx-auto max-w-7xl">
                <h3 class="font-serif text-xl font-semibold text-gray-700 mb-6 text-center">
                    Liens temporaires (admin)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ url('/resorts') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-semibold text-sm transition-colors">
                        Resorts
                    </a>
                    <a href="{{ url('/clients') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-semibold text-sm transition-colors">
                        Clients
                    </a>
                    <a href="{{ url('/typeclubs') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-semibold text-sm transition-colors">
                        TypeClubs
                    </a>
                    <a href="{{ url('/localisations') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg font-semibold text-sm transition-colors">
                        Localisations
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white py-12 border-t border-gray-200" role="contentinfo">
        <div class="px-4 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Club Med & vous</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="#" class="hover:text-gray-900">Inscription à la Newsletter</a></li>
                            <li><a href="#" class="hover:text-gray-900">Programme Great Members</a></li>
                            <li><a href="#" class="hover:text-gray-900">Assurance Voyage</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Nos inspirations</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Vacances en famille</a></li>
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Voyage de noces</a></li>
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Vacances au soleil</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Nos destinations</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Europe & Méditerranée</a></li>
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Alpes</a></li>
                            <li><a href="{{ url('/resorts') }}" class="hover:text-gray-900">Océan Indien</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Contact</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>0810 810 810</li>
                            <li>Du lundi au samedi : 9h-19h</li>
                            <li>Service 0,05€/min + prix appel</li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 border-t border-gray-200 text-center">
                    <p class="text-sm text-gray-600">&copy; {{ date('Y') }} Club Med - Tous droits réservés</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>