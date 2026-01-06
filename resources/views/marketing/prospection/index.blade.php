@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                        ← Retour au tableau de bord
                    </a>
                    <h1 class="font-serif text-3xl text-clubmed-blue font-bold">🔍 Prospection Resorts</h1>
                    <p class="text-slate-500 mt-1">Demandes d'information envoyées à des resorts potentiels</p>
                </div>
                <a href="{{ route('marketing.prospection.create') }}" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouvelle prospection
                </a>
            </div>
        </div>

        {{-- MESSAGES --}}
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

        {{-- STATISTIQUES --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            @php
                $totalProspections = $prospections->total();
                $envoyees = \App\Models\ProspectionResort::where('statut', 'envoyee')->count();
                $repondues = \App\Models\ProspectionResort::where('statut', 'repondue')->count();
                $enCours = \App\Models\ProspectionResort::where('statut', 'en_cours')->count();
            @endphp
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500">
                <p class="text-2xl font-bold text-purple-600">{{ $totalProspections }}</p>
                <p class="text-sm text-gray-500">Total prospections</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
                <p class="text-2xl font-bold text-blue-600">{{ $envoyees }}</p>
                <p class="text-sm text-gray-500">En attente</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
                <p class="text-2xl font-bold text-green-600">{{ $repondues }}</p>
                <p class="text-sm text-gray-500">Répondues</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
                <p class="text-2xl font-bold text-yellow-600">{{ $enCours }}</p>
                <p class="text-sm text-gray-500">En cours</p>
            </div>
        </div>

        {{-- LISTE DES PROSPECTIONS --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            @if($prospections->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucune prospection</h3>
                    <p class="text-gray-500 mb-6">Commencez par contacter un resort potentiel</p>
                    <a href="{{ route('marketing.prospection.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nouvelle prospection
                    </a>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Resort</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Localisation</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Objet</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Statut</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Date</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($prospections as $prospection)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $prospection->nom_resort }}</div>
                                    <div class="text-sm text-gray-500">{{ $prospection->email_resort }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($prospection->ville || $prospection->pays)
                                        <div class="text-sm text-gray-600">
                                            {{ $prospection->ville }}{{ $prospection->ville && $prospection->pays ? ', ' : '' }}{{ $prospection->pays }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-xs truncate">{{ $prospection->objet }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $color = $prospection->statut_color;
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $color }}-100 text-{{ $color }}-800">
                                        @if($prospection->statut === 'envoyee')
                                            📨
                                        @elseif($prospection->statut === 'repondue')
                                            ✅
                                        @elseif($prospection->statut === 'en_cours')
                                            ⏳
                                        @else
                                            📁
                                        @endif
                                        {{ $prospection->statut_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $prospection->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $prospection->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('marketing.prospection.show', $prospection->numprospection) }}" class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition">
                                        👁️ Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if($prospections->hasPages())
                    <div class="px-6 py-4 bg-slate-50 border-t">
                        {{ $prospections->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
