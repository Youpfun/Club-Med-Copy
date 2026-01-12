<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning & Occupation - Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="py-12 px-4 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-serif">Planning des Chambres</h1>
                    <p class="text-gray-600 mt-1">Gérez les pannes et déplacez les clients si nécessaire.</p>
                </div>
                <div class="mt-4 md:mt-0 space-x-2">
                    <a href="{{ route('marketing.indisponibilite.index') }}" class="text-gray-600 hover:text-gray-900 font-semibold text-sm mr-4">← Retour Liste</a>
                    <a href="{{ route('marketing.indisponibilite.select') }}" class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-full font-bold shadow transition">
                        + Bloquer (Générique)
                    </a>
                </div>
            </div>

            {{-- Filtres --}}
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <form action="{{ route('marketing.indisponibilite.occupancy') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Resort</label>
                        <select name="numresort" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" onchange="this.form.submit()">
                            @foreach($resorts as $r)
                                <option value="{{ $r->numresort }}" {{ (isset($selectedResort) && $selectedResort->numresort == $r->numresort) ? 'selected' : '' }}>
                                    {{ $r->nomresort }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                        <input type="date" name="date_debut" value="{{ $dateDebut }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                        <input type="date" name="date_fin" value="{{ $dateFin }}" class="w-full rounded-md border-gray-300 shadow-sm focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div class="md:col-span-4 flex justify-end mt-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 text-sm font-bold">Actualiser le Planning</button>
                    </div>
                </form>
            </div>

            @if($selectedResort)
                {{-- STATISTIQUES --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @foreach($stats as $stat)
                        <div class="bg-white rounded-lg shadow p-4 border-t-4 {{ $stat['dispo'] <= 0 ? 'border-red-500' : 'border-green-500' }}">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-bold text-gray-900">{{ $stat['type']->nomtype }}</h3>
                                <span class="text-xs font-bold px-2 py-1 rounded {{ $stat['dispo'] <= 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $stat['dispo'] }} Libres
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Total: {{ $stat['total'] }} | Occ: {{ $stat['reservees'] }} | HS: {{ $stat['bloquees'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- TABLEAU DES CHAMBRES (MAPPING) --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">État Détaillé des Chambres</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Chambre</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Occupant / État</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($roomsView as $item)
                                    <tr class="hover:bg-gray-50 transition-colors {{ $item['etat'] == 'bloquee' ? 'bg-red-50' : ($item['etat'] == 'occupee' ? 'bg-blue-50' : '') }}">
                                        
                                        {{-- 1. Numéro --}}
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                            N° {{ $item['chambre']->numchambre }}
                                        </td>
                                        
                                        {{-- 2. Type --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item['type']->nomtype }}
                                        </td>
                                        
                                        {{-- 3. État / Nom --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item['etat'] == 'bloquee')
                                                <div class="text-red-600 font-bold flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Indisponible
                                                </div>
                                                <div class="text-xs text-red-500">{{ $item['indispo']->motif }}</div>
                                            
                                            @elseif($item['etat'] == 'occupee')
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                                        {{ strtoupper(substr($item['reservation']->user->name ?? 'C', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-bold text-gray-900">
                                                            {{ $item['reservation']->user->name ?? 'Client Inconnu' }}
                                                        </div>
                                                        <div class="text-xs text-blue-600">
                                                            <a href="{{ route('reservation.show', $item['reservation']->numreservation) }}" target="_blank" class="hover:underline">
                                                                Réservation #{{ $item['reservation']->numreservation }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Libre
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 4. Actions --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($item['etat'] == 'bloquee')
                                                {{-- Débloquer --}}
                                                <form action="{{ route('marketing.indisponibilite.destroy', $item['indispo']->numindisponibilite) }}" method="POST" class="inline" onsubmit="return confirm('Libérer cette chambre ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-green-600 hover:text-green-900 font-bold text-xs">
                                                        Rouvrir
                                                    </button>
                                                </form>
                                            
                                            @elseif($item['etat'] == 'occupee')
                                                {{-- Bloquer & Gérer Conflit --}}
                                                <form action="{{ route('marketing.indisponibilite.store') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="idchambre" value="{{ $item['chambre']->idchambre }}">
                                                    <input type="hidden" name="datedebut" value="{{ $dateDebut }}">
                                                    <input type="hidden" name="datefin" value="{{ $dateFin }}">
                                                    <input type="hidden" name="motif" value="Maintenance Urgente">
                                                    {{-- On passe l'ID de la résa cible pour faciliter le traitement du conflit --}}
                                                    <input type="hidden" name="target_reservation" value="{{ $item['reservation']->numreservation }}">
                                                    
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-orange-600 hover:bg-orange-700 focus:outline-none" title="Signaler un problème dans cette chambre occupée">
                                                        ⚠️ Bloquer & Gérer
                                                    </button>
                                                </form>

                                            @else
                                                {{-- Bloquer Simple --}}
                                                <form action="{{ route('marketing.indisponibilite.store') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="idchambre" value="{{ $item['chambre']->idchambre }}">
                                                    <input type="hidden" name="datedebut" value="{{ $dateDebut }}">
                                                    <input type="hidden" name="datefin" value="{{ $dateFin }}">
                                                    <input type="hidden" name="motif" value="Maintenance">
                                                    
                                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold border border-red-200 px-2 py-1 rounded hover:bg-red-50 text-xs">
                                                        Bloquer
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">Sélectionnez un resort pour voir le planning.</p>
                </div>
            @endif

        </div>
    </main>
</body>
</html>