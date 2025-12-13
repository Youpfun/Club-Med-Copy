<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans text-[#113559]">
    @include('layouts.header')

    <main class="max-w-7xl mx-auto px-4 py-12 lg:px-8 space-y-16">
        
        {{-- EN-TÊTE --}}
        <div>
            <h1 class="text-4xl font-serif font-bold mb-2">Mon Espace Voyage</h1>
            <p class="text-gray-600">Gérez vos séjours, personnalisez vos activités et retrouvez vos souvenirs.</p>
        </div>

        {{-- Messages de succès (ex: après suppression du panier) --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ==================================================================================== --}}
        {{-- PARTIE 1 : PROCHAINS DÉPARTS (PANIER + À VENIR) --}}
        {{-- ==================================================================================== --}}
        <section>
            <div class="flex items-center gap-3 mb-6 border-b border-gray-200 pb-4">
                <div class="bg-blue-100 p-2 rounded-lg text-blue-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold">Prochains départs</h2>
                    <p class="text-sm text-gray-500">Finalisez vos projets et préparez vos valises.</p>
                </div>
            </div>

            @if($panierResorts->isEmpty() && $aVenir->isEmpty())
                {{-- CAS VIDE : Aucun projet --}}
                <div class="bg-white rounded-2xl p-12 text-center border border-dashed border-gray-300">
                    <div class="mb-4 text-gray-300">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <p class="text-gray-500 mb-6 text-lg">Vous n'avez aucun voyage prévu pour le moment.</p>
                    <a href="{{ url('/resorts') }}" class="inline-flex items-center px-8 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-full font-bold text-sm transition-colors shadow-md transform hover:scale-105 duration-200">
                        Réserver mon prochain séjour
                    </a>
                </div>
            @else
                <div class="grid gap-8">
                    
                    {{-- 1A. LE PANIER (Non réservé) --}}
                    @foreach($panierResorts as $resort)
                        <div class="bg-white rounded-3xl shadow-md border-2 border-[#ffc000] overflow-hidden flex flex-col lg:flex-row group hover:shadow-xl transition-all duration-300 relative">
                            {{-- Badge --}}
                            <div class="absolute top-0 right-0 bg-[#ffc000] text-[#113559] text-xs font-bold px-4 py-1 rounded-bl-xl z-20">
                                DANS VOTRE PANIER
                            </div>

                            {{-- Image --}}
                            <div class="lg:w-1/3 h-64 lg:h-auto bg-gray-200 relative overflow-hidden">
                                @php 
                                    $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                                    $imagePath = 'img/ressort/' . $imageName;
                                    $fullPath = public_path($imagePath);
                                @endphp
                                @if(file_exists($fullPath))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $resort->nomresort }}" class="w-full h-full object-cover grayscale opacity-90 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">Image indisponible</div>
                                @endif
                                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-[#113559] shadow-sm z-10">
                                    {{ $resort->nompays }}
                                </div>
                            </div>
                            
                            {{-- Contenu --}}
                            <div class="p-8 lg:w-2/3 flex flex-col justify-center">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-2xl font-serif font-bold text-[#113559]">{{ $resort->nomresort }}</h3>
                                </div>
                                <p class="text-gray-500 mb-6">Ce séjour est en attente dans votre panier. Finalisez-le vite pour garantir les disponibilités.</p>

                                <div class="flex flex-wrap gap-4 mt-auto">
                                    {{-- Bouton Finaliser --}}
                                    <a href="{{ route('cart.index') }}" class="flex-1 text-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-xl font-bold text-sm transition-colors shadow-md">
                                        Finaliser la réservation
                                    </a>
                                    
                                    {{-- Bouton Supprimer --}}
                                    <form action="{{ route('panier.remove', $resort->numreservation) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-6 py-3 bg-white border border-gray-200 hover:border-red-500 text-gray-500 hover:text-red-600 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- 1B. RÉSERVATIONS CONFIRMÉES --}}
                    @foreach($aVenir as $res)
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col lg:flex-row group hover:shadow-lg transition-all duration-300">
                            {{-- Image --}}
                            <div class="lg:w-1/3 h-64 lg:h-auto bg-gray-200 relative overflow-hidden">
                                @php 
                                    $imageName = strtolower(str_replace(' ', '', $res->nomresort)) . '.webp';
                                    $imagePath = 'img/ressort/' . $imageName;
                                    $fullPath = public_path($imagePath);
                                @endphp
                                @if(file_exists($fullPath))
                                    <img src="{{ asset($imagePath) }}" alt="{{ $res->nomresort }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">Image indisponible</div>
                                @endif
                                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-[#113559] shadow-sm">
                                    {{ $res->nompays }}
                                </div>
                            </div>
                            
                            {{-- Contenu --}}
                            <div class="p-8 lg:w-2/3 flex flex-col">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-2xl font-serif font-bold mb-1">{{ $res->nomresort }}</h3>
                                        <div class="text-sm font-medium text-blue-600 mb-4 bg-blue-50 inline-block px-3 py-1 rounded-md">
                                            Départ dans {{ \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($res->datedebut)) }} jours
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-600">Confirmé</span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm text-gray-600 mb-8">
                                    <div>
                                        <span class="block text-xs text-gray-400 uppercase font-bold mb-1">Arrivée</span>
                                        {{ \Carbon\Carbon::parse($res->datedebut)->format('d/m/Y') }}
                                    </div>
                                    <div>
                                        <span class="block text-xs text-gray-400 uppercase font-bold mb-1">Voyageurs</span>
                                        {{ $res->nbpersonnes }} pers.
                                    </div>
                                    <div class="col-span-2">
                                        <span class="block text-xs text-gray-400 uppercase font-bold mb-1">Hébergement</span>
                                        {{ $res->type_chambre }}
                                    </div>
                                </div>

                                <div class="mt-auto pt-6 border-t border-gray-100 flex flex-wrap gap-4">
                                    <a href="{{ route('reservation.show', $res->numreservation) }}" class="flex-1 text-center px-6 py-3 bg-white border-2 border-[#113559] text-[#113559] hover:bg-gray-50 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        Voir le détail
                                    </a>
                                    <a href="{{ route('reservation.activities', ['id' => $res->numreservation]) }}" class="flex-1 text-center px-6 py-3 bg-[#113559] hover:bg-[#0e2a47] text-white rounded-xl font-bold text-sm transition-colors flex items-center justify-center gap-2 shadow-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                        Ajouter des activités
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ==================================================================================== --}}
        {{-- PARTIE 2 : SÉJOURS EN COURS --}}
        {{-- ==================================================================================== --}}
        @if($enCours->isNotEmpty())
        <section>
            <div class="flex items-center gap-3 mb-6 border-b border-gray-200 pb-4">
                <div class="bg-green-100 p-2 rounded-lg text-green-700 animate-pulse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-green-800">Actuellement en séjour</h2>
                    <p class="text-sm text-gray-500">Profitez de chaque instant !</p>
                </div>
            </div>

            <div class="grid gap-6">
                @foreach($enCours as $res)
                    <div class="bg-gradient-to-r from-green-50 to-white rounded-3xl shadow-md border border-green-200 p-6 flex flex-col md:flex-row items-center gap-6">
                        <div class="md:w-1/4">
                            @php 
                                $imageName = strtolower(str_replace(' ', '', $res->nomresort)) . '.webp';
                                $imagePath = 'img/ressort/' . $imageName;
                                $fullPath = public_path($imagePath);
                            @endphp
                            @if(file_exists($fullPath))
                                <img src="{{ asset($imagePath) }}" alt="{{ $res->nomresort }}" class="w-full h-32 object-cover rounded-xl shadow-sm">
                            @else
                                <div class="w-full h-32 flex items-center justify-center text-gray-400 bg-gray-100 rounded-xl">Image indisponible</div>
                            @endif
                        </div>
                        <div class="md:w-2/4">
                            <h3 class="text-2xl font-serif font-bold text-[#113559]">{{ $res->nomresort }}</h3>
                            <p class="text-green-700 font-medium mb-2">Séjour en cours jusqu'au {{ \Carbon\Carbon::parse($res->datefin)->format('d/m') }}</p>
                            <p class="text-sm text-gray-600">Envie d'un soin au spa ou d'une excursion ? Réservez maintenant.</p>
                        </div>
                        <div class="md:w-1/4 w-full flex flex-col gap-3">
                            {{-- Bouton pour voir le détail --}}
                            <a href="{{ route('reservation.show', $res->numreservation) }}" class="w-full block text-center px-6 py-4 bg-white border-2 border-[#113559] text-[#113559] hover:bg-gray-50 rounded-xl font-bold text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Voir le détail
                            </a>

                            {{-- Bouton activités --}}
                            <a href="{{ route('reservation.activities', ['id' => $res->numreservation]) }}" class="w-full block text-center px-6 py-4 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] rounded-xl font-bold text-sm transition-colors shadow-sm">
                                Activités à la carte
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ==================================================================================== --}}
        {{-- PARTIE 3 : SÉJOURS TERMINÉS (HISTORIQUE) --}}
        {{-- ==================================================================================== --}}
        @if($terminees->isNotEmpty())
        <section>
            <div class="flex items-center gap-3 mb-6 border-b border-gray-200 pb-4">
                <div class="bg-gray-100 p-2 rounded-lg text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-700">Souvenirs de voyage</h2>
                    <p class="text-sm text-gray-500">Retrouvez vos séjours passés.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                @foreach($terminees as $res)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 flex gap-4 opacity-90 hover:opacity-100 transition-opacity">
                        <div class="w-24 h-24 flex-shrink-0">
                            @php 
                                $imageName = strtolower(str_replace(' ', '', $res->nomresort)) . '.webp';
                                $imagePath = 'img/ressort/' . $imageName;
                                $fullPath = public_path($imagePath);
                            @endphp
                            @if(file_exists($fullPath))
                                <img src="{{ asset($imagePath) }}" alt="{{ $res->nomresort }}" class="w-full h-full object-cover rounded-lg grayscale hover:grayscale-0 transition-all">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100 rounded-lg">Img</div>
                            @endif
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-[#113559]">{{ $res->nomresort }}</h4>
                            <p class="text-xs text-gray-500 mb-3">
                                {{ \Carbon\Carbon::parse($res->datedebut)->format('M Y') }} • {{ $res->nbpersonnes }} pers.
                            </p>
                            
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('reservation.show', $res->numreservation) }}" class="text-sm font-bold text-[#113559] hover:text-blue-800 hover:underline flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Revoir les détails
                                </a>
                                <a href="{{ route('reservation.review', ['id' => $res->numreservation]) }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                    Laisser un avis
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

    </main>

    @include('layouts.footer')
</body>
</html>