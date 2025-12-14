@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8 text-center">
            <h1 class="font-serif text-4xl text-[#113559] font-bold mt-2 mb-4">Campagnes Promotionnelles</h1>
        </div>

        {{-- MESSAGES --}}
        @if(session('success'))
            <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg border-l-4 border-green-500 shadow-sm">
                ✅ {!! session('success') !!}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm">
                ❌ {!! session('error') !!}
            </div>
        @endif

        {{-- ==============================
             SECTION 1 : ACTIONS GLOBALES
             ============================== --}}
        <div class="bg-indigo-900 rounded-2xl shadow-xl p-6 mb-10 text-white">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                ⚡ Actions de Masse
            </h2>

            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                {{-- COLONNE GAUCHE : APPLIQUER UNE PROMO --}}
                <div class="flex-grow w-full lg:w-2/3 border-r border-indigo-700 pr-0 lg:pr-8">
                    <form action="{{ route('marketing.bulk_promo') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        
                        {{-- Choix Période --}}
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">1. Période Ciblée</label>
                            <select name="numperiode" required class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400">
                                @foreach($periodes as $p)
                                    <option value="{{ $p->numperiode }}">{{ $p->nomperiode }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Choix Cible --}}
                        <div>
                            <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">2. Cible</label>
                            <select name="target_type" id="bulkTarget" onchange="toggleBulkType()" class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400">
                                <option value="global">🌍 Tous les Resorts</option>
                                <option value="category">🏔️ Par Type de Séjour</option>
                            </select>
                            
                            {{-- Sous-choix Type Club --}}
                            <div id="bulkTypeClubDiv" class="hidden mt-2">
                                <select name="type_club_id" class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400 text-sm">
                                    @foreach($typeClubs as $tc)
                                        <option value="{{ $tc->numtypeclub }}">{{ $tc->nomtypeclub }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Valeur --}}
                        <div>
                            <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">3. Prix Final (%)</label>
                            <div class="relative">
                                <input type="number" name="pourcentage" min="1" max="100" placeholder="Ex: 80" required 
                                       class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400 pl-3 pr-8">
                                <span class="absolute right-3 top-2.5 text-indigo-300 font-bold">%</span>
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-2">
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-lg flex items-center justify-center gap-2">
                                ✅ Appliquer la Promotion
                            </button>
                            <p class="text-xs text-indigo-300 mt-2 text-center">
                                Écrase les promotions existantes (80 = -20% de réduction).
                            </p>
                        </div>
                    </form>
                </div>

                {{-- COLONNE DROITE : RESET (DANGER ZONE) --}}
                <div class="w-full lg:w-1/3">
                    <div class="bg-red-900/30 p-4 rounded-xl border border-red-500/30">
                        <h3 class="text-red-300 font-bold mb-3 text-sm uppercase">⚠️ Zone de danger</h3>
                        <p class="text-indigo-200 text-xs mb-4">
                            Supprimer toutes les promotions pour une période donnée. Cette action est irréversible.
                        </p>
                        
                        <form action="{{ route('marketing.reset_promos') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer TOUTES les promotions de cette période ?');">
                            @csrf
                            <label class="block text-xs font-bold uppercase text-red-200 mb-1">Période à nettoyer</label>
                            <select name="numperiode" required class="w-full bg-red-900/50 border-red-500/50 text-white rounded-lg focus:ring-red-500 mb-3 text-sm">
                                @foreach($periodes as $p)
                                    <option value="{{ $p->numperiode }}">{{ $p->nomperiode }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg flex items-center justify-center gap-2 text-sm">
                                🗑️ Tout Réinitialiser
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>


        {{-- ==============================
             SECTION 2 : FILTRE & GESTION INDIVIDUELLE
             ============================== --}}
        
        <div class="flex flex-col md:flex-row gap-4 mb-8 items-center justify-between">
            {{-- Filtres --}}
            <form action="{{ route('marketing.dashboard') }}" method="GET" class="flex gap-4 flex-grow bg-white p-4 rounded-xl shadow-sm border border-slate-200">
                <div class="flex-grow">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filtrer par Type</label>
                    <select name="type_club" class="w-full rounded border-slate-300 text-sm" onchange="this.form.submit()">
                        <option value="">Tout voir</option>
                        @foreach($typeClubs as $tc)
                            <option value="{{ $tc->numtypeclub }}" {{ request('type_club') == $tc->numtypeclub ? 'selected' : '' }}>
                                {{ $tc->nomtypeclub }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Choisir Resort</label>
                    <select name="numresort" class="w-full rounded border-slate-300 text-sm" onchange="this.form.submit()">
                        <option value="">-- Sélectionner --</option>
                        @foreach($resorts as $r)
                            <option value="{{ $r->numresort }}" {{ (isset($selectedResort) && $selectedResort->numresort == $r->numresort) ? 'selected' : '' }}>
                                {{ $r->nomresort }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            {{-- Bouton Nouvelle Période --}}
            <button onclick="document.getElementById('modalPeriode').classList.remove('hidden')" 
                    class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-4 py-3 rounded-xl font-bold shadow-sm transition">
                📅 + Période
            </button>
        </div>


        {{-- GRILLE INDIVIDUELLE --}}
        @if(isset($selectedResort))
            <h2 class="text-2xl font-bold text-[#113559] mb-6 pb-2 border-b border-slate-200">
                Gestion détaillée : <span class="text-blue-600">{{ $selectedResort->nomresort }}</span>
            </h2>

            @foreach($periodes as $periode)
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-slate-100">
                    <h3 class="text-lg font-bold text-[#113559] mb-4">{{ $periode->nomperiode }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($typesChambre as $typeChambre)
                            @php $stat = $stats[$periode->numperiode][$typeChambre->numtype] ?? null; @endphp

                            @if($stat && $stat['valide_pour_resort'])
                                <div class="bg-slate-50 rounded-xl p-4 border transition-all {{ $stat['isActive'] ? 'border-green-400 bg-green-50' : 'border-slate-200' }}">
                                    
                                    {{-- En-tête Carte --}}
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-slate-700">{{ $typeChambre->nomtype }}</h4>
                                        @if($stat['isActive'])
                                            <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded">Actif</span>
                                        @endif
                                    </div>

                                    {{-- Prix actuel --}}
                                    <div class="text-xs text-slate-500 mb-3">
                                        Standard : <span class="line-through">{{ number_format($stat['prix_base'], 0) }}€</span>
                                        @if($stat['isActive'])
                                            <br>Promo : <span class="font-bold text-green-700 text-sm">{{ number_format($stat['current_promo'], 0) }}€</span>
                                        @endif
                                    </div>

                                    {{-- Formulaire --}}
                                    <form action="{{ route('marketing.update_price') }}" method="POST" x-data="{ mode: 'percentage' }">
                                        @csrf
                                        <input type="hidden" name="numperiode" value="{{ $periode->numperiode }}">
                                        <input type="hidden" name="numtype" value="{{ $typeChambre->numtype }}">
                                        <input type="hidden" name="numresort" value="{{ $selectedResort->numresort }}">
                                        
                                        {{-- Toggle % / € --}}
                                        <div class="flex justify-center mb-2">
                                            <div class="bg-slate-200 p-1 rounded-lg flex text-xs font-bold">
                                                <button type="button" @click="mode = 'percentage'" 
                                                    :class="mode === 'percentage' ? 'bg-white shadow text-blue-800' : 'text-slate-500'"
                                                    class="px-3 py-1 rounded-md transition">
                                                    % (Taux)
                                                </button>
                                                <button type="button" @click="mode = 'amount'" 
                                                    :class="mode === 'amount' ? 'bg-white shadow text-blue-800' : 'text-slate-500'"
                                                    class="px-3 py-1 rounded-md transition">
                                                    € (Prix Fixe)
                                                </button>
                                            </div>
                                            <input type="hidden" name="mode" x-model="mode">
                                        </div>

                                        {{-- Input --}}
                                        <div class="flex gap-2">
                                            <div class="relative w-full">
                                                <input type="number" name="valeur" min="0" 
                                                       :placeholder="mode === 'percentage' ? '{{ $stat['taux_calcule'] }}' : '{{ $stat['current_promo'] ?? '' }}'"
                                                       class="w-full pl-3 pr-8 py-2 border rounded-lg text-center font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                                                
                                                <span class="absolute right-3 top-2 text-slate-400 font-bold text-xs" x-text="mode === 'percentage' ? '%' : '€'"></span>
                                            </div>
                                            <button type="submit" class="bg-[#113559] text-white px-3 rounded-lg font-bold hover:bg-blue-800 text-sm">OK</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- Script pour le Toggle Bulk --}}
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    function toggleBulkType() {
        const val = document.getElementById('bulkTarget').value;
        const div = document.getElementById('bulkTypeClubDiv');
        div.style.display = (val === 'category') ? 'block' : 'none';
    }
</script>

{{-- Modal Ajout Période (Caché) --}}
<div id="modalPeriode" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-[#113559] mb-4">Nouvelle Période</h3>
        <form action="{{ route('marketing.store_periode') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <input type="text" name="nomperiode" placeholder="Nom (ex: Été 2026)" required class="w-full border-slate-300 rounded-lg">
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="datedebutperiode" required class="w-full border-slate-300 rounded-lg">
                    <input type="date" name="datefinperiode" required class="w-full border-slate-300 rounded-lg">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('modalPeriode').classList.add('hidden')" class="text-slate-500">Annuler</button>
                <button type="submit" class="bg-[#113559] text-white px-4 py-2 rounded-lg font-bold">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection