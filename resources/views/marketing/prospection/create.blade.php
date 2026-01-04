@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <a href="{{ route('marketing.prospection.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                ← Retour à la liste
            </a>
            <h1 class="font-serif text-3xl text-[#113559] font-bold">✉️ Nouvelle Prospection Resort</h1>
            <p class="text-slate-500 mt-1">Contacter un resort potentiel pour en savoir plus</p>
        </div>

        {{-- MESSAGES --}}
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">❌</span> {!! session('error') !!}
            </div>
        @endif

        {{-- INFO --}}
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="text-2xl">💡</span>
                <div>
                    <p class="font-medium text-purple-900">À propos de cette fonctionnalité</p>
                    <p class="text-sm text-purple-700 mt-1">
                        Utilisez ce formulaire pour contacter un resort que vous envisagez d'ajouter à notre catalogue. 
                        Un email professionnel sera envoyé au nom du Club Méditerranée pour demander des informations sur l'établissement.
                    </p>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <div class="bg-white rounded-2xl shadow-md p-8">
            <form action="{{ route('marketing.prospection.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Section 1: Informations sur le resort --}}
                <div class="border-b pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Informations sur le resort
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nom du resort --}}
                        <div class="md:col-span-2">
                            <label for="nom_resort" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom du resort <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom_resort" id="nom_resort" 
                                value="{{ old('nom_resort') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition @error('nom_resort') border-red-500 @enderror"
                                placeholder="Ex: Hôtel Paradise Beach Resort"
                                required>
                            @error('nom_resort')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email du resort --}}
                        <div class="md:col-span-2">
                            <label for="email_resort" class="block text-sm font-medium text-gray-700 mb-1">
                                Email du resort <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email_resort" id="email_resort" 
                                value="{{ old('email_resort') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition @error('email_resort') border-red-500 @enderror"
                                placeholder="contact@resort.com"
                                required>
                            @error('email_resort')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">📧 L'email sera envoyé à : clubmedsae@gmail.com (mode développement)</p>
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <input type="text" name="pays" id="pays" 
                                value="{{ old('pays') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="Ex: Maldives">
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="ville" id="ville" 
                                value="{{ old('ville') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="Ex: Malé">
                        </div>

                        {{-- Téléphone --}}
                        <div class="md:col-span-2">
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone (optionnel)</label>
                            <input type="text" name="telephone" id="telephone" 
                                value="{{ old('telephone') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition"
                                placeholder="Ex: +960 123 4567">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Contenu du message --}}
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Votre message
                    </h2>

                    {{-- Objet --}}
                    <div class="mb-4">
                        <label for="objet" class="block text-sm font-medium text-gray-700 mb-1">
                            Objet de l'email <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="objet" id="objet" 
                            value="{{ old('objet', 'Demande d\'information - Partenariat Club Méditerranée') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition @error('objet') border-red-500 @enderror"
                            required>
                        @error('objet')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" id="message" rows="8"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 transition @error('message') border-red-500 @enderror"
                            placeholder="Décrivez ce que vous souhaitez savoir sur ce resort..."
                            required>{{ old('message', "Nous souhaitons en savoir plus sur votre établissement :\n\n- Quels types d'hébergements proposez-vous ?\n- Quelles sont vos capacités d'accueil ?\n- Quelles activités et services offrez-vous à vos clients ?\n- Seriez-vous intéressé par un partenariat avec le Club Méditerranée ?\n\nMerci de nous faire parvenir une présentation de votre établissement ainsi que vos conditions tarifaires.") }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Ce message sera intégré dans un email professionnel au nom du Club Méditerranée.</p>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('marketing.prospection.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Envoyer l'email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
