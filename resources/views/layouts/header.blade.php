<header class="flex items-center justify-between gap-x-4 overflow-visible bg-white p-4 px-4 lg:px-8 relative z-50 border-b border-gray-100" role="banner">
    <a href="{{ url('/') }}" class="w-32 md:w-40">
        <span class="sr-only">Club Med Luxury All Inclusive Resorts & Holiday Packages</span>
        <span class="text-2xl font-bold text-blue-700">Club Med</span>
    </a>

    <nav class="hidden md:flex items-center gap-x-6 px-4">
        <a href="{{ url('/resorts') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Nos Resorts</a>
        <a href="{{ url('/clients') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">Clients (Admin)</a>
        
        @auth
            {{-- Accès Espace Marketing pour Directeur OU Membre --}}
            @if(strpos(strtolower(Auth::user()->role ?? ''), 'marketing') !== false)
                <a href="{{ route('marketing.dashboard') }}" class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                    Espace Marketing
                </a>
            @endif
        @endauth
    </nav>

    <div class="flex gap-x-4 items-center">
        
        <a href="{{ url('/resorts') }}" class="flex items-center justify-center px-4 py-2 md:px-6 md:py-2.5 bg-yellow-500 hover:bg-yellow-600 text-black rounded-full font-semibold text-sm transition-colors">
            <span class="hidden md:inline">Nos Offres</span>
            <span class="md:hidden text-xl">%</span>
        </a>

        <div class="relative group">
            <button class="inline-flex items-center justify-center px-6 py-2.5 bg-blue-900 hover:bg-blue-800 text-white rounded-full font-bold text-sm transition-colors shadow-lg cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                
                @auth
                    {{ Auth::user()->name }}
                @else
                    Espace Client
                @endauth

                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:rotate-180 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="absolute right-0 top-full mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 overflow-hidden transform scale-95 opacity-0 invisible group-hover:scale-100 group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right z-50">
                
                @auth
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100 mb-2">
                            Connecté en tant que<br>
                            <span class="font-bold text-blue-900">{{ Auth::user()->email }}</span>
                        </div>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900">
                            Mon tableau de bord
                        </a>
                        <a href="{{ route('cart.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900">
                            Panier
                        </a>
                        <a href="{{ route('reservations.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900">
                            Mes réservations
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-2">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-semibold">
                                Se déconnecter
                            </button>
                        </form>
                    </div>

                @else
                    <div class="py-2">
                        <span class="block px-4 py-2 text-xs text-gray-400 uppercase tracking-wider font-bold">
                            Compte Club Med
                        </span>
                        
                        <a href="{{ route('login') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 border-l-4 border-transparent hover:border-blue-900 transition-all">
                            Se connecter
                        </a>

                        <a href="{{ route('inscription.create') }}" class="block px-4 py-3 text-sm font-bold text-blue-900 bg-blue-50 hover:bg-blue-100 transition-colors">
                            Créer un compte
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>