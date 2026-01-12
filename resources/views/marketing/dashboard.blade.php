@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- EN-TÊTE PRINCIPAL --}}
        <div class="mb-8 text-center">
            <h1 class="font-serif text-4xl text-clubmed-blue font-bold mt-2 mb-4">Tableau de Bord Marketing</h1>
            <p class="text-slate-500">
                Bienvenue, {{ Auth::user()->name }} 
                <span class="text-xs bg-clubmed-blue/10 text-clubmed-blue py-1 px-2 rounded-full ml-2">{{ Auth::user()->role }}</span>
            </p>
        </div>

        {{-- MESSAGES DE NOTIFICATION --}}
        @if(session('success'))
            <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg border-l-4 border-green-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">✅</span> {!! session('success') !!}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">❌</span> {!! session('error') !!}
            </div>
        @endif

        {{-- ==============================
             SECTION 0 : ACTIONS RAPIDES (ACCESSIBLES À TOUS)
             ============================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            
            {{-- 1. NOUVEAU SÉJOUR --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-clubmed-blue hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-clubmed-blue/10 p-2 rounded-lg">🏨</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Nouveau Séjour</h3>
                        <p class="text-sm text-gray-500">Créer une fiche resort</p>
                    </div>
                </div>
                <a href="{{ route('resort.create') }}" class="block w-full text-center px-4 py-3 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg font-bold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Créer
                </a>
            </div>

            {{-- 2. INDISPONIBILITÉS --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-red-500 hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-red-100 p-2 rounded-lg">🚫</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Fermetures</h3>
                        <p class="text-sm text-gray-500">Gérer les travaux/incidents</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('marketing.indisponibilite.select') }}" class="block w-full text-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Bloquer une chambre
                    </a>
                    <a href="{{ route('marketing.indisponibilite.index') }}" class="block w-full text-center px-4 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Voir la liste
                    </a>
                </div>
            </div>

            {{-- 3. DEMANDES DE DISPO --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-green-100 p-2 rounded-lg">📋</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Demandes Dispo</h3>
                        <p class="text-sm text-gray-500">Vérifier avant création</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('marketing.demandes.create') }}" class="block w-full text-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Nouvelle demande
                    </a>
                    <a href="{{ route('marketing.demandes.index') }}" class="block w-full text-center px-4 py-2 bg-white border border-green-200 text-green-600 hover:bg-green-50 rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Voir les demandes
                    </a>
                </div>
            </div>

            {{-- 4. PROSPECTION RESORTS --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-purple-100 p-2 rounded-lg">🔍</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Prosp. Resorts</h3>
                        <p class="text-sm text-gray-500">Contacter nouveaux resorts</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('marketing.prospection.create') }}" class="block w-full text-center px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Contacter
                    </a>
                    <a href="{{ route('marketing.prospection.index') }}" class="block w-full text-center px-4 py-2 bg-white border border-purple-200 text-purple-600 hover:bg-purple-50 rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Suivi
                    </a>
                </div>
            </div>

            {{-- 5. PROSPECTION PARTENAIRES --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-emerald-500 hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-emerald-100 p-2 rounded-lg">🤝</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Prosp. Partenaires</h3>
                        <p class="text-sm text-gray-500">Contacter ESF, Spa, etc.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('marketing.prospection-partenaire.create') }}" class="block w-full text-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Contacter
                    </a>
                    <a href="{{ route('marketing.prospection-partenaire.index') }}" class="block w-full text-center px-4 py-2 bg-white border border-emerald-200 text-emerald-600 hover:bg-emerald-50 rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Suivi
                    </a>
                </div>
            </div>

            {{-- 6. PROJETS DE SÉJOUR --}}
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-indigo-500 hover:shadow-lg transition">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3 bg-indigo-100 p-2 rounded-lg">📋</span>
                    <div>
                        <h3 class="text-lg font-bold font-serif text-gray-900">Projets de Séjour</h3>
                        <p class="text-sm text-gray-500">Soumettre au Directeur</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('marketing.projet-sejour.create') }}" class="block w-full text-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Nouveau projet
                    </a>
                    <a href="{{ route('marketing.projet-sejour.index') }}" class="block w-full text-center px-4 py-2 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold transition flex items-center justify-center gap-2 text-sm">
                        Voir les projets
                    </a>
                </div>
            </div>

        </div>

        {{-- ==============================
             SECTION 1 : CATALOGUE DES SÉJOURS (VISIBLE PAR TOUS)
             ============================== --}}
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden mb-10">
            <div class="p-6 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold font-serif text-clubmed-blue flex items-center gap-2">
                        📂 Catalogue des Séjours 
                        <span class="text-xs bg-slate-200 text-slate-600 px-2 py-1 rounded-full">{{ $resortsList->count() }}</span>
                    </h2>
                    <p class="text-sm text-slate-500">Reprenez la création ou modifiez les configurations des séjours existants.</p>
                </div>
            </div>

            <div class="overflow-x-auto max-h-96">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Resort</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pays</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">État Config.</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($resortsList as $resortItem)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-clubmed-blue/10 rounded-full flex items-center justify-center text-xl">🏨</div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $resortItem->nomresort }}</div>
                                            <div class="text-xs text-gray-500">{{ $resortItem->nbtridents }} Tridents</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $resortItem->pays->nompays ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    {{-- AFFICHAGE DE L'ÉTAT --}}
                                    @if($resortItem->est_valide ?? false)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                            🌍 En Ligne
                                        </span>
                                    @elseif($resortItem->typechambres_count > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            Hors ligne / Prêt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            Incomplet
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-3">
                                        
                                        {{-- GESTION ÉTAT (En Ligne / Hors Ligne) --}}
                                        @if($isDirecteur)
                                            <form action="{{ route('marketing.resort.status', $resortItem->numresort) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                @if($resortItem->est_valide ?? false)
                                                    <input type="hidden" name="est_valide" value="0">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-700 border border-green-200 transition group" title="Cliquez pour mettre hors ligne">
                                                        <span class="group-hover:hidden">🟢</span>
                                                        <span class="hidden group-hover:inline">🛑</span>
                                                    </button>
                                                @else
                                                    <input type="hidden" name="est_valide" value="1">
                                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-500 hover:bg-green-100 hover:text-green-700 border border-gray-300 transition group" title="Cliquez pour publier">
                                                        <span class="group-hover:hidden">🛑</span>
                                                        <span class="hidden group-hover:inline">🟢</span>
                                                    </button>
                                                @endif
                                            </form>
                                        @endif

                                        <div class="h-6 w-px bg-slate-200 mx-1"></div>

                                        {{-- ACTIONS MODIFICATION --}}
                                        <a href="{{ route('resort.editStructure', $resortItem->numresort) }}" class="text-gray-500 hover:text-blue-600 transition" title="Modifier Structure">
                                            Modifier
                                        </a>
                                        <a href="{{ route('resort.step2', $resortItem->numresort) }}" class="text-gray-500 hover:text-blue-600 transition" title="Hébergement">
                                            Chambres.
                                        </a>
                                        <a href="{{ route('resort.step3', $resortItem->numresort) }}" class="text-gray-500 hover:text-purple-600 transition" title="Activités">
                                            Activités.
                                        </a>
                                        <a href="{{ route('resort.step4', $resortItem->numresort) }}" class="text-gray-500 hover:text-green-600 transition" title="Prix">
                                            €
                                        </a>

                                        {{-- BOUTON SUPPRIMER --}}
                                        @if($isDirecteur)
                                            <form action="{{ route('marketing.resort.destroy', $resortItem->numresort) }}" method="POST" onsubmit="return confirm('⚠️ ATTENTION : Cette action est IRRÉVERSIBLE !\n\nCela supprimera :\n- Le resort {{ $resortItem->nomresort }}\n- Toutes les réservations associées\n- Les photos, avis et tarifs\n\nÊtes-vous sûr de vouloir continuer ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50" title="Supprimer définitivement">
                                                    Suppr.
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==============================
             SECTION 2 : GESTION PRIX & PROMOS (RÉSERVÉ AU DIRECTEUR)
             ============================== --}}
        
        @if($isDirecteur)
            
            <div class="bg-indigo-900 rounded-2xl shadow-xl p-6 mb-10 text-white">
                <h2 class="text-xl font-bold font-serif mb-4 flex items-center gap-2">
                    ⚡ Campagnes Promotionnelles (Réservé Directeur)
                </h2>

                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    
                    {{-- APPLIQUER UNE PROMO --}}
                    <div class="flex-grow w-full lg:w-2/3 border-r border-indigo-700 pr-0 lg:pr-8">
                        <form action="{{ route('marketing.bulk_promo') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">1. Période Ciblée</label>
                                <select name="numperiode" required class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400">
                                    @foreach($periodes as $p)
                                        <option value="{{ $p->numperiode }}">{{ $p->nomperiode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase text-indigo-200 mb-1">2. Cible</label>
                                <select name="target_type" id="bulkTarget" onchange="toggleBulkType()" class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400">
                                    <option value="global">🌍 Tous les Resorts</option>
                                    <option value="category">🏔️ Par Type de Séjour</option>
                                </select>
                                <div id="bulkTypeClubDiv" class="hidden mt-2">
                                    <select name="type_club_id" class="w-full bg-indigo-800 border-indigo-700 text-white rounded-lg focus:ring-indigo-400 text-sm">
                                        @foreach($typeClubs as $tc)
                                            <option value="{{ $tc->numtypeclub }}">{{ $tc->nomtypeclub }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                    🏷️ Appliquer la Promotion
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- RESET --}}
                    <div class="w-full lg:w-1/3">
                        <div class="bg-red-900/30 p-4 rounded-xl border border-red-500/30">
                            <h3 class="text-red-300 font-bold mb-3 text-sm uppercase">⚠️ Zone de danger</h3>
                            <p class="text-indigo-200 text-xs mb-4">Supprimer toutes les promotions pour une période.</p>
                            <form action="{{ route('marketing.reset_promos') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                                @csrf
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

            {{-- GESTION DÉTAILLÉE PRIX (TABLEAUX) --}}
            <div class="flex flex-col md:flex-row gap-4 mb-8 items-center justify-between">
                <form action="{{ route('marketing.dashboard') }}" method="GET" class="flex gap-4 flex-grow bg-white p-4 rounded-xl shadow-sm border border-slate-200 w-full md:w-auto">
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filtrer par Type</label>
                        <select name="type_club" class="w-full rounded border-slate-300 text-sm" onchange="this.form.submit()">
                            <option value="">Tout voir</option>
                            @foreach($typeClubs as $tc)
                                <option value="{{ $tc->numtypeclub }}" {{ request('type_club') == $tc->numtypeclub ? 'selected' : '' }}>{{ $tc->nomtypeclub }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Choisir Resort</label>
                        <select name="numresort" class="w-full rounded border-slate-300 text-sm" onchange="this.form.submit()">
                            <option value="">-- Sélectionner --</option>
                            @foreach($resorts as $r)
                                <option value="{{ $r->numresort }}" {{ (isset($selectedResort) && $selectedResort->numresort == $r->numresort) ? 'selected' : '' }}>{{ $r->nomresort }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <button onclick="document.getElementById('modalPeriode').classList.remove('hidden')" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 px-6 py-3 rounded-xl font-bold shadow-sm transition whitespace-nowrap">
                    📅 + Période
                </button>
            </div>

            @if(isset($selectedResort))
                <h2 class="text-2xl font-bold font-serif text-clubmed-blue mb-6 pb-2 border-b border-slate-200">
                    Gestion détaillée : <span class="text-clubmed-gold">{{ $selectedResort->nomresort }}</span>
                </h2>

                @foreach($periodes as $periode)
                    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-slate-100">
                        <h3 class="text-lg font-bold font-serif text-clubmed-blue mb-4">{{ $periode->nomperiode }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($typesChambre as $typeChambre)
                                @php $stat = $stats[$periode->numperiode][$typeChambre->numtype] ?? null; @endphp
                                @if($stat && $stat['valide_pour_resort'])
                                    <div class="bg-slate-50 rounded-xl p-4 border transition-all {{ $stat['isActive'] ? 'border-green-400 bg-green-50' : 'border-slate-200' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-slate-700">{{ $typeChambre->nomtype }}</h4>
                                            @if($stat['isActive'])
                                                <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded">Actif</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 mb-3">
                                            Standard : <span class="line-through">{{ number_format($stat['prix_base'], 0) }}€</span>
                                            @if($stat['isActive'])
                                                <br>Promo : <span class="font-bold text-green-700 text-sm">{{ number_format($stat['current_promo'], 0) }}€</span>
                                            @endif
                                        </div>
                                        <form action="{{ route('marketing.update_price') }}" method="POST" x-data="{ mode: 'percentage' }">
                                            @csrf
                                            <input type="hidden" name="numperiode" value="{{ $periode->numperiode }}">
                                            <input type="hidden" name="numtype" value="{{ $typeChambre->numtype }}">
                                            <input type="hidden" name="numresort" value="{{ $selectedResort->numresort }}">
                                            
                                            <div class="flex justify-center mb-2">
                                                <div class="bg-slate-200 p-1 rounded-lg flex text-xs font-bold">
                                                    <button type="button" @click="mode = 'percentage'" :class="mode === 'percentage' ? 'bg-white shadow text-blue-800' : 'text-slate-500'" class="px-3 py-1 rounded-md transition">%</button>
                                                    <button type="button" @click="mode = 'amount'" :class="mode === 'amount' ? 'bg-white shadow text-blue-800' : 'text-slate-500'" class="px-3 py-1 rounded-md transition">€</button>
                                                </div>
                                                <input type="hidden" name="mode" x-model="mode">
                                            </div>

                                            <div class="flex gap-2">
                                                <div class="relative w-full">
                                                    <input type="number" name="valeur" min="0" :placeholder="mode === 'percentage' ? '{{ $stat['taux_calcule'] }}' : '{{ $stat['current_promo'] ?? '' }}'" class="w-full pl-3 pr-8 py-2 border rounded-lg text-center font-bold text-slate-700 focus:ring-2 focus:ring-clubmed-blue outline-none text-sm">
                                                    <span class="absolute right-3 top-2 text-slate-400 font-bold text-xs" x-text="mode === 'percentage' ? '%' : '€'"></span>
                                                </div>
                                                <button type="submit" class="bg-clubmed-blue text-white px-3 rounded-lg font-bold hover:bg-clubmed-blue/90 text-sm">OK</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

        @else
            {{-- MESSAGE POUR LE MEMBRE (QUI N'A PAS ACCÈS AUX PROMOS) --}}
            <div class="bg-clubmed-blue/10 p-6 rounded-xl border-l-4 border-clubmed-blue text-clubmed-blue mt-10">
                <p class="font-bold">Accès Prix restreint</p>
                <p>En tant que membre, l'accès à la gestion des prix et promotions est réservé au Directeur.</p>
            </div>
        @endif

    </div>
</div>

{{-- MODAL et SCRIPTS (Uniquement pour le Directeur) --}}
@if(Auth::user()->role === 'Directeur du Service Marketing')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function toggleBulkType() {
            const val = document.getElementById('bulkTarget').value;
            const div = document.getElementById('bulkTypeClubDiv');
            div.style.display = (val === 'category') ? 'block' : 'none';
        }
    </script>

    <div id="modalPeriode" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold font-serif text-clubmed-blue mb-4">Nouvelle Période</h3>
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
                    <button type="button" onclick="document.getElementById('modalPeriode').classList.add('hidden')" class="text-slate-500 hover:text-gray-700">Annuler</button>
                    <button type="submit" class="bg-clubmed-blue hover:bg-clubmed-blue/90 text-white px-4 py-2 rounded-lg font-bold transition">Créer</button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection