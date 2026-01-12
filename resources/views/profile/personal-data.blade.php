@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-clubmed-beige py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('profile.show') }}" class="inline-flex items-center text-clubmed-blue hover:text-clubmed-blue-dark mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au profil
            </a>
            <h1 class="text-3xl font-serif font-bold text-clubmed-blue">Mes données personnelles</h1>
            <p class="text-gray-600 mt-2">Conformément au RGPD, vous pouvez consulter, exporter et demander la suppression de vos données.</p>
        </div>

        {{-- Alerte si suppression en attente --}}
        @if($user->deletion_requested_at)
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-bold text-red-700">Suppression programmée</p>
                        <p class="text-red-600 text-sm">Votre compte sera supprimé le {{ \Carbon\Carbon::parse($user->deletion_requested_at)->addDays(30)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <form action="{{ route('profile.cancel-deletion') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900 underline">
                        Annuler la suppression
                    </button>
                </form>
            </div>
        @endif

        {{-- Actions rapides --}}
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('profile.gdpr-request') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">Gérer mes données RGPD</p>
                    <p class="text-sm text-gray-500">Anonymiser ou supprimer</p>
                </div>
            </a>

            <a href="{{ route('profile.export-data') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">Exporter mes données</p>
                    <p class="text-sm text-gray-500">Télécharger au format JSON</p>
                </div>
            </a>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-900">Données sécurisées</p>
                    <p class="text-sm text-gray-500">Conformité RGPD</p>
                </div>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Résumé de vos données</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-clubmed-blue">{{ $stats['reservations_count'] }}</p>
                    <p class="text-sm text-gray-600">Réservations</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-clubmed-blue">{{ $stats['avis_count'] }}</p>
                    <p class="text-sm text-gray-600">Avis publiés</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-clubmed-blue">{{ $stats['signalements_count'] }}</p>
                    <p class="text-sm text-gray-600">Signalements</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <p class="text-3xl font-bold text-clubmed-blue">{{ $stats['remboursements_count'] }}</p>
                    <p class="text-sm text-gray-600">Remboursements</p>
                </div>
            </div>
        </div>

        {{-- Informations personnelles --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
            <div class="bg-clubmed-blue px-6 py-4">
                <h2 class="text-lg font-bold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informations du compte
                </h2>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Nom complet</label>
                        <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Genre</label>
                        <p class="text-gray-900 font-medium">{{ $user->genre ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date de naissance</label>
                        <p class="text-gray-900 font-medium">{{ $user->datenaissance ? \Carbon\Carbon::parse($user->datenaissance)->format('d/m/Y') : 'Non renseignée' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Téléphone</label>
                        <p class="text-gray-900 font-medium">{{ $user->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Rôle</label>
                        <p class="text-gray-900 font-medium">{{ $user->role ?? 'Utilisateur' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Adresse</label>
                        <p class="text-gray-900 font-medium">
                            @if($user->numrue || $user->nomrue || $user->codepostal || $user->ville)
                                {{ $user->numrue }} {{ $user->nomrue }}, {{ $user->codepostal }} {{ $user->ville }}
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Compte créé le</label>
                        <p class="text-gray-900 font-medium">{{ $user->created_at ? $user->created_at->format('d/m/Y à H:i') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Email vérifié</label>
                        <p class="text-gray-900 font-medium">
                            @if($user->email_verified_at)
                                <span class="text-green-600">✓ Oui ({{ $user->email_verified_at->format('d/m/Y') }})</span>
                            @else
                                <span class="text-red-600">✗ Non</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réservations --}}
        @if($reservations->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="bg-clubmed-blue px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Mes réservations ({{ $reservations->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-500 border-b">
                                    <th class="pb-3 font-medium">N°</th>
                                    <th class="pb-3 font-medium">Resort</th>
                                    <th class="pb-3 font-medium">Dates</th>
                                    <th class="pb-3 font-medium">Personnes</th>
                                    <th class="pb-3 font-medium">Prix</th>
                                    <th class="pb-3 font-medium">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($reservations as $res)
                                    <tr class="text-sm">
                                        <td class="py-3 font-medium">{{ $res->numreservation }}</td>
                                        <td class="py-3">{{ $res->resort->nomresort ?? 'N/A' }}</td>
                                        <td class="py-3">{{ \Carbon\Carbon::parse($res->datedebut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($res->datefin)->format('d/m/Y') }}</td>
                                        <td class="py-3">{{ $res->nbpersonnes }}</td>
                                        <td class="py-3 font-medium">{{ number_format($res->prixtotal ?? 0, 2, ',', ' ') }} €</td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($res->statut === 'confirmee' || $res->statut === 'payee') bg-green-100 text-green-700
                                                @elseif($res->statut === 'en_attente') bg-yellow-100 text-yellow-700
                                                @elseif($res->statut === 'annulee') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $res->statut ?? 'N/A')) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Avis --}}
        @if($avis->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="bg-clubmed-blue px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Mes avis ({{ $avis->count() }})
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($avis as $unAvis)
                        <div class="border border-gray-100 rounded-xl p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $unAvis->resort->nomresort ?? 'Resort inconnu' }}</p>
                                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($unAvis->datepublication)->format('d/m/Y') }}</p>
                                </div>
                                <div class="flex text-clubmed-gold">
                                    @for($i = 0; $i < $unAvis->noteavis; $i++) ★ @endfor
                                    @for($i = $unAvis->noteavis; $i < 5; $i++) <span class="text-gray-300">★</span> @endfor
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm">{{ $unAvis->commentaireavis }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Signalements --}}
        @if($signalements->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="bg-clubmed-blue px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Mes signalements ({{ $signalements->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($signalements as $signalement)
                            <div class="border border-gray-100 rounded-lg p-3 text-sm">
                                <p class="text-gray-600">{{ $signalement->message }}</p>
                                <p class="text-gray-400 text-xs mt-1">{{ $signalement->datesignalement ? $signalement->datesignalement->format('d/m/Y H:i') : 'Date inconnue' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Remboursements --}}
        @if($remboursements->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="bg-clubmed-blue px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Mes remboursements ({{ $remboursements->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-500 border-b">
                                    <th class="pb-3 font-medium">Date</th>
                                    <th class="pb-3 font-medium">Montant</th>
                                    <th class="pb-3 font-medium">Raison</th>
                                    <th class="pb-3 font-medium">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($remboursements as $remb)
                                    <tr class="text-sm">
                                        <td class="py-3">{{ $remb->created_at->format('d/m/Y') }}</td>
                                        <td class="py-3 font-medium">{{ number_format($remb->montant, 2, ',', ' ') }} €</td>
                                        <td class="py-3">{{ $remb->raison ?? 'N/A' }}</td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($remb->statut === 'effectue') bg-green-100 text-green-700
                                                @elseif($remb->statut === 'en_attente') bg-yellow-100 text-yellow-700
                                                @else bg-gray-100 text-gray-700 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $remb->statut ?? 'N/A')) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Informations RGPD --}}
        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
            <h3 class="font-bold text-blue-900 mb-2">Vos droits RGPD</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>✓ <strong>Droit d'accès</strong> : Vous pouvez consulter toutes vos données sur cette page</li>
                <li>✓ <strong>Droit de portabilité</strong> : Vous pouvez exporter vos données au format JSON</li>
                <li>✓ <strong>Droit à l'effacement</strong> : Vous pouvez demander la suppression de votre compte</li>
                <li>✓ <strong>Droit de rectification</strong> : Vous pouvez modifier vos informations depuis votre profil</li>
            </ul>
            <p class="text-sm text-blue-600 mt-4">
                Pour toute question concernant vos données, contactez-nous à 
                <a href="mailto:privacy@clubmed.com" class="underline font-medium">privacy@clubmed.com</a>
            </p>
        </div>
    </div>
</div>
@endsection
