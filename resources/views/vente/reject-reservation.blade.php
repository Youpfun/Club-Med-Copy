@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-8 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-6 border-b pb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Rejeter une Réservation</h1>
                <p class="text-gray-600">Enregistrer le refus du client et initier le remboursement</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-red-800 font-semibold mb-2">Erreurs détectées</h3>
                    <ul class="text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-blue-900 font-semibold mb-3">Détails de la Réservation</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 font-semibold">N° de réservation</p>
                        <p class="text-lg text-gray-900">{{ $reservation->numreservation }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Client</p>
                        <p class="text-lg text-gray-900">{{ $reservation->user->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Resort</p>
                        <p class="text-lg text-gray-900">{{ $reservation->resort->nomresort ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Prix Total</p>
                        <p class="text-lg text-gray-900 font-bold">{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Dates</p>
                        <p class="text-lg text-gray-900">{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold">Statut Actuel</p>
                        <p class="text-lg text-gray-900">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $reservation->statut === 'payee' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($reservation->statut) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <form action="{{ route('vente.reject', $reservation->numreservation) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="reason" class="block text-sm font-semibold text-gray-900 mb-3">
                        Motif du Rejet <span class="text-red-600">*</span>
                    </label>
                    <select name="reason" id="reason" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="client_refused" selected>Client a refusé la réservation</option>
                        <option value="new_resort_not_accepted">Client a refusé le nouveau resort proposé</option>
                        <option value="availability_issue">Problème de disponibilité</option>
                        <option value="other">Autre</option>
                    </select>
                    @error('reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="refund_amount" class="block text-sm font-semibold text-gray-900 mb-3">
                        Montant du Remboursement (€)
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="refund_amount" 
                            id="refund_amount" 
                            step="0.01"
                            min="0"
                            value="{{ old('refund_amount', $reservation->prixtotal) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="0.00"
                        >
                        <span class="absolute right-4 top-3 text-gray-600 font-semibold">€</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Montant maximal: <strong>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</strong></p>
                    @error('refund_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-3">
                        Commentaires Supplémentaires (Optionnel)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="5"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="Détails additionnels sur le rejet..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="text-yellow-900 font-semibold mb-2">⚠️ Attention</h3>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• Cette action marquera la réservation comme "rejetée"</li>
                        <li>• Le remboursement sera enregistré avec le statut "en attente"</li>
                        <li>• Assurez-vous que le montant du remboursement est correct</li>
                    </ul>
                </div>

                <div class="flex gap-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition"
                    >
                        ✓ Confirmer le Rejet et Initier le Remboursement
                    </button>
                    <a 
                        href="{{ route('vente.dashboard') }}" 
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition text-center"
                    >
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
