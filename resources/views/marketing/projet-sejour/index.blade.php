@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('marketing.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                        ← Retour au tableau de bord
                    </a>
                    <h1 class="font-serif text-3xl text-clubmed-blue font-bold">📋 Projets de Séjour</h1>
                    <p class="text-slate-500 mt-1">Gérez vos propositions de nouveaux séjours à soumettre au Directeur des Ventes</p>
                </div>
                <a href="{{ route('marketing.projet-sejour.create') }}" class="px-6 py-3 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg font-bold transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nouveau Projet
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-gray-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Brouillons</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['brouillon'] }}</p>
                    </div>
                    <span class="text-3xl">📝</span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">En attente d'approbation</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['soumis'] }}</p>
                    </div>
                    <span class="text-3xl">📤</span>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Approuvés</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['approuve'] }}</p>
                    </div>
                    <span class="text-3xl">✅</span>
                </div>
            </div>
        </div>

        {{-- LISTE DES PROJETS --}}
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            @if($projets->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Aucun projet de séjour</h3>
                    <p class="text-gray-500 mb-6">Créez votre premier projet basé sur les réponses positives de vos prospections</p>
                    <a href="{{ route('marketing.projet-sejour.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Créer un projet
                    </a>
                </div>
            @else
                <table class="w-full">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Projet</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Destination</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Tridents</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">Statut</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">Créé par</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($projets as $projet)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $projet->nom_sejour }}</div>
                                <div class="text-sm text-gray-500">Créé le {{ $projet->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800">{{ $projet->pays }}</div>
                                @if($projet->ville)
                                    <div class="text-sm text-gray-500">{{ $projet->ville }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-yellow-500">
                                    @for($i = 0; $i < $projet->nb_tridents; $i++)🔱@endfor
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $projet->statut_color }}-100 text-{{ $projet->statut_color }}-800">
                                    {{ $projet->statut_icon }} {{ $projet->statut_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $projet->createur->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('marketing.projet-sejour.show', $projet->numprojet) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg text-sm font-medium transition">
                                    Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $projets->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
