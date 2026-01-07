{{-- ===== HEADER CLUB MED - STYLE OFFICIEL ===== --}}
@php
    // Pour Ski : utiliser typeclub "Montagne" ou localisation "Les Alpes"
    $headerSkiTypeclub = \DB::table('typeclub')
        ->where('nomtypeclub', 'LIKE', '%Montagne%')
        ->first();
        
    $headerSkiLocalisation = \DB::table('localisation')
        ->where('nomlocalisation', 'LIKE', '%Alpes%')
        ->first();
        
    // Pour Soleil : utiliser typeclub "Mer & Plage" ou regroupement "Soleil d'Hiver"
    $headerSoleilTypeclub = \DB::table('typeclub')
        ->where('nomtypeclub', 'LIKE', '%Mer%')
        ->orWhere('nomtypeclub', 'LIKE', '%Plage%')
        ->first();
        
    $headerSoleilRegroupement = \DB::table('regroupementclub')
        ->where('nomregroupement', 'LIKE', '%Soleil%')
        ->first();
        
    // Pour Nos Offres : utiliser regroupement "Dernière minute"
    $headerDerniereMinuteRegroupement = \DB::table('regroupementclub')
        ->where('nomregroupement', 'LIKE', '%Dernière minute%')
        ->orWhere('nomregroupement', 'LIKE', '%Derniere minute%')
        ->first();
        
    // Construire les URLs
    $headerSkiUrl = url('/resorts');
    $headerSoleilUrl = url('/resorts');
    $headerOffresUrl = url('/resorts');
    
    // Ski : priorité au typeclub, puis localisation
    if ($headerSkiTypeclub) {
        $headerSkiUrl = url('/resorts?typeclub=' . $headerSkiTypeclub->numtypeclub);
    } elseif ($headerSkiLocalisation) {
        $headerSkiUrl = url('/resorts?localisation=' . $headerSkiLocalisation->numlocalisation);
    }
    
    // Soleil : priorité au typeclub, puis regroupement
    if ($headerSoleilTypeclub) {
        $headerSoleilUrl = url('/resorts?typeclub=' . $headerSoleilTypeclub->numtypeclub);
    } elseif ($headerSoleilRegroupement) {
        $headerSoleilUrl = url('/resorts?regroupement=' . $headerSoleilRegroupement->numregroupement);
    }
    
    // Nos Offres : regroupement "Dernière minute"
    if ($headerDerniereMinuteRegroupement) {
        $headerOffresUrl = url('/resorts?regroupement=' . $headerDerniereMinuteRegroupement->numregroupement);
    }
