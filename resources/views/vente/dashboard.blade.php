@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-7xl mx-auto">
        {{-- En-tête --}}
        <div class="mb-12">
            <h1 class="text-4xl font-bold mb-2">Tableau de Bord Service Vente</h1>
            <p class="text-gray-600">Gérez les confirmations de séjours et les validations des partenaires</p>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm">Réservations en attente</p>
                        <p class="text-3xl font-bold text-red-600">{{ $stats['total_pending'] }}</p>
                    </div>
                    <div class="text-4xl text-red-200">
                        <i class="fas fa-clock"></i>
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
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_upcoming'] }}</p>
                    </div>
                    <div class="text-4xl text-blue-200">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réservations en attente de confirmation --}}
        <div class="bg-white rounded-lg shadow mb-12">
            <div class="border-b p-6">
                <h2 class="text-2xl font-bold">Réservations en Attente de Confirmation</h2>
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
                                        <span class="font-bold text-blue-600">#{{ $reservation->numreservation }}</span>
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
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('stay-confirmation.form', $reservation->numreservation) }}" 
                                           class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold transition">
                                            Confirmer
                                        </a>
                                            <a href="{{ route('vente.reject-form', $reservation->numreservation) }}" 
                                               class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-bold transition">
                                                Rejeter
                                            </a>
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
                <h2 class="text-2xl font-bold">Confirmations Récentes</h2>
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
