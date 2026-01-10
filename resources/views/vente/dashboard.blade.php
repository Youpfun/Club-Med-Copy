@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-7xl mx-auto">
        {{-- En-tete --}}
        <div class="mb-12">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold font-serif text-clubmed-blue mb-2">Tableau de Bord Service Vente</h1>
                    <p class="text-gray-600">Gerez les confirmations de sejours et les validations des partenaires</p>
                </div>
                <a href="{{ route('vente.avis') }}" 
                   class="px-6 py-3 bg-clubmed-blue text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    Gerer les Avis
                </a>
            </div>
               {{-- Messages de session --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✓ Succès!</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>✗ Erreur!</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Réservations en attente</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $stats['total_pending'] }}</p>
                    </div>
                    <div class="text-4xl text-yellow-200">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Resorts indisponibles</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['total_resort_refused'] }}</p>
                    </div>
                    <div class="text-4xl text-red-200">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Activités sans réponse</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_activities_pending'] ?? 0 }}</p>
                    </div>
                    <div class="text-4xl text-purple-200">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Confirmations effectuées</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_confirmed'] }}</p>
                    </div>
                    <div class="text-4xl text-green-200">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Séjours à venir</p>
                        <p class="text-3xl font-bold text-clubmed-blue">{{ $stats['total_upcoming'] }}</p>
                    </div>
                    <div class="text-4xl text-clubmed-blue/30">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réservations nécessitant une proposition alternative --}}
        @if($reservationsResortRefused->isNotEmpty())
        <div class="bg-white rounded-lg shadow mb-12 border-l-4 border-red-500">
            <div class="border-b p-6 bg-red-50">
                <h2 class="text-2xl font-bold font-serif text-red-800">🚨 Resorts Indisponibles - Action Requise</h2>
                <p class="text-red-600 text-sm mt-1">Ces réservations nécessitent la proposition d'un resort alternatif au client</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-red-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Réservation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Resort Initial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Dates</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Statut Alternative</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($reservationsResortRefused as $reservation)
                            <tr class="hover:bg-red-50">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-red-600">#{{ $reservation->numreservation }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium">{{ $reservation->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium line-through text-gray-500">{{ $reservation->resort->nomresort ?? 'N/A' }}</p>
                                        <span class="text-xs text-red-600">❌ Indisponible</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($reservation->alternative_resort_status === 'none')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800">
                                            Aucune proposition
                                        </span>
                                    @elseif($reservation->alternative_resort_status === 'proposed')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800">
                                            ⏳ En attente réponse client
                                        </span>
                                        @if($reservation->alternativeResort)
                                            <p class="text-xs text-gray-500 mt-1">→ {{ $reservation->alternativeResort->nomresort }}</p>
                                        @endif
                                    @elseif($reservation->alternative_resort_status === 'refused')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800">
                                            ❌ Refusé par client
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($reservation->alternative_resort_status !== 'proposed')
                                        <a href="{{ route('vente.propose-alternative-form', $reservation->numreservation) }}" 
                                           class="inline-block px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded font-bold transition">
                                            🔄 Proposer Alternative
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-500">En attente...</span>
                                    @endif
                                    <a href="{{ route('vente.reject-form', $reservation->numreservation) }}" 
                                       class="inline-block px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded font-bold transition mt-2">
                                        Annuler
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Activités en attente de réponse partenaire --}}
        @if(isset($reservationsActivitiesPending) && $reservationsActivitiesPending->isNotEmpty())
        <div class="bg-white rounded-lg shadow mb-12 border-l-4 border-purple-500">
            <div class="border-b p-6 bg-purple-50">
                <h2 class="text-2xl font-bold font-serif text-purple-800">Activités Sans Réponse Partenaire</h2>
                <p class="text-purple-600 text-sm mt-1">Ces activités n'ont pas reçu de confirmation du partenaire. Vous pouvez les annuler et rembourser le client.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-purple-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Réservation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Resort</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Activités en attente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($reservationsActivitiesPending as $reservation)
                            @php
                                $totalPendingAmount = $reservation->activites_pending->sum(function($a) {
                                    return $a->prix_unitaire * $a->quantite;
                                });
                            @endphp
                            <tr class="hover:bg-purple-50">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-purple-600">#{{ $reservation->numreservation }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium">{{ $reservation->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $reservation->resort->nomresort ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        @foreach($reservation->activites_pending as $activity)
                                            <div class="text-sm bg-purple-100 rounded p-2">
                                                <p class="font-medium">{{ $activity->nomactivite }}</p>
                                                <p class="text-xs text-gray-600">
                                                    Partenaire: {{ $activity->nompartenaire ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $activity->quantite }}x {{ number_format($activity->prix_unitaire, 2, ',', ' ') }} €
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-purple-700">
                                        {{ number_format($totalPendingAmount, 2, ',', ' ') }} €
                                    </span>
                                    <p class="text-xs text-gray-500">à rembourser</p>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('vente.cancel-pending-partner-activities', $reservation->numreservation) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler {{ $reservation->activites_pending->count() }} activité(s) et procéder au remboursement de {{ number_format($totalPendingAmount, 2, ',', ' ') }} € ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded font-bold transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Annuler et Rembourser
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Réservations en attente de confirmation --}}
        <div class="bg-white rounded-lg shadow mb-12">
            <div class="border-b p-6">
                <h2 class="text-2xl font-bold font-serif text-clubmed-blue">Réservations en Attente de Confirmation</h2>
                <p class="text-gray-600 text-sm mt-1">Confirmez les séjours dont les partenaires ont validé la disponibilité</p>
            </div>

            @if($reservationsPendingConfirmation->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <p class="mb-4">Aucune réservation en attente</p>
                    <p class="text-sm">Tous les séjours ont été confirmés ✓</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Réservation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Resort</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($reservationsPendingConfirmation as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-clubmed-blue">#{{ $reservation->numreservation }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium">{{ $reservation->user->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $reservation->user->email ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $reservation->resort->nomresort ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full 
                                            @if($reservation->statut === 'en_attente')
                                                bg-yellow-100 text-yellow-800
                                            @elseif($reservation->statut === 'payee')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-gray-100 text-gray-800
                                            @endif
                                        ">
                                            {{ ucfirst($reservation->statut) }}
                                        </span>
                                        
                                        @if($reservation->partenaires_status && $reservation->partenaires_status->count() > 0)
                                            <div class="mt-2 space-y-1">
                                                @foreach($reservation->partenaires_status as $ps)
                                                    <div class="text-xs">
                                                        <span class="font-semibold">{{ $ps->nompartenaire }}:</span>
                                                        @if($ps->partenaire_validation_status === 'accepted')
                                                            <span class="text-green-600">✓ Validé</span>
                                                        @elseif($ps->partenaire_validation_status === 'refused')
                                                            <span class="text-red-600">✗ Refusé</span>
                                                        @else
                                                            <span class="text-orange-600">⏳ En attente</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                   {{ ucfirst($reservation->statut) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm space-y-2">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('stay-confirmation.form', $reservation->numreservation) }}" 
                                               class="inline-block px-4 py-2 bg-clubmed-blue hover:bg-clubmed-darkblue text-white rounded font-bold transition">
                                                Confirmer
                                            </a>
                                            <a href="{{ route('vente.reject-form', $reservation->numreservation) }}" 
                                               class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-bold transition">
                                                Rejeter
                                            </a>
                                        </div>
                                        @if($reservation->activites && $reservation->activites->count() > 0)
                                            <a href="{{ route('vente.activities', $reservation->numreservation) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white rounded text-sm transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                Activités ({{ $reservation->activites->count() }})
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t">
                    {{ $reservationsPendingConfirmation->links() }}
                </div>
            @endif
        </div>

        {{-- Confirmations récentes --}}
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-6">
                <h2 class="text-2xl font-bold font-serif text-clubmed-blue">Confirmations Récentes</h2>
                <p class="text-gray-600 text-sm mt-1">Les 10 derniers séjours confirmés</p>
            </div>

            @if($confirmedReservations->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <p>Aucune confirmation enregistrée</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Réservation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Resort</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Confirmée le</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($confirmedReservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-green-600">#{{ $reservation->numreservation }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $reservation->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $reservation->resort->nomresort ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if(!empty($reservation->confirmed_at))
                                            {{ \Carbon\Carbon::parse($reservation->confirmed_at)->format('d/m/Y H:i') }}
                                        @else
                                            {{ $reservation->datedebut->format('d/m/Y') }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
