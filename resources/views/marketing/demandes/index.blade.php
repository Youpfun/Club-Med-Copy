@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-6xl mx-auto">
        {{-- En-tête --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('marketing.dashboard') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    ← Retour au Dashboard
                </a>
                <h1 class="text-3xl font-bold">Demandes de Disponibilité</h1>
                <p class="text-gray-600">Suivez vos demandes envoyées aux resorts</p>
            </div>
            <a href="{{ route('marketing.demandes.create') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle demande
            </a>
        </div>

        {{-- Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✓</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>✗</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Liste des demandes --}}
        <div class="bg-white rounded-lg shadow">
            @if($demandes->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-lg font-semibold">Aucune demande de disponibilité</p>
                    <p class="text-sm mt-2">Créez votre première demande pour vérifier les disponibilités d'un resort.</p>
                    <a href="{{ route('marketing.demandes.create') }}" 
                       class="inline-block mt-4 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold transition">
                        Créer une demande
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Resort</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Période</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Chambres</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Réponse</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($demandes as $demande)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-blue-600">#{{ $demande->numdemande }}</span>
                                        <p class="text-xs text-gray-500">{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-medium">{{ $demande->resort->nomresort ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $demande->resort->pays->nompays ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $demande->date_debut->format('d/m/Y') }} - {{ $demande->date_fin->format('d/m/Y') }}
                                        <p class="text-xs text-gray-500">{{ $demande->date_debut->diffInDays($demande->date_fin) }} nuits</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $demande->nb_chambres ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($demande->statut === 'pending')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ En attente
                                            </span>
                                        @elseif($demande->statut === 'responded')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">
                                                ✓ Répondu
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800">
                                                {{ $demande->statut }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($demande->response_status === 'available')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">
                                                ✓ Disponible
                                            </span>
                                        @elseif($demande->response_status === 'partially_available')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-orange-100 text-orange-800">
                                                ⚠️ Partiellement
                                            </span>
                                        @elseif($demande->response_status === 'not_available')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">
                                                ✗ Indisponible
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('marketing.demandes.show', $demande->numdemande) }}" 
                                           class="inline-block px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm transition">
                                            Voir
                                        </a>
                                        @if($demande->statut === 'pending')
                                            <form action="{{ route('marketing.demandes.resend', $demande->numdemande) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition">
                                                    Renvoyer
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t">
                    {{ $demandes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
