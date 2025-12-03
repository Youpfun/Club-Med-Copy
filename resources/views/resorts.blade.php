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
    @include('layouts.header')

    <main id="main" role="main" tabindex="-1">
        <section class="relative isolate px-4 py-12 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl">
                <h1 class="font-serif text-4xl lg:text-5xl font-bold mb-4">Nos Resorts</h1>
                <p class="text-base text-gray-700 max-w-xl">Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.</p>
            </div>
        </section>

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

        @if(request('typeclub') || request('localisation') || request('pays'))
            <section class="px-4 lg:px-8 xl:px-16 mb-6">
                <div class="mx-auto max-w-7xl">
                    <p class="text-base text-gray-700">
                        <span class="font-bold text-blue-600">{{ count($resorts) }}</span> resort(s) trouvé(s)
                    </p>
                </div>
            </section>
        @endif

        <section class="px-4 lg:px-8 xl:px-16 mb-20">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @foreach($resorts as $resort)
                        <article class="group relative isolate flex flex-col overflow-hidden rounded-lg bg-white shadow-sm hover:shadow-xl transition-shadow duration-300 outline-none h-full">
                            
                            @php
                                // Correspondance nom resort -> fichier image (en minuscules sans espaces)
                                // Exemple : "Alpe d'Huez" devient "alped'huez.webp"
                                $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                                // Note: dossier 'img/ressort' avec deux 's' comme dans ton exemple
                                $imagePath = 'img/ressort/' . $imageName;
                                $fullPath = public_path($imagePath);
                            @endphp

                            <div class="relative h-56 w-full overflow-hidden bg-gray-200">
                                @if(file_exists($fullPath))
                                    <img 
                                        src="{{ asset($imagePath) }}" 
                                        alt="Club Med {{ $resort->nomresort }}" 
                                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    >
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"></div>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full w-full bg-gray-100 text-gray-400 p-4 text-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs">Image indisponible</span>
                                        <span class="text-[10px] mt-1 text-gray-300">{{ $imageName }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="relative p-5 sm:p-6 flex flex-col flex-grow">
                                <a href="/ficheresort/{{ $resort->numresort }}" class="flex flex-col justify-center">
                                    <h3 class="font-serif text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                                        {{ $resort->nomresort }}
                                    </h3>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $resort->codepays }}
                                    </p>
                                </a>
                                
                                <div class="relative space-y-2 text-sm mb-4">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Catégorie</span>
                                        <div class="text-yellow-500 text-xs">
                                            @for($i = 0; $i < $resort->nbtridents; $i++)
                                                <span class="text-lg">🔱</span>
                                            @endfor
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-gray-600">Note moyenne</span>
                                        <span class="font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded text-xs">
                                            {{ $resort->moyenneavis }} / 5 ⭐
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-gray-600">Chambres</span>
                                        <span class="font-medium text-gray-900">{{ $resort->nbchambrestotal }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto pt-4">
                                    @if($resort->descriptionresort)
                                        <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-4">
                                            {{ $resort->descriptionresort }}
                                        </p>
                                    @endif
                                    
                                    <a href="/ficheresort/{{ $resort->numresort }}" class="flex items-center justify-center w-full px-6 py-3 bg-blue-900 hover:bg-blue-800 text-white rounded-full font-bold text-sm transition-all shadow-md hover:shadow-lg">
                                        Découvrir le Resort
                                    </a>
                                </div>
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

    <footer class="bg-white py-8 border-t border-gray-200" role="contentinfo">
        <div class="px-4 lg:px-8 xl:px-16">
            <div class="mx-auto max-w-7xl text-center">
                <p class="text-sm text-gray-600">&copy; {{ date('Y') }} Club Med - Tous droits réservés</p>
            </div>
        </div>
    </footer>
</body>
</html>