@endphp
<header class="sticky top-0 z-50 bg-clubmed-beige" role="banner">
    {{-- Barre supérieure --}}
    <div class="border-b border-black/10">
        <div class="max-w-7xl mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                
                {{-- Logo Club Med --}}
                <a href="{{ url('/') }}" class="flex items-center">
                    <span class="sr-only">Club Med</span>
                    <span class="text-xl lg:text-2xl font-serif font-bold tracking-tight text-black">Club Med</span>
                </a>

                {{-- Navigation centrale (desktop) --}}
                <nav class="hidden lg:flex items-center gap-x-8">
                    <a href="{{ url('/resorts') }}" class="text-sm font-medium text-black hover:text-clubmed-blue transition-colors py-2 border-b-2 border-transparent hover:border-clubmed-gold">
                        Destinations
                    </a>
                    <a href="{{ $headerSoleilUrl }}" class="text-sm font-medium text-black hover:text-clubmed-blue transition-colors py-2 border-b-2 border-transparent hover:border-clubmed-gold">
                        Vacances au soleil
                    </a>
                    <a href="{{ $headerSkiUrl }}" class="text-sm font-medium text-black hover:text-clubmed-blue transition-colors py-2 border-b-2 border-transparent hover:border-clubmed-gold">
                        Ski
                    </a>
                    <a href="{{ route('guide') }}" class="text-sm font-medium text-black hover:text-clubmed-blue transition-colors py-2 border-b-2 border-transparent hover:border-clubmed-gold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Guide
                    </a>
                    
                    @auth
                        @php
                            $userRole = strtolower(Auth::user()->role ?? '');
                            $isMarketing = str_contains($userRole, 'marketing');
                            $isVente = str_contains($userRole, 'vente');
                            $isAdmin = str_contains($userRole, 'admin') || str_contains($userRole, 'directeur');
                        @endphp
                        
                        {{-- Lien Espace Marketing --}}
                        @if($isMarketing)
                            <a href="{{ route('marketing.dashboard') }}" class="text-sm font-medium text-clubmed-blue hover:text-clubmed-blue-dark transition-colors py-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                Espace Marketing
                            </a>
                        @endif
                        
                        {{-- Lien Espace Vente --}}
                        @if($isVente)
                            <a href="{{ route('vente.dashboard') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors py-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                Espace Vente
                            </a>
                        @endif
                        
                        {{-- Lien Admin --}}
                        @if($isAdmin)
                            <a href="{{ url('/clients') }}" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors py-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Administration
                            </a>
                        @endif
                    @endauth
                </nav>

                {{-- Actions droites --}}
                <div class="flex items-center gap-x-3 lg:gap-x-4">
                    
                    {{-- Bouton Nos Offres (Dernière minute) --}}
                    <a href="{{ $headerOffresUrl }}" class="inline-flex items-center justify-center px-4 lg:px-6 py-2 lg:py-2.5 bg-clubmed-gold hover:bg-yellow-400 text-black rounded-full font-semibold text-sm transition-all hover:shadow-md">
                        <span class="hidden sm:inline">Nos Offres</span>
                        <span class="sm:hidden">%</span>
                    </a>

                    {{-- Menu utilisateur --}}
                    <div class="relative group">
                        <button class="inline-flex items-center justify-center w-10 h-10 lg:w-auto lg:h-auto lg:px-4 lg:py-2.5 bg-black hover:bg-gray-800 text-white rounded-full font-medium text-sm transition-colors cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 lg:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="hidden lg:inline">
                                @auth
                                    {{ Auth::user()->name }}
                                @else
                                    Mon compte
                                @endauth
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="hidden lg:block h-4 w-4 ml-1 group-hover:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown menu --}}
                        <div class="absolute right-0 top-full mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden transform scale-95 opacity-0 invisible group-hover:scale-100 group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right">
                            @auth
                                @php
                                    $userRole = strtolower(Auth::user()->role ?? '');
                                    $isMarketing = str_contains($userRole, 'marketing');
                                    $isVente = str_contains($userRole, 'vente');
                                    $isAdmin = str_contains($userRole, 'admin') || str_contains($userRole, 'directeur');
                                @endphp
                                
                                <div class="p-4 bg-clubmed-beige border-b border-gray-200">
                                    <p class="text-xs text-gray-500">Connecté en tant que</p>
                                    <p class="font-semibold text-black truncate">{{ Auth::user()->email }}</p>
                                    @if(Auth::user()->role)
                                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full 
                                            @if($isMarketing) bg-blue-100 text-blue-700
                                            @elseif($isVente) bg-emerald-100 text-emerald-700
                                            @elseif($isAdmin) bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ Auth::user()->role }}
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Liens selon le rôle --}}
                                @if($isMarketing || $isVente || $isAdmin)
                                    <div class="py-2 border-b border-gray-100">
                                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase">Espace professionnel</p>
                                        
                                        @if($isMarketing)
                                            <a href="{{ route('marketing.dashboard') }}" class="flex items-center px-4 py-3 text-sm text-clubmed-blue hover:bg-blue-50 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                                Espace Marketing
                                            </a>
                                        @endif
                                        
                                        @if($isVente)
                                            <a href="{{ route('vente.dashboard') }}" class="flex items-center px-4 py-3 text-sm text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                Espace Vente
                                            </a>
                                        @endif
                                        
                                        @if($isAdmin)
                                            <a href="{{ url('/clients') }}" class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                Gestion Clients
                                            </a>
                                            <a href="{{ url('/resorts/create') }}" class="flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                Créer un Resort
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="py-2">
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-clubmed-beige transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                        Tableau de bord
                                    </a>
                                    <a href="{{ route('cart.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-clubmed-beige transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Mon panier
                                    </a>
                                    <a href="{{ route('reservations.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-clubmed-beige transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Mes réservations
                                    </a>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Se déconnecter
                                    </button>
                                </form>
                            @else
                                <div class="p-4">
                                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 bg-black hover:bg-gray-800 text-white rounded-full font-semibold text-sm transition-colors mb-3">
                                        Se connecter
                                    </a>
                                    <a href="{{ route('inscription.create') }}" class="block w-full text-center px-4 py-3 border-2 border-black text-black hover:bg-black hover:text-white rounded-full font-semibold text-sm transition-colors">
                                        Créer un compte
                                    </a>
                                </div>
                                <div class="px-4 pb-4">
                                    <p class="text-xs text-gray-500 text-center">Rejoignez Great Members et profitez d'avantages exclusifs</p>
                                </div>
                            @endauth
                        </div>
                    </div>

                    {{-- Menu hamburger mobile --}}
                    <button class="lg:hidden inline-flex items-center justify-center w-10 h-10 text-black" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Menu mobile --}}
