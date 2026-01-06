<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Resorts | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-clubmed-beige font-sans">
    @include('layouts.header')

    <main id="main" role="main" tabindex="-1">
        
        {{-- SECTION HERO --}}
        <section class="relative isolate px-4 py-16 lg:px-8 xl:px-16 bg-white">
            <div class="mx-auto max-w-7xl">
                <p class="text-clubmed-gold font-semibold text-sm uppercase tracking-widest mb-3">Découvrez nos destinations</p>
                <h1 class="font-serif text-4xl lg:text-6xl font-bold mb-4 text-clubmed-blue">Nos Resorts</h1>
                <p class="text-lg text-gray-600 max-w-2xl">Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.</p>
            </div>
        </section>

        {{-- SECTION FILTRES --}}
        <section class="px-4 lg:px-8 xl:px-16 py-8 bg-clubmed-beige">
            <div class="mx-auto max-w-7xl">
                <form action="{{ url('/resorts') }}" method="GET" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:p-8">
                    
                    {{-- GRILLE DES FILTRES (Adaptée pour 5 éléments) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
                        
                        {{-- 1. COLLECTION --}}
                        <div class="relative">
                            <label for="regroupement" class="block text-xs font-bold text-clubmed-blue uppercase tracking-wide mb-2">Collection</label>
                            <select name="regroupement" id="regroupement" class="w-full h-12 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold cursor-pointer bg-white transition-all">
                                <option value="">Toutes collections</option>
                                @foreach($regroupementsList ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('regroupement') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. DESTINATION (Localisation) --}}
                        <div class="relative">
                            <label for="localisation" class="block text-xs font-bold text-clubmed-blue uppercase tracking-wide mb-2">Destination</label>
                            <select name="localisation" id="localisation" class="w-full h-12 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold cursor-pointer bg-white transition-all">
                                <option value="">Toutes destinations</option>
                                @foreach($localisations ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('localisation') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. PAYS (Nouveau filtre) --}}
                        <div class="relative">
                            <label for="pays" class="block text-xs font-bold text-clubmed-blue uppercase tracking-wide mb-2">Pays</label>
                            <select name="pays" id="pays" class="w-full h-12 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold cursor-pointer bg-white transition-all">
                                <option value="">Tous les pays</option>
                                @foreach($paysList ?? [] as $code => $nom)
                                    <option value="{{ $code }}" {{ request('pays') == $code ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. ACTIVITÉ --}}
                        <div class="relative">
                            <label for="activite" class="block text-xs font-bold text-clubmed-blue uppercase tracking-wide mb-2">Activité</label>
                            <select name="activite" id="activite" class="w-full h-12 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold cursor-pointer bg-white transition-all">
                                <option value="">Toutes activités</option>
                                @foreach($activitesList ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('activite') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 5. TYPE DE SÉJOUR --}}
                        <div class="relative">
                            <label for="typeclub" class="block text-xs font-bold text-clubmed-blue uppercase tracking-wide mb-2">Type de séjour</label>
                            <select name="typeclub" id="typeclub" class="w-full h-12 px-4 py-2 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold cursor-pointer bg-white transition-all">
                                <option value="">Tous les types</option>
                                @foreach($typeclubs ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('typeclub') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    {{-- SECTION TRI ET BOUTONS --}}
                    <div class="flex flex-col md:flex-row justify-between items-center pt-6 border-t border-gray-100 gap-4">
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <label for="tri" class="text-sm font-semibold text-gray-600 whitespace-nowrap">Trier par :</label>
                            <select name="tri" id="tri" class="h-10 pl-4 pr-8 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-clubmed-gold cursor-pointer bg-clubmed-beige transition-all">
                                <option value="nom" {{ request('tri') == 'nom' ? 'selected' : '' }}>Nom (A-Z)</option>
                                <option value="prix_asc" {{ request('tri') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="prix_desc" {{ request('tri') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            </select>
                        </div>
                        <div class="flex gap-3 w-full md:w-auto justify-end">
                            @if(request()->anyFilled(['typeclub', 'localisation', 'pays', 'activite', 'regroupement', 'tri']))
                                <a href="{{ url('/resorts') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full font-semibold text-sm transition-all">Réinitialiser</a>
                            @endif
                            <button type="submit" class="px-8 py-3 bg-clubmed-gold hover:bg-yellow-400 text-clubmed-blue rounded-full font-bold text-sm transition-all shadow-md hover:shadow-lg">Rechercher</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        @if(request()->anyFilled(['typeclub', 'localisation', 'pays', 'activite', 'regroupement']))
            <section class="px-4 lg:px-8 xl:px-16 pb-6">
                <div class="mx-auto max-w-7xl">
                    <p class="text-sm text-gray-600 bg-white inline-block px-5 py-2.5 rounded-full shadow-sm border border-gray-100">
                        <span class="font-bold text-clubmed-blue">{{ $resorts->total() }}</span> resort(s) trouvé(s).
                    </p>
                </div>
            </section>
        @endif

        {{-- LISTE DES RESORTS --}}
        <section class="px-4 lg:px-8 xl:px-16 py-12 bg-clubmed-beige">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($resorts as $resort)
                        <article class="group relative isolate flex flex-col h-full bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                            
                            @php
                                $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                                $imagePath = 'img/ressort/' . $imageName;
                                $fullPath = public_path($imagePath);
                            @endphp

                            <div class="relative h-64 w-full overflow-hidden bg-clubmed-beige">
                                @if(file_exists($fullPath))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $resort->nomresort }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent"></div>
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-clubmed-beige to-gray-200 flex flex-col items-center justify-center text-gray-400 p-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs text-center font-medium">Image indisponible</span>
                                    </div>
                                @endif

                                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs font-bold text-clubmed-blue shadow-sm flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $resort->codepays }}
                                </div>
                            </div>

                            <div class="relative p-6 flex flex-col flex-grow">
                                <a href="/ficheresort/{{ $resort->numresort }}" class="block mb-4">
                                    {{-- TITRE À GAUCHE - TRIDENTS À DROITE --}}
                                    <div class="flex justify-between items-start gap-2">
                                        <h3 class="font-serif text-2xl font-bold text-clubmed-blue group-hover:text-clubmed-blue-dark transition-colors leading-tight">
                                            {{ $resort->nomresort }}
                                        </h3>
                                        <span class="font-bold text-xl text-clubmed-gold whitespace-nowrap shrink-0">
                                            {{ $resort->nbtridents }} 🔱
                                        </span>
                                    </div>
                                </a>
                                
                                {{-- GRILLE 3 COLONNES AVEC PRIX DYNAMIQUE --}}
                                <div class="grid grid-cols-3 divide-x divide-gray-100 border-y border-gray-100 py-4 mb-6">
                                    {{-- 1. AVIS --}}
                                    <div class="flex flex-col items-center justify-center px-1">
                                        <span class="text-gray-400 text-[10px] uppercase font-bold tracking-wider mb-1">Avis</span>
                                        <div class="flex items-center gap-1">
                                            <span class="font-bold text-clubmed-blue text-lg leading-none">{{ $resort->moyenneavis ?? '-' }}</span>
                                            <span class="text-clubmed-gold text-sm">★</span>
                                        </div>
                                    </div>
                                    
                                    {{-- 2. CHAMBRES --}}
                                    <div class="flex flex-col items-center justify-center px-1">
                                        <span class="text-gray-400 text-[10px] uppercase font-bold tracking-wider mb-1">Chambres</span>
                                        <div class="flex items-center">
                                            <span class="font-bold text-clubmed-blue text-lg leading-none">{{ $resort->nbchambrestotal }}</span>
                                        </div>
                                    </div>

                                    {{-- 3. PRIX DYNAMIQUE --}}
                                    <div class="flex flex-col items-center justify-center px-1">
                                        <span class="text-gray-400 text-[10px] uppercase font-bold tracking-wider mb-1">Prix</span>
                                        <div class="flex flex-col items-center">
                                            @if(isset($resort->min_price) && $resort->min_price > 0)
                                                <span class="font-bold text-clubmed-blue text-lg leading-none">
                                                    {{ number_format($resort->min_price, 0, ',', ' ') }} €
                                                </span>
                                                <span class="text-[10px] text-gray-500 font-medium">/ par nuit</span>
                                            @else
                                                <span class="font-bold text-clubmed-blue text-sm leading-none">Sur devis</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    @if($resort->descriptionresort)
                                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-5">
                                            {{ $resort->descriptionresort }}
                                        </p>
                                    @endif
                                    
                                    <a href="/ficheresort/{{ $resort->numresort }}" class="flex items-center justify-center w-full px-6 py-3.5 bg-clubmed-blue hover:bg-clubmed-blue-dark text-white rounded-full font-bold text-sm uppercase tracking-wide transition-all shadow-md hover:shadow-lg group-hover:bg-clubmed-gold group-hover:text-clubmed-blue">
                                        Découvrir le Resort
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(method_exists($resorts, 'links'))
                    <div class="mt-12 flex justify-center">
                        {{ $resorts->links() }}
                    </div>
                @endif

                @if(count($resorts) === 0)
                    <div class="text-center py-20 bg-white rounded-3xl shadow-sm mt-8">
                        <div class="mb-6 text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-serif font-bold text-clubmed-blue mb-3">Aucun resort trouvé</h3>
                        <p class="text-gray-500 text-base mb-8 max-w-md mx-auto">Nous n'avons trouvé aucun résultat correspondant à vos critères de recherche.</p>
                        <a href="{{ url('/resorts') }}" class="inline-flex items-center px-8 py-3.5 bg-clubmed-gold hover:bg-yellow-400 text-clubmed-blue rounded-full font-bold text-sm transition-all shadow-md hover:shadow-lg">
                            Voir tous les resorts
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>