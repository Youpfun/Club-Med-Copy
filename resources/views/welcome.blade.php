<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Séjours tout compris ou voyage all-inclusive | Club Med</title>
    <meta name="description" content="Trouvez la destination de vos rêves pour vos prochaines vacances parmi près de 80 Resorts Club Med en Europe, Asie, Amérique ou dans les Caraïbes.">
    
    {{-- Fonts Club Med --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;0,6..72,700;1,6..72,400;1,6..72,500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
</head>
<body class="bg-clubmed-beige font-sans antialiased">
    @include('layouts.header')

    <main id="main" role="main" tabindex="-1">
        
        {{-- ===== HERO SECTION ===== --}}
        <section class="relative bg-clubmed-beige">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 py-12 lg:py-20">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                    
                    {{-- Texte Hero --}}
                    <div class="space-y-6 lg:space-y-8">
                        <div>
                            <p class="text-clubmed-gold font-serif italic text-lg lg:text-xl mb-2">That's</p>
                            <h1 class="font-serif text-5xl lg:text-7xl font-bold text-black leading-tight">
                                L'Esprit Libre
                            </h1>
                        </div>
                        
                        <p class="text-gray-700 text-lg lg:text-xl leading-relaxed max-w-xl">
                            Club Med, pionnier des vacances tout inclus, réinvente l'art de l'évasion pour faire de chaque instant une promesse de <strong>liberté</strong>, de <strong>partage</strong> et d'<strong>émotion</strong>.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ url('/resorts') }}" class="inline-flex items-center justify-center px-8 py-4 bg-clubmed-gold hover:bg-yellow-400 text-black rounded-full font-semibold text-base transition-all hover:shadow-lg">
                                Découvrir nos destinations
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    {{-- Image Hero --}}
                    <div class="relative">
                        <div class="aspect-[4/5] lg:aspect-square rounded-3xl overflow-hidden shadow-2xl">
                            @php
                                $heroImages = ['puntacana.webp', 'seychelles.webp', 'marrakech.webp'];
                                $heroImage = $heroImages[array_rand($heroImages)];
                                $heroPath = public_path('img/ressort/' . $heroImage);
                            @endphp
                            @if(file_exists($heroPath))
                                <img src="{{ asset('img/ressort/' . $heroImage) }}" alt="Club Med Resort" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-clubmed-blue to-clubmed-blue-dark flex items-center justify-center">
                                    <span class="text-white text-8xl">🏝️</span>
                                </div>
                            @endif
                        </div>
                        {{-- Badge flottant --}}
                        <div class="absolute -bottom-4 -left-4 lg:-bottom-6 lg:-left-6 bg-white rounded-2xl shadow-xl p-4 lg:p-6">
                            <p class="text-3xl lg:text-4xl font-serif font-bold text-black">80+</p>
                            <p class="text-sm text-gray-600">destinations de rêve</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== BARRE DE RECHERCHE DYNAMIQUE ===== --}}
        <section class="bg-white py-8 lg:py-12 border-y border-gray-200">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="text-center mb-6">
                    <h2 class="font-serif text-2xl lg:text-3xl font-bold text-black mb-2">Trouvez vos vacances idéales</h2>
                    <p class="text-gray-600">Explorez nos destinations et trouvez le resort parfait</p>
                </div>
                
                {{-- Composant de barre de recherche dynamique --}}
                <x-search-bar :resorts="$searchResorts ?? collect()" :localisations="$localisations ?? collect()" />
            </div>
        </section>

        {{-- ===== DESTINATIONS POPULAIRES ===== --}}
        <section class="bg-clubmed-beige py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12">
                    <div>
                        <h2 class="font-serif text-3xl lg:text-5xl font-bold text-black mb-4">
                            Plus de 80<br><span class="text-clubmed-blue">destinations de rêve</span>
                        </h2>
                        <p class="text-gray-600 text-lg max-w-xl">
                            Soleil ou neige, près ou loin, vous trouverez certainement le Resort de vos rêves pour vos prochaines vacances.
                        </p>
                    </div>
                    <div class="mt-6 lg:mt-0 flex gap-3">
                        <a href="{{ url('/resorts') }}" class="px-6 py-3 bg-white border-2 border-black text-black hover:bg-black hover:text-white rounded-full font-semibold text-sm transition-all">
                            Tous les Resorts
                        </a>
                    </div>
                </div>

                {{-- Affichage des resorts dynamiquement depuis la base de données --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @if(isset($featuredResorts) && $featuredResorts->count() > 0)
                        @foreach($featuredResorts->take(4) as $resort)
                            <article class="group">
                                <a href="{{ route('resort.show', $resort->numresort) }}" class="block">
                                    <div class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-4">
                                        @php
                                            $photo = $resort->photos->first();
                                            $hasImage = $photo && $photo->urlphoto;
                                        @endphp
                                        @if($hasImage)
                                            <img src="{{ asset('img/ressort/' . $photo->urlphoto) }}" alt="{{ $resort->nomresort }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-clubmed-blue to-clubmed-blue-dark flex items-center justify-center">
                                                <span class="text-white text-5xl">🏝️</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                        @if($resort->nbtridents >= 4)
                                            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full">
                                                <span class="text-xs font-semibold text-black">
                                                    @if($resort->nbtridents == 5) Gamme Luxe @else Avec espace Luxe @endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="font-serif text-xl font-semibold text-black group-hover:text-clubmed-blue transition-colors">{{ $resort->nomresort }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $resort->pays->nompays ?? 'Destination Club Med' }}</p>
                                </a>
                            </article>
                        @endforeach
                    @else
                        {{-- Fallback si pas de données --}}
                        @php
                            $destinations = [
                                ['name' => 'Punta Cana', 'country' => 'République Dominicaine', 'image' => 'puntacana.webp', 'badge' => 'Avec espace Luxe'],
                                ['name' => 'Seychelles', 'country' => 'Les Seychelles', 'image' => 'seychelles.webp', 'badge' => 'Gamme Luxe'],
                                ['name' => 'Marrakech', 'country' => 'Maroc', 'image' => 'marrakech.webp', 'badge' => 'Avec espace Luxe'],
                                ['name' => 'La Caravelle', 'country' => 'Guadeloupe', 'image' => 'lacaravelle.webp', 'badge' => null],
                            ];
                        @endphp
                        @foreach($destinations as $dest)
                            <article class="group">
                                <a href="{{ url('/resorts') }}" class="block">
                                    <div class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-4">
                                        @php
                                            $imgPath = public_path('img/ressort/' . $dest['image']);
                                        @endphp
                                        @if(file_exists($imgPath))
                                            <img src="{{ asset('img/ressort/' . $dest['image']) }}" alt="{{ $dest['name'] }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-clubmed-blue to-clubmed-blue-dark flex items-center justify-center">
                                                <span class="text-white text-5xl">🏝️</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                        @if($dest['badge'])
                                            <div class="absolute top-4 left-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-full">
                                                <span class="text-xs font-semibold text-black">{{ $dest['badge'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="font-serif text-xl font-semibold text-black group-hover:text-clubmed-blue transition-colors">{{ $dest['name'] }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $dest['country'] }}</p>
                                </a>
                            </article>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        {{-- ===== AVANTAGES CLUB MED ===== --}}
        <section class="bg-white py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="font-serif text-3xl lg:text-5xl font-bold text-black mb-4">
                        Club Med, créateur du tout compris<br>et du lâcher prise
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                    @php
                        $avantages = [
                            ['icon' => '🌍', 'title' => '60+ destinations mer & montagne', 'subtitle' => "d'exception", 'color' => 'bg-blue-50'],
                            ['icon' => '👨‍👩‍👧‍👦', 'title' => 'Bonheur & complicité', 'subtitle' => 'en famille', 'color' => 'bg-yellow-50'],
                            ['icon' => '🎾', 'title' => 'Évasion sportive', 'subtitle' => '& bien-être', 'color' => 'bg-green-50'],
                            ['icon' => '🍽️', 'title' => 'Table gourmande', 'subtitle' => '& inspirée', 'color' => 'bg-orange-50'],
                            ['icon' => '💫', 'title' => 'Équipes passionnées', 'subtitle' => '& ambiance signature', 'color' => 'bg-purple-50'],
                            ['icon' => '🌱', 'title' => 'Engagé pour un tourisme', 'subtitle' => 'responsable', 'color' => 'bg-emerald-50'],
                        ];
                    @endphp

                    @foreach($avantages as $avantage)
                        <div class="group text-center p-8 rounded-2xl {{ $avantage['color'] }} hover:shadow-lg transition-all cursor-pointer">
                            <div class="text-5xl mb-6">{{ $avantage['icon'] }}</div>
                            <h3 class="font-serif text-xl font-semibold text-black">
                                {{ $avantage['title'] }}<br>
                                <span class="text-clubmed-blue">{{ $avantage['subtitle'] }}</span>
                            </h3>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== 4 BONNES RAISONS ===== --}}
        <section class="bg-clubmed-beige py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <h2 class="font-serif text-3xl lg:text-4xl font-bold text-black text-center mb-12">
                    4 bonnes raisons de réserver<br>vos vacances avec Club Med
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $raisons = [
                            ['icon' => '💳', 'title' => 'Paiement en plusieurs fois', 'subtitle' => 'À votre rythme'],
                            ['icon' => '✈️', 'title' => 'Votre confort assuré', 'subtitle' => 'Du départ à l\'arrivée'],
                            ['icon' => '🛡️', 'title' => 'Assurance Écran Total', 'subtitle' => 'Des garanties exclusives'],
                            ['icon' => '🔄', 'title' => 'Modification sans frais', 'subtitle' => 'Jusqu\'à J-14*'],
                        ];
                    @endphp

                    @foreach($raisons as $raison)
                        <div class="bg-white p-6 rounded-2xl hover:shadow-lg transition-all">
                            <div class="text-4xl mb-4">{{ $raison['icon'] }}</div>
                            <h3 class="font-semibold text-black text-lg mb-1">{{ $raison['title'] }}</h3>
                            <p class="text-gray-600 text-sm">{{ $raison['subtitle'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== TOURISME RESPONSABLE ===== --}}
        <section class="bg-emerald-900 py-16 lg:py-24">
            <div class="max-w-7xl mx-auto px-4 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6">
                        <h2 class="font-serif text-3xl lg:text-4xl font-bold text-white">
                            Quand la Responsabilité fait aussi partie du Tout Compris.
                        </h2>
                        <p class="text-emerald-100 text-lg leading-relaxed">
                            Le Club Med est profondément engagé dans le développement durable, en se concentrant sur la réduction des déchets, la préservation de la biodiversité et le soutien aux communautés locales.
                        </p>
                        <a href="#" class="inline-flex items-center text-white font-semibold hover:text-clubmed-gold transition-colors">
                            En savoir plus
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                    <div class="flex justify-center">
                        <div class="text-9xl">🌍</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== NEWSLETTER ===== --}}
        <section class="bg-clubmed-beige py-16 lg:py-24">
            <div class="max-w-3xl mx-auto px-4 lg:px-8 text-center">
                <h2 class="font-serif text-3xl lg:text-4xl font-bold text-black mb-4">
                    Restez inspiré
                </h2>
                <p class="text-gray-600 text-lg mb-8">
                    Inscrivez-vous à notre newsletter et recevez nos meilleures offres et inspirations voyage.
                </p>
                <form class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
                    <input type="email" placeholder="Votre adresse email" class="flex-1 h-14 px-6 bg-white border-0 rounded-full text-base focus:ring-2 focus:ring-clubmed-gold">
                    <button type="submit" class="h-14 px-8 bg-black hover:bg-gray-800 text-white rounded-full font-semibold text-base transition-all">
                        S'inscrire
                    </button>
                </form>
            </div>
        </section>

    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="bg-black text-white py-16" role="contentinfo">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <div>
                    <h4 class="font-serif text-xl font-bold mb-6">Club Med</h4>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Pionnier des vacances tout inclus depuis 1950, Club Med réinvente l'art de l'évasion.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-6">Club Med & vous</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Programme Great Members</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Assurance Voyage</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6">Nos destinations</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ url('/resorts') }}" class="hover:text-white transition-colors">Vacances au soleil</a></li>
                        <li><a href="{{ url('/resorts') }}" class="hover:text-white transition-colors">Ski & Montagne</a></li>
                        <li><a href="{{ url('/resorts') }}" class="hover:text-white transition-colors">Gamme Luxe</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6">Contact</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li>📞 0810 810 810</li>
                        <li>Du lundi au samedi : 9h-19h</li>
                        <li>Service 0,05€/min + prix appel</li>
                    </ul>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-800 text-center">
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Club Med - Tous droits réservés</p>
            </div>
        </div>
    </footer>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')

    {{-- Bandeau de consentement cookies RGPD --}}
    @include('components.cookie-banner')

    {{-- Didacticiel de bienvenue (uniquement pour les visiteurs non connectés) --}}
    @guest
    <div id="tutorialOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[10000]">
        {{-- Spotlight - zone éclairée autour de l'élément ciblé --}}
        <div id="tutorialSpotlight" class="absolute border-4 border-white rounded-lg shadow-2xl transition-all duration-300"></div>
    </div>

    <div id="tutorialBox" class="hidden fixed z-[10001] bg-white rounded-xl shadow-2xl p-6 max-w-sm">
        {{-- Contenu du tutoriel --}}
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span id="tutorialStep" class="text-xs font-semibold text-blue-600 uppercase">Étape 1/5</span>
                <button id="tutorialClose" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <h3 id="tutorialTitle" class="text-xl font-bold text-gray-900 mb-2">Bienvenue sur Club Med !</h3>
            <p id="tutorialText" class="text-gray-700 leading-relaxed"></p>
        </div>

        {{-- Boutons de navigation --}}
        <div class="flex gap-3">
            <button id="tutorialSkip" class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                Passer
            </button>
            <button id="tutorialNext" class="flex-1 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Suivant
            </button>
        </div>
    </div>

    <script>
        // Configuration du tutoriel
        const tutorialSteps = [
            {
                target: '.group button, button:has(svg path[d*="M16 7a4"])',
                title: 'Bienvenue sur Club Med !',
                text: 'Connectez-vous ou créez un compte pour profiter de toutes nos fonctionnalités : réservations, historique, compagnons de voyage...',
                position: 'bottom'
            },
            {
                target: '[href*="resorts"]',
                title: 'Découvrez nos Resorts',
                text: 'Explorez notre collection de resorts à travers le monde. Utilisez les filtres pour trouver votre destination idéale !',
                position: 'bottom'
            },
            {
                target: '.hero-cta, [href*="resorts"]',
                title: 'Recherchez votre séjour',
                text: 'Cliquez ici pour accéder à la page de recherche avec tous nos filtres : type de club, localisation, pays, activités...',
                position: 'bottom'
            },
            {
                target: '[href*="guide"]',
                title: 'Besoin d\'aide ?',
                text: 'Consultez notre guide utilisateur complet avec des captures d\'écran et des explications détaillées pour chaque fonctionnalité.',
                position: 'bottom'
            },
            {
                target: 'footer',
                title: 'Prêt à partir ?',
                text: 'Vous savez maintenant naviguer sur le site ! Explorez nos destinations et réservez votre prochain voyage en toute confiance.',
                position: 'top'
            }
        ];

        let currentStep = 0;

        // Vérifier si le tutoriel a déjà été vu
        function shouldShowTutorial() {
            return !localStorage.getItem('clubmed_tutorial_completed');
        }

        // Trouver l'élément ciblé
        function findTargetElement(selector) {
            const selectors = selector.split(',').map(s => s.trim());
            for (const sel of selectors) {
                const element = document.querySelector(sel);
                if (element) return element;
            }
            return null;
        }

        // Positionner le tutoriel
        function positionTutorial() {
            const step = tutorialSteps[currentStep];
            const targetElement = findTargetElement(step.target);
            
            if (!targetElement) {
                // Si l'élément n'existe pas, passer à l'étape suivante
                nextStep();
                return;
            }

            const overlay = document.getElementById('tutorialOverlay');
            const spotlight = document.getElementById('tutorialSpotlight');
            const box = document.getElementById('tutorialBox');

            // Obtenir la position de l'élément
            const rect = targetElement.getBoundingClientRect();
            
            // Positionner le spotlight
            spotlight.style.left = (rect.left - 8) + 'px';
            spotlight.style.top = (rect.top - 8) + 'px';
            spotlight.style.width = (rect.width + 16) + 'px';
            spotlight.style.height = (rect.height + 16) + 'px';

            // Positionner la box de tutoriel
            const boxRect = box.getBoundingClientRect();
            let boxLeft, boxTop;

            if (step.position === 'bottom') {
                boxLeft = rect.left + (rect.width / 2) - (boxRect.width / 2);
                boxTop = rect.bottom + 20;
            } else if (step.position === 'top') {
                boxLeft = rect.left + (rect.width / 2) - (boxRect.width / 2);
                boxTop = rect.top - boxRect.height - 20;
            }

            // S'assurer que la box reste dans la fenêtre
            boxLeft = Math.max(16, Math.min(boxLeft, window.innerWidth - boxRect.width - 16));
            boxTop = Math.max(16, Math.min(boxTop, window.innerHeight - boxRect.height - 16));

            box.style.left = boxLeft + 'px';
            box.style.top = boxTop + 'px';

            // Mettre à jour le contenu
            document.getElementById('tutorialStep').textContent = `Étape ${currentStep + 1}/${tutorialSteps.length}`;
            document.getElementById('tutorialTitle').textContent = step.title;
            document.getElementById('tutorialText').textContent = step.text;

            // Mettre à jour le bouton "Suivant"
            const nextBtn = document.getElementById('tutorialNext');
            if (currentStep === tutorialSteps.length - 1) {
                nextBtn.textContent = 'Terminer';
            } else {
                nextBtn.textContent = 'Suivant';
            }
        }

        // Afficher le tutoriel
        function showTutorial() {
            document.getElementById('tutorialOverlay').classList.remove('hidden');
            document.getElementById('tutorialBox').classList.remove('hidden');
            positionTutorial();
        }

        // Masquer le tutoriel
        function hideTutorial() {
            document.getElementById('tutorialOverlay').classList.add('hidden');
            document.getElementById('tutorialBox').classList.add('hidden');
            localStorage.setItem('clubmed_tutorial_completed', 'true');
        }

        // Étape suivante
        function nextStep() {
            currentStep++;
            if (currentStep >= tutorialSteps.length) {
                hideTutorial();
            } else {
                positionTutorial();
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            if (shouldShowTutorial()) {
                // Attendre un peu que la page soit complètement chargée
                setTimeout(showTutorial, 500);
            }

            // Événements
            document.getElementById('tutorialNext').addEventListener('click', nextStep);
            document.getElementById('tutorialSkip').addEventListener('click', hideTutorial);
            document.getElementById('tutorialClose').addEventListener('click', hideTutorial);

            // Repositionner lors du redimensionnement
            window.addEventListener('resize', () => {
                if (!document.getElementById('tutorialOverlay').classList.contains('hidden')) {
                    positionTutorial();
                }
            });

            // Repositionner lors du scroll
            window.addEventListener('scroll', () => {
                if (!document.getElementById('tutorialOverlay').classList.contains('hidden')) {
                    positionTutorial();
                }
            });
        });
    </script>
    @endguest
</body>
</html>
