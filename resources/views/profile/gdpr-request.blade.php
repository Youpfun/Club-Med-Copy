@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-clubmed-beige py-8">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('profile.personal-data') }}" class="inline-flex items-center text-clubmed-blue hover:text-clubmed-blue-dark mb-4 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour aux données personnelles
            </a>
            <h1 class="text-3xl font-serif font-bold text-clubmed-blue">Gestion de vos données RGPD</h1>
            <p class="text-gray-600 mt-2">Conformément au Règlement Général sur la Protection des Données (RGPD), vous pouvez exercer vos droits sur vos données personnelles.</p>
        </div>

        {{-- Messages de succès/erreur --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Information RGPD --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-bold mb-2">Vos droits RGPD</p>
                    <ul class="space-y-1 list-disc list-inside">
                        <li><strong>Droit à la suppression :</strong> Vos données seront définitivement supprimées de nos serveurs.</li>
                        <li><strong>Droit à l'anonymisation :</strong> Vos données identifiables seront remplacées par des données anonymes, préservant les statistiques tout en protégeant votre identité.</li>
                    </ul>
                    <p class="mt-3 text-xs">Ces actions sont <strong>irréversibles</strong>. Assurez-vous d'avoir exporté vos données si nécessaire.</p>
                </div>
            </div>
        </div>

        {{-- Vérification des réservations actives --}}
        @if($activeReservations > 0)
            <div class="bg-orange-50 border-l-4 border-orange-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-orange-700">Attention : Réservations actives</p>
                        <p class="text-orange-600 text-sm">Vous avez {{ $activeReservations }} réservation(s) en cours. Pour la suppression complète, vous devez d'abord annuler vos réservations actives.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire de demande RGPD --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-clubmed-blue px-6 py-4">
                <h2 class="text-lg font-bold text-white">Choisissez une action</h2>
            </div>

            <form action="{{ route('profile.process-gdpr-request') }}" method="POST" class="p-6">
                @csrf

                {{-- Type de demande --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Type de demande *</label>
                    <div class="space-y-4">
                        {{-- Option Anonymisation --}}
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-clubmed-blue transition-colors" onclick="updateRequestType('anonymize')">
                            <input type="radio" name="request_type" value="anonymize" class="mt-1" required>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-900">Anonymisation de mes données</span>
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">Recommandé</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Vos informations personnelles identifiables (nom, email, téléphone, adresse) seront remplacées par des données anonymes. 
                                    Vos réservations et avis resteront dans le système de manière anonyme pour les statistiques.
                                </p>
                                <ul class="text-xs text-gray-500 mt-2 space-y-1">
                                    <li>✓ Préserve l'historique statistique</li>
                                    <li>✓ Conforme RGPD (Art. 17)</li>
                                    <li>✓ Nom → "Utilisateur Anonyme #XXX"</li>
                                    <li>✓ Email → "anonyme[id]@example.com"</li>
                                    <li>✓ Date de naissance → Année uniquement</li>
                                </ul>
                            </div>
                        </label>

                        {{-- Option Suppression --}}
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-500 transition-colors" onclick="updateRequestType('delete')">
                            <input type="radio" name="request_type" value="delete" class="mt-1" required>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-900">Suppression complète de mon compte</span>
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Irréversible</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Toutes vos données personnelles seront définitivement supprimées de nos serveurs. 
                                    Cette action est irréversible et votre compte ne pourra pas être récupéré.
                                </p>
                                <ul class="text-xs text-gray-500 mt-2 space-y-1">
                                    <li>✗ Suppression définitive</li>
                                    <li>✗ Perte de l'historique</li>
                                    <li>✗ Impossible de restaurer</li>
                                    @if($activeReservations > 0)
                                        <li class="text-orange-600">⚠ Nécessite l'annulation des réservations actives</li>
                                    @endif
                                </ul>
                            </div>
                        </label>
                    </div>
                    @error('request_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Raison (optionnelle) --}}
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-bold text-gray-700 mb-2">Raison de votre demande (optionnel)</label>
                    <textarea 
                        name="reason" 
                        id="reason" 
                        rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-clubmed-blue focus:border-transparent"
                        placeholder="Vous pouvez nous expliquer pourquoi vous souhaitez exercer ce droit..."
                    >{{ old('reason') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Ces informations nous aident à améliorer nos services.</p>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation par mot de passe --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Confirmez votre mot de passe *</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-clubmed-blue focus:border-transparent"
                        placeholder="Entrez votre mot de passe pour confirmer"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation finale --}}
                <div class="mb-6">
                    <label class="flex items-start">
                        <input 
                            type="checkbox" 
                            name="confirm_action" 
                            value="1" 
                            required
                            class="mt-1 h-5 w-5 text-clubmed-blue border-gray-300 rounded focus:ring-clubmed-blue"
                        >
                        <span class="ml-3 text-sm text-gray-700">
                            Je comprends que cette action est <strong class="text-red-600">irréversible</strong> et j'ai pris connaissance de mes droits RGPD. 
                            J'ai exporté mes données si nécessaire.
                        </span>
                    </label>
                    @error('confirm_action')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Boutons d'action --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('profile.personal-data') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="px-6 py-3 bg-clubmed-blue hover:bg-clubmed-blue-dark text-white rounded-lg font-medium transition-colors flex items-center"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Confirmer ma demande
                    </button>
                </div>
            </form>
        </div>

        {{-- Informations complémentaires --}}
        <div class="mt-8 bg-white rounded-xl p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-3">Besoin d'aide ?</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p>• <strong>Avant la suppression :</strong> Exportez vos données via la <a href="{{ route('profile.personal-data') }}" class="text-clubmed-blue hover:underline">page des données personnelles</a>.</p>
                <p>• <strong>Délai de traitement :</strong> Votre demande sera traitée dans un délai maximum de 30 jours conformément au RGPD.</p>
                <p>• <strong>Questions :</strong> Contactez notre délégué à la protection des données à <a href="mailto:dpo@clubmed.com" class="text-clubmed-blue hover:underline">dpo@clubmed.com</a></p>
            </div>
        </div>
    </div>
</div>

<script>
function updateRequestType(type) {
    const submitBtn = document.getElementById('submitBtn');
    const radio = document.querySelector(`input[value="${type}"]`);
    
    if (radio) {
        radio.checked = true;
    }
    
    if (type === 'delete') {
        submitBtn.classList.remove('bg-clubmed-blue', 'hover:bg-clubmed-blue-dark');
        submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
        submitBtn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Supprimer définitivement
        `;
    } else {
        submitBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        submitBtn.classList.add('bg-clubmed-blue', 'hover:bg-clubmed-blue-dark');
        submitBtn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Anonymiser mes données
        `;
    }
}

// Mise à jour du bouton au changement de radio
document.querySelectorAll('input[name="request_type"]').forEach(radio => {
    radio.addEventListener('change', (e) => {
        updateRequestType(e.target.value);
    });
});
</script>
@endsection
