@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-clubmed-beige py-8">
    <div class="max-w-2xl mx-auto px-4">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('profile.personal-data') }}" class="inline-flex items-center text-clubmed-blue hover:text-clubmed-blue-dark mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à mes données
            </a>
            <h1 class="text-3xl font-serif font-bold text-red-600">Supprimer mon compte</h1>
            <p class="text-gray-600 mt-2">Cette action est irréversible. Toutes vos données seront supprimées.</p>
        </div>

        {{-- Messages --}}
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Avertissement réservations actives --}}
        @if($activeReservations > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <svg class="w-6 h-6 text-yellow-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-yellow-800">Réservations en cours détectées</p>
                        <p class="text-yellow-700 text-sm">Vous avez {{ $activeReservations }} réservation(s) en cours ou à venir. Vous devez les annuler avant de pouvoir supprimer votre compte.</p>
                        <a href="{{ route('reservations.index') }}" class="text-yellow-800 font-medium underline text-sm mt-2 inline-block">Gérer mes réservations →</a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire de suppression --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-red-600 px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Demande de suppression de compte
                </h2>
            </div>
            
            <div class="p-6">
                {{-- Ce qui sera supprimé --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h3 class="font-bold text-gray-900 mb-3">Ce qui sera supprimé :</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Vos informations personnelles (nom, email, téléphone, adresse)
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Votre historique de réservations
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Vos avis et commentaires
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Vos signalements et préférences
                        </li>
                    </ul>
                </div>

                {{-- Délai de grâce --}}
                <div class="bg-blue-50 rounded-xl p-4 mb-6">
                    <h3 class="font-bold text-blue-900 mb-2">📅 Délai de 30 jours</h3>
                    <p class="text-sm text-blue-700">
                        Votre compte sera définitivement supprimé 30 jours après votre demande. 
                        Pendant ce délai, vous pouvez annuler la suppression en vous reconnectant.
                    </p>
                </div>

                <form action="{{ route('profile.request-deletion') }}" method="POST">
                    @csrf
                    
                    {{-- Raison (optionnelle) --}}
                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Pourquoi souhaitez-vous supprimer votre compte ? (optionnel)
                        </label>
                        <textarea 
                            id="reason" 
                            name="reason" 
                            rows="3" 
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Votre avis nous aide à améliorer nos services...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmez votre mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Votre mot de passe actuel">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmation --}}
                    <div class="mb-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="confirm_deletion" 
                                value="1"
                                class="mt-1 w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                required>
                            <span class="text-sm text-gray-700">
                                Je comprends que cette action est <strong>irréversible</strong> et que toutes mes données seront 
                                <strong>définitivement supprimées</strong> après 30 jours.
                            </span>
                        </label>
                        @error('confirm_deletion')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-4">
                        <a href="{{ route('profile.personal-data') }}" class="flex-1 py-3 px-6 text-center border border-gray-300 text-gray-700 rounded-full font-medium hover:bg-gray-50 transition-colors">
                            Annuler
                        </a>
                        <button 
                            type="submit" 
                            class="flex-1 py-3 px-6 bg-red-600 hover:bg-red-700 text-white rounded-full font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            @if($activeReservations > 0) disabled @endif>
                            Supprimer mon compte
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Alternative --}}
        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Vous souhaitez simplement faire une pause ? 
                <a href="#" class="text-clubmed-blue font-medium hover:underline">Désactivez temporairement votre compte</a>
            </p>
        </div>
    </div>
</div>
@endsection
