<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Resorts | Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-family-serif: 'Newsreader', Georgia, serif;
        }
        body { font-family: var(--font-family-sans); }
        .font-serif { font-family: var(--font-family-serif); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="flex items-center justify-between gap-x-4 overflow-x-clip bg-white p-4 px-4 lg:px-8 relative isolate z-5 border-b border-gray-100" role="banner">
        <a href="{{ url('/') }}" class="w-32 md:w-40">
            <span class="sr-only">Club Med</span>
            <span class="text-2xl font-bold text-blue-700">Club Med</span>
        </a>
        <nav class="flex items-center gap-x-12 px-8">
            <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors">← Accueil</a>
            <a href="{{ url('/resorts') }}" class="text-sm font-semibold text-blue-600">Nos Resorts</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main id="main" role="main" tabindex="-1">
        <!-- Hero Section -->
        <section class="relative isolate px-4 py-12 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <h1 class="font-serif text-4xl lg:text-5xl font-bold mb-4">Nos Resorts</h1>
                <p class="text-base text-gray-700 max-w-xl">Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.</p>
            </div>
        </section>

        <!-- Filter Section -->
        <section class="px-4 lg:px-8 xl:px-16 mb-12">
            <div class="mx-auto max-w-7xl">
                <form action="{{ url('/resorts') }}" method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                        <div class="relative">
                            <label for="typeclub" class="block text-sm font-medium text-gray-700 mb-2">Type de club</label>
                            <select name="typeclub" id="typeclub" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                <option value="">Tous les types</option>
                                @foreach($typeclubs ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('typeclub') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="relative">
                            <label for="localisation" class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
                            <select name="localisation" id="localisation" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                <option value="">Toutes les localisations</option>
                                @foreach($localisations ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('localisation') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="relative">
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                            <select name="pays" id="pays" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-gray-900 transition-colors">
                                <option value="">Tous les pays</option>
                                @foreach($paysList ?? [] as $code => $nom)
                                    <option value="{{ $code }}" {{ request('pays') == $code ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-sm transition-colors">
                            Rechercher
                        </button>
                        @if(request('typeclub') || request('localisation') || request('pays'))
                            <a href="{{ url('/resorts') }}" class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-full font-semibold text-sm transition-colors">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        <!-- Results Count -->
        @if(request('typeclub') || request('localisation') || request('pays'))
            <section class="px-4 lg:px-8 xl:px-16 mb-6">
                <div class="mx-auto max-w-7xl">
                    <p class="text-base text-gray-700">
                        <span class="font-bold text-blue-600">{{ count($resorts) }}</span> resort(s) trouvé(s)
                    </p>
                </div>
            </section>
        @endif

        <!-- Resorts Grid -->
        <section class="px-4 lg:px-8 xl:px-16 mb-20">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($resorts as $resort)
                        <article class="group relative isolate overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-xl transition-shadow duration-300 outline-none">
                            <div class="pointer-events-none absolute inset-0 flex flex-col overflow-auto rounded-lg bg-gradient-to-b from-transparent via-transparent to-black/10"></div>
                            
                            <div class="relative p-5 sm:p-6">
                                <a href="/ficheresort/{{ $resort->numresort }}" class="flex flex-col justify-center before:absolute before:inset-0 before:block">
                                    <h3 class="font-serif text-xl font-semibold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                        {{ $resort->nomresort }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">{{ $resort->codepays }}</p>
                                </a>
                                
                                <div class="relative space-y-2 text-sm mb-4">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Catégorie</span>
                                        <span class="font-medium text-gray-900">{{ $resort->nbtridents }} 🔱</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                                        <span class="text-gray-600">Note moyenne</span>
                                        <span class="font-medium text-yellow-600">{{ $resort->moyenneavis }} / 5 ⭐</span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-4">
                                        <span class="text-gray-600">Chambres</span>
                                        <span class="font-medium text-gray-900">{{ $resort->nbchambrestotal }}</span>
                                    </div>
                                </div>
                                
                                @if($resort->descriptionresort)
                                    <p class="relative text-gray-600 text-sm leading-relaxed line-clamp-3 mb-5">
                                        {{ $resort->descriptionresort }}
                                    </p>
                                @endif
                                
                                <a href="/ficheresort/{{ $resort->numresort }}" class="relative flex items-center justify-center w-full px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-sm transition-colors">
                                    Voir les détails
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(count($resorts) === 0)
                    <div class="text-center py-12">
                        <p class="text-gray-600 text-lg">Aucun resort trouvé avec ces critères.</p>
                        <a href="{{ url('/resorts') }}" class="mt-6 inline-flex items-center px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-sm transition-colors">
                            Voir tous les resorts
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-8 border-t border-gray-200" role="contentinfo">
        <div class="px-4 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl text-center">
                <p class="text-sm text-gray-600">&copy; {{ date('Y') }} Club Med - Tous droits réservés</p>
            </div>
        </div>
    </footer>
</body>
</html>
