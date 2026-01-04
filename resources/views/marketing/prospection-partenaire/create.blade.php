@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-3xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <a href="{{ route('marketing.prospection-partenaire.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                ← Retour à la liste
            </a>
            <h1 class="font-serif text-3xl text-[#113559] font-bold">✉️ Contacter un Partenaire</h1>
            <p class="text-slate-500 mt-1">Envoyer une demande d'information à un partenaire potentiel (ESF, spa, etc.)</p>
        </div>

        {{-- MESSAGES --}}
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">❌</span> {!! session('error') !!}
            </div>
        @endif

        {{-- INFO --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="text-2xl">💡</span>
                <div>
                    <p class="font-medium text-emerald-900">Prospection de partenaires</p>
                    <p class="text-sm text-emerald-700 mt-1">
                        Utilisez ce formulaire pour contacter des prestataires d'activités (ESF, moniteurs de plongée, 
                        spas, etc.) que vous envisagez d'ajouter comme partenaires pour nos resorts.
                    </p>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <div class="bg-white rounded-2xl shadow-md p-8">
            <form action="{{ route('marketing.prospection-partenaire.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Section 1: Informations sur le partenaire --}}
                <div class="border-b pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-emerald-100 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Informations sur le partenaire
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nom du partenaire --}}
                        <div class="md:col-span-2">
                            <label for="nom_partenaire" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom du partenaire <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom_partenaire" id="nom_partenaire" 
                                value="{{ old('nom_partenaire') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition @error('nom_partenaire') border-red-500 @enderror"
                                placeholder="Ex: ESF Courchevel, Spa Zen, Club de Plongée..."
                                required>
                            @error('nom_partenaire')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email du partenaire --}}
                        <div class="md:col-span-2">
                            <label for="email_partenaire" class="block text-sm font-medium text-gray-700 mb-1">
                                Email du partenaire <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email_partenaire" id="email_partenaire" 
                                value="{{ old('email_partenaire') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition @error('email_partenaire') border-red-500 @enderror"
                                placeholder="contact@partenaire.com"
                                required>
                            @error('email_partenaire')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type d'activité --}}
                        <div class="md:col-span-2">
                            <label for="type_activite" class="block text-sm font-medium text-gray-700 mb-1">Type d'activité</label>
                            <select name="type_activite" id="type_activite" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition">
                                <option value="">-- Sélectionnez un type --</option>
                                @foreach($typesActivite as $key => $label)
                                    <option value="{{ $key }}" {{ old('type_activite') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <input type="text" name="pays" id="pays" 
                                value="{{ old('pays') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition"
                                placeholder="Ex: France">
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" name="ville" id="ville" 
                                value="{{ old('ville') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition"
                                placeholder="Ex: Courchevel">
                        </div>

                        {{-- Téléphone --}}
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" 
                                value="{{ old('telephone') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition"
                                placeholder="Ex: +33 4 79 00 00 00">
                        </div>

                        {{-- Site web --}}
                        <div>
                            <label for="site_web" class="block text-sm font-medium text-gray-700 mb-1">Site web</label>
                            <input type="url" name="site_web" id="site_web" 
                                value="{{ old('site_web') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition"
                                placeholder="https://www.partenaire.com">
                        </div>
                    </div>
                </div>

                {{-- Section 2: Contenu du message --}}
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-emerald-100 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Votre message
                    </h2>

                    {{-- Objet --}}
                    <div class="mb-4">
                        <label for="objet" class="block text-sm font-medium text-gray-700 mb-1">
                            Objet de l'email <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="objet" id="objet" 
                            value="{{ old('objet', 'Proposition de partenariat - Club Méditerranée') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition @error('objet') border-red-500 @enderror"
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
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 transition @error('message') border-red-500 @enderror"
                            placeholder="Décrivez ce que vous recherchez comme partenariat..."
                            required>{{ old('message', "Nous sommes à la recherche de partenaires pour enrichir l'offre d'activités de nos resorts.\n\nNous aimerions en savoir plus sur :\n- Vos prestations et services proposés\n- Votre capacité d'accueil (groupes, individuels)\n- Vos tarifs et conditions de partenariat\n- Votre disponibilité pour un échange ou une rencontre\n\nPouvez-vous nous transmettre une présentation de votre structure ainsi que vos conditions tarifaires pour les groupes ?") }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Ce message sera intégré dans un email professionnel au nom du Club Méditerranée.</p>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('marketing.prospection-partenaire.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition flex items-center gap-2">
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