<div id="mobile-menu" class="fixed inset-0 z-[60] bg-clubmed-beige transform translate-x-full transition-transform duration-300 lg:hidden overflow-y-auto">
    <div class="flex items-center justify-between p-4 border-b border-black/10">
        <span class="text-xl font-serif font-bold">Club Med</span>
        <button id="close-mobile-menu" class="w-10 h-10 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    
    <nav class="p-4 space-y-2">
        <a href="{{ url('/resorts') }}" class="block py-3 text-lg font-medium text-black border-b border-black/10">Destinations</a>
        <a href="{{ $headerSoleilUrl }}" class="block py-3 text-lg font-medium text-black border-b border-black/10">Vacances au soleil</a>
        <a href="{{ $headerSkiUrl }}" class="block py-3 text-lg font-medium text-black border-b border-black/10">Ski</a>
    </nav>
    
    @auth
        @php
            $userRole = strtolower(Auth::user()->role ?? '');
            $isMarketing = str_contains($userRole, 'marketing');
            $isVente = str_contains($userRole, 'vente');
            $isAdmin = str_contains($userRole, 'admin') || str_contains($userRole, 'directeur');
        @endphp
        
        {{-- Section Espace Pro (mobile) --}}
        @if($isMarketing || $isVente || $isAdmin)
            <div class="px-4 pt-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Espace professionnel</p>
                <div class="space-y-2">
                    @if($isMarketing)
                        <a href="{{ route('marketing.dashboard') }}" class="flex items-center py-3 px-4 bg-blue-50 text-clubmed-blue rounded-xl font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                            Espace Marketing
                        </a>
                    @endif
                    @if($isVente)
                        <a href="{{ route('vente.dashboard') }}" class="flex items-center py-3 px-4 bg-emerald-50 text-emerald-600 rounded-xl font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Espace Vente
                        </a>
                    @endif
                    @if($isAdmin)
                        <a href="{{ url('/clients') }}" class="flex items-center py-3 px-4 bg-red-50 text-red-600 rounded-xl font-medium">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Administration
                        </a>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Compte utilisateur (mobile) --}}
        <div class="p-4 mt-4 border-t border-black/10">
            <div class="bg-white rounded-xl p-4 mb-4">
                <p class="text-xs text-gray-500">Connecté en tant que</p>
                <p class="font-semibold text-black truncate">{{ Auth::user()->name }}</p>
                @if(Auth::user()->role)
                    <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full 
                        @if($isMarketing) bg-blue-100 text-blue-700
                        @elseif($isVente) bg-emerald-100 text-emerald-700
                        @elseif($isAdmin) bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ Auth::user()->role }}
                    </span>
                @endif
            </div>
            <div class="space-y-2">
                <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700">Tableau de bord</a>
                <a href="{{ route('cart.index') }}" class="block py-2 text-gray-700">Mon panier</a>
                <a href="{{ route('reservations.index') }}" class="block py-2 text-gray-700">Mes réservations</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-3 bg-red-50 text-red-600 rounded-xl font-semibold">
                    Se déconnecter
                </button>
            </form>
        </div>
    @else
        {{-- Non connecté (mobile) --}}
        <div class="p-4 mt-4 border-t border-black/10 space-y-3">
            <a href="{{ route('login') }}" class="block w-full text-center py-3 bg-black text-white rounded-full font-semibold">
                Se connecter
            </a>
            <a href="{{ route('inscription.create') }}" class="block w-full text-center py-3 border-2 border-black text-black rounded-full font-semibold">
                Créer un compte
            </a>
        </div>
    @endauth
</div>

<script>
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.remove('translate-x-full');
});
document.getElementById('close-mobile-menu')?.addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.add('translate-x-full');
});
</script>