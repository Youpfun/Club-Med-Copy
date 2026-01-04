@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg text-center">
                <h1 class="text-2xl font-bold text-white">📋 Demande de Disponibilité</h1>
                <p class="text-blue-100 mt-1">Club Méditerranée - Service Marketing</p>
            </div>

            <div class="p-8">
                {{-- Informations de la demande --}}
                <div class="bg-blue-50 rounded-lg p-6 mb-6">
                    <h2 class="text-lg font-bold text-blue-800 mb-4">Détails de la demande</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Resort</p>
                            <p class="font-bold">{{ $demande->resort->nomresort ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Demandeur</p>
                            <p class="font-bold">{{ $demande->user->name ?? 'Service Marketing' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de début</p>
                            <p class="font-bold">{{ $demande->date_debut->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date de fin</p>
                            <p class="font-bold">{{ $demande->date_fin->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durée</p>
                            <p class="font-bold">{{ $demande->date_debut->diffInDays($demande->date_fin) }} nuits</p>
                        </div>
                        @if($demande->nb_chambres)
                        <div>
                            <p class="text-sm text-gray-500">Chambres demandées</p>
                            <p class="font-bold">{{ $demande->nb_chambres }}</p>
                        </div>
                        @endif
                        @if($demande->nb_personnes)
                        <div>
                            <p class="text-sm text-gray-500">Personnes prévues</p>
                            <p class="font-bold">{{ $demande->nb_personnes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($demande->message)
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded">
                        <h3 class="font-bold text-yellow-800">💬 Message du service Marketing</h3>
                        <p class="text-gray-700 mt-2">{!! nl2br(e($demande->message)) !!}</p>
                    </div>
                @endif

                {{-- Formulaire de réponse --}}
                <form action="{{ url('/resort/disponibilite/' . $token) }}" method="POST">
                    @csrf

                    <h2 class="text-lg font-bold text-gray-800 mb-4">Votre réponse</h2>

                    {{-- Statut de disponibilité --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Disponibilité pour cette période <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="response_status" value="available" required class="mr-3 text-green-600">
                                <div>
                                    <p class="font-bold text-green-700">✅ Disponible</p>
                                    <p class="text-sm text-gray-500">Toutes les chambres demandées sont disponibles</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-500 transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                                <input type="radio" name="response_status" value="partially_available" class="mr-3 text-orange-600">
                                <div>
                                    <p class="font-bold text-orange-700">⚠️ Partiellement disponible</p>
                                    <p class="text-sm text-gray-500">Certaines chambres sont disponibles mais pas toutes</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="response_status" value="not_available" class="mr-3 text-red-600">
                                <div>
                                    <p class="font-bold text-red-700">❌ Non disponible</p>
                                    <p class="text-sm text-gray-500">Aucune disponibilité pour cette période</p>
                                </div>
                            </label>
                        </div>
                        @error('response_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre de chambres disponibles --}}
                    <div class="mb-6">
                        <label for="chambres_disponibles" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de chambres disponibles
                        </label>
                        <input type="number" name="chambres_disponibles" id="chambres_disponibles" min="0"
                               placeholder="Ex: 15"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Indiquez le nombre de chambres que vous pouvez proposer</p>
                    </div>

                    {{-- Message --}}
                    <div class="mb-6">
                        <label for="response_message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message (optionnel)
                        </label>
                        <textarea name="response_message" id="response_message" rows="4"
                                  placeholder="Ajoutez des précisions sur les disponibilités, types de chambres, conditions particulières..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    {{-- Votre nom --}}
                    <div class="mb-6">
                        <label for="responded_by" class="block text-sm font-medium text-gray-700 mb-2">
                            Votre nom
                        </label>
                        <input type="text" name="responded_by" id="responded_by"
                               placeholder="Ex: Jean Dupont - Responsable Réservations"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Bouton --}}
                    <div class="text-center">
                        <button type="submit" 
                                class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-lg transition">
                            📤 Envoyer ma réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
