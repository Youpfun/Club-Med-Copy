<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilotage Promotions | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Newsreader', serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    @include('layouts.header')

    <main class="max-w-5xl mx-auto px-4 py-12">
        
        <div class="mb-12 text-center">
            <span class="text-blue-600 font-bold tracking-wider uppercase text-xs">Espace Marketing</span>
            <h1 class="font-serif text-4xl text-[#113559] font-bold mt-2 mb-4">Campagnes Promotionnelles</h1>
            <p class="text-slate-500 max-w-2xl mx-auto">
                Appliquez un pourcentage de réduction (ex: -20%) pour un type de chambre sur toute une période.
            </p>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center gap-3">
                <span class="text-xl">✅</span> {!! session('success') !!}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-8 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm flex items-center gap-3">
                <span class="text-xl">❌</span> {!! session('error') !!}
            </div>
        @endif

        {{-- GRILLE DES PÉRIODES --}}
        @foreach($periodes as $periode)
            
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-10 border border-slate-100">
                <h2 class="text-3xl font-bold font-serif text-[#113559] mb-1">{{ $periode->nomperiode }}</h2>
                <p class="text-sm text-slate-500 mb-6">
                    Du {{ \Carbon\Carbon::parse($periode->datedebutperiode)->format('d/m/Y') }} 
                    au {{ \Carbon\Carbon::parse($periode->datefinperiode)->format('d/m/Y') }}
                </p>

                {{-- SOUS-GRILLE DES TYPES DE CHAMBRE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($typesChambre as $typeChambre)
                        @php
                            // Utilisation de la clé combinée pour récupérer les stats
                            $stat = $stats[$periode->numperiode][$typeChambre->numtypch] ?? ['total' => 0, 'promos' => 0, 'isActive' => false, 'current_taux' => 100];
                            $tauxActuel = $stat['current_taux'];
                            $reductionActuelle = 100 - $tauxActuel;
                        @endphp

                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 relative">
                            
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-bold text-slate-700">{{ $typeChambre->nomtypch }}</h3>
                                @if($stat['isActive'])
                                    <span class="text-lg font-extrabold text-green-600 bg-green-100 px-3 py-1 rounded-full">
                                        -{{ $reductionActuelle }}%
                                    </span>
                                @else
                                    <span class="text-sm font-bold text-slate-400">Tarif standard</span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-400 mb-4">
                                {{ $stat['total'] }} Tarifs / Séjours concernés. 
                                @if($stat['isActive'])
                                    (Prix de base : 100% → **{{ $tauxActuel }}%**)
                                @endif
                            </p>

                            <form action="{{ route('marketing.update_price') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="numperiode" value="{{ $periode->numperiode }}">
                                <input type="hidden" name="numtypch" value="{{ $typeChambre->numtypch }}">
                                
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nouveau taux du prix final (%)</label>
                                
                                <div class="flex gap-2">
                                    <div class="relative flex-grow">
                                        <input type="number" 
                                               name="taux_final" 
                                               min="0" 
                                               max="100" 
                                               step="1"
                                               placeholder="{{ $tauxActuel }}" 
                                               class="w-full pl-4 pr-8 py-3 bg-white border-2 border-slate-200 rounded-xl focus:border-[#ffc000] focus:ring-0 font-bold text-lg text-slate-700 transition-colors"
                                        >
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                            <span class="text-slate-400 font-bold">%</span>
                                        </div>
                                    </div>

                                    <button type="submit" class="px-6 py-3 bg-[#113559] hover:bg-[#0e2a47] text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                        Appliquer
                                    </button>
                                </div>
                                
                                <p class="text-xs text-slate-400 mt-3 text-center">
                                    Entrez **100** pour annuler les promos (prix initial). Entrez **80** pour une réduction de **20%**.
                                </p>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </main>

    @include('layouts.footer')
</body>
</html>