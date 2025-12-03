<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Resorts | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
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
    @include('layouts.header')

    <main id="main" role="main" tabindex="-1">
        <section class="relative isolate px-4 py-12 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <h1 class="font-serif text-4xl lg:text-5xl font-bold mb-4 text-[#113559]">Nos Resorts</h1>
                <p class="text-base text-gray-700 max-w-xl">Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.</p>
            </div>
        </section>

        <section class="px-4 lg:px-8 xl:px-16 mb-12">
            <div class="mx-auto max-w-7xl">
                <form action="{{ url('/resorts') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                        <div class="relative">
                            <label for="typeclub" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Type de club</label>
                            <select name="typeclub" id="typeclub" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-[#113559] focus:ring-1 focus:ring-[#113559] transition-colors cursor-pointer bg-white">
                                <option value="">Tous les types</option>
                                @foreach($typeclubs ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('typeclub') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="relative">
                            <label for="localisation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Localisation</label>
                            <select name="localisation" id="localisation" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-[#113559] focus:ring-1 focus:ring-[#113559] transition-colors cursor-pointer bg-white">
                                <option value="">Toutes les localisations</option>
                                @foreach($localisations ?? [] as $id => $nom)
                                    <option value="{{ $id }}" {{ request('localisation') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="relative">
                            <label for="pays" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Pays</label>
                            <select name="pays" id="pays" class="w-full h-12 px-4 py-2 border border-gray-300 rounded-full text-sm font-semibold text-gray-900 focus:outline-none focus:border-[#113559] focus:ring-1 focus:ring-[#113559] transition-colors cursor-pointer bg-white">
                                <option value="">Tous les pays</option>
                                @foreach($paysList ?? [] as $code => $nom)
                                    <option value="{{ $code }}" {{ request('pays') == $code ? 'selected' : '' }}>{{ $nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-2 border-t border-gray-100 mt-4">
                        <button type="submit" class="px-8 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-full font-bold text-sm transition-colors shadow-md">
                            Rechercher
                        </button>
                        @if(request('typeclub') || request('localisation') || request('pays'))
                            <a href="{{ url('/resorts') }}" class="px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full font-bold text-sm transition-colors">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        @if(request('typeclub') || request('localisation') || request('pays'))
            <section class="px-4 lg:px-8 xl:px-16 mb-6">
                <div class="mx-auto max-w-7xl">
                    <p class="text-sm text-gray-600 bg-blue-50 inline-block px-4 py-2 rounded-full">
                        <span class="font-bold text-[#113559]">{{ count($resorts) }}</span> resort(s) trouvé(s) correspondant à votre recherche.
                    </p>
                </div>
            </section>
        @endif

        <section class="px-4 lg:px-8 xl:px-16 mb-20">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($resorts as $resort)
                        <article class="group relative isolate flex flex-col h-full bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                            
                            @php
                                $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                                $imagePath = 'img/ressort/' . $imageName;
                                $fullPath = public_path($imagePath);
                            @endphp

                            <div class="relative h-64 w-full overflow-hidden bg-gray-200">
                                @if(file_exists($fullPath))
                                    <img src="{{ asset($imagePath) }}" 
                                         alt="{{ $resort->nomresort }}" 
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-slate-100 to-slate-200 flex flex-col items-center justify-center text-slate-400 p-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs text-center font-medium">Image indisponible</span>
                                        <span class="text-[10px] text-slate-300 mt-1">{{ $imageName }}</span>
                                    </div>
                                @endif

                                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-[#113559] shadow-sm flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $resort->codepays }}
                                </div>
                            </div>

                            <div class="relative p-6 flex flex-col flex-grow">
                                <a href="/ficheresort/{{ $resort->numresort }}" class="flex flex-col mb-2">
                                    <h3 class="font-serif text-2xl font-bold text-[#113559] mb-1 group-hover:text-blue-600 transition-colors">
                                        {{ $resort->nomresort }}
                                    </h3>
                                    
                                    {{-- MODIFICATION ICI : Chiffre + 1 Trident --}}
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Catégorie</span>
                                        <div class="flex items-center gap-1">
                                            {{-- Le chiffre en gros --}}
                                            <span class="font-bold text-xl text-[#113559]">{{ $resort->nbtridents }} 🔱</span>
                                        </div>
                                    </div>
                                </a>
                                
                                <div class="grid grid-cols-2 gap-4 text-sm mb-6 border-y border-gray-100 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-400 text-xs uppercase font-semibold">Avis Clients</span>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="font-bold text-gray-900 text-lg">{{ $resort->moyenneavis }}</span>
                                            <span class="text-yellow-400 text-lg">★</span>
                                            <span class="text-gray-400 text-xs">/ 5</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col border-l border-gray-100 pl-4">
                                        <span class="text-gray-400 text-xs uppercase font-semibold">Chambres</span>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="font-bold text-gray-900 text-lg">{{ $resort->nbchambrestotal }}</span>
                                            <span class="text-xs text-gray-500">disp.</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    @if($resort->descriptionresort)
                                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-5">
                                            {{ $resort->descriptionresort }}
                                        </p>
                                    @endif
                                    
                                    <a href="/ficheresort/{{ $resort->numresort }}" class="flex items-center justify-center w-full px-6 py-3 bg-[#113559] hover:bg-[#0e2a47] text-white rounded-full font-bold text-sm uppercase tracking-wide transition-all shadow-md hover:shadow-lg group-hover:bg-[#ffc000] group-hover:text-[#113559]">
                                        Découvrir le Resort
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(count($resorts) === 0)
                    <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="mb-4 text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-[#113559] mb-2">Aucun resort trouvé</h3>
                        <p class="text-gray-500 text-base mb-6 max-w-md mx-auto">Nous n'avons trouvé aucun résultat correspondant à vos critères de recherche.</p>
                        <a href="{{ url('/resorts') }}" class="inline-flex items-center px-8 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-full font-bold text-sm transition-colors shadow-md">
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