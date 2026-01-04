@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-3xl mx-auto">
        {{-- En-tête --}}
        <a href="{{ route('marketing.demandes.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Retour à la liste
        </a>

        {{-- Détails de la demande --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b p-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Demande #{{ $demande->numdemande }}</h1>
                        <p class="text-blue-100 mt-1">Créée le {{ $demande->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @if($demande->statut === 'pending')
                        <span class="px-4 py-2 bg-yellow-400 text-yellow-900 rounded-full font-bold text-sm">
                            ⏳ En attente de réponse
                        </span>
                    @elseif($demande->statut === 'responded')
                        <span class="px-4 py-2 bg-green-400 text-green-900 rounded-full font-bold text-sm">
                            ✓ Répondu
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                {{-- Informations Resort --}}
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <span class="mr-2">🏨</span> Resort concerné
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-semibold text-lg">{{ $demande->resort->nomresort ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $demande->resort->pays->nompays ?? '' }}</p>
                        @if($demande->resort->emailresort)
                            <p class="text-sm text-gray-500 mt-1">📧 {{ $demande->resort->emailresort }}</p>
                        @endif
                    </div>
                </div>

                {{-- Période demandée --}}
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <span class="mr-2">📅</span> Période demandée
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500">Début</p>
                            <p class="font-bold text-lg">{{ $demande->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500">Fin</p>
                            <p class="font-bold text-lg">{{ $demande->date_fin->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500">Durée</p>
                            <p class="font-bold text-lg">{{ $demande->date_debut->diffInDays($demande->date_fin) }} nuits</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-500">Chambres</p>
                            <p class="font-bold text-lg">{{ $demande->nb_chambres ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Message envoyé --}}
                @if($demande->message)
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                            <span class="mr-2">💬</span> Votre message
                        </h2>
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                            <p class="text-gray-700">{!! nl2br(e($demande->message)) !!}</p>
                        </div>
                    </div>
                @endif

                {{-- Demandeur --}}
                <div class="text-sm text-gray-500">
                    Demande effectuée par <strong>{{ $demande->user->name ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>

        {{-- Réponse du Resort --}}
        @if($demande->statut === 'responded')
            <div class="bg-white rounded-lg shadow">
                <div class="border-b p-6 
                    @if($demande->response_status === 'available')
                        bg-gradient-to-r from-green-500 to-green-600
                    @elseif($demande->response_status === 'partially_available')
                        bg-gradient-to-r from-orange-500 to-orange-600
                    @else
                        bg-gradient-to-r from-red-500 to-red-600
                    @endif
                    rounded-t-lg">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        @if($demande->response_status === 'available')
                            <span class="mr-2">✅</span> Disponible
                        @elseif($demande->response_status === 'partially_available')
                            <span class="mr-2">⚠️</span> Partiellement disponible
                        @else
                            <span class="mr-2">❌</span> Non disponible
                        @endif
                    </h2>
                    <p class="text-white/80 text-sm mt-1">Répondu le {{ $demande->responded_at->format('d/m/Y à H:i') }}</p>
                </div>

                <div class="p-6">
                    @if($demande->response_details && isset($demande->response_details['chambres_disponibles']))
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Chambres disponibles</p>
                            <p class="font-bold text-2xl text-blue-600">{{ $demande->response_details['chambres_disponibles'] }}</p>
                        </div>
                    @endif

                    @if($demande->response_message)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="font-bold text-gray-800 mb-2">Message du resort</h3>
                            <p class="text-gray-700">{!! nl2br(e($demande->response_message)) !!}</p>
                        </div>
                    @endif

                    {{-- Actions selon la réponse --}}
                    @if($demande->response_status === 'available' || $demande->response_status === 'partially_available')
                        <div class="mt-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                            <h4 class="font-bold text-green-800">🎉 Bonne nouvelle !</h4>
                            <p class="text-green-700 mt-1">
                                Le resort a indiqué des disponibilités. Vous pouvez maintenant procéder à la création du séjour.
                            </p>
                            <a href="{{ route('resort.create') }}" 
                               class="inline-block mt-3 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold transition">
                                Créer le séjour
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            {{-- En attente --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg">
                <div class="flex items-start">
                    <span class="text-3xl mr-4">⏳</span>
                    <div>
                        <h3 class="font-bold text-yellow-800">En attente de réponse</h3>
                        <p class="text-yellow-700 mt-1">
                            Le resort n'a pas encore répondu à votre demande. 
                            Le lien expire le {{ $demande->validation_token_expires_at->format('d/m/Y') }}.
                        </p>
                        <form action="{{ route('marketing.demandes.resend', $demande->numdemande) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded font-bold transition">
                                📧 Renvoyer la demande
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
