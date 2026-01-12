@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <a href="{{ route('marketing.projet-sejour.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                ← Retour à la liste
            </a>
            <h1 class="font-serif text-3xl text-clubmed-blue font-bold">✨ Nouveau Projet de Séjour</h1>
            <p class="text-slate-500 mt-1">Créez une proposition de séjour basée sur vos prospections positives</p>
        </div>

        {{-- MESSAGES --}}
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">❌</span> {!! session('error') !!}
            </div>
        @endif

        {{-- INFO --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="text-2xl">💡</span>
                <div>
                    <p class="font-medium text-blue-900">Création d'un projet de séjour</p>
                    <p class="text-sm text-blue-700 mt-1">
                        Renseignez les informations du séjour que vous souhaitez proposer. 
                        Vous pouvez lier ce projet à vos prospections positives (resort et partenaires).
                        Une fois soumis, le Directeur des Ventes pourra approuver ou demander des modifications.
                    </p>
                </div>
            </div>
        </div>

        {{-- FORMULAIRE --}}
        <div class="bg-white rounded-2xl shadow-md p-8">
            <form action="{{ route('marketing.projet-sejour.store') }}" method="POST" class="space-y-8">
                @csrf

                {{-- Section 1: Informations générales --}}
                <div class="border-b pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-clubmed-blue/10 text-clubmed-blue w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                        Informations générales
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nom du séjour --}}
                        <div class="md:col-span-2">
                            <label for="nom_sejour" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom du séjour <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nom_sejour" id="nom_sejour" 
                                value="{{ old('nom_sejour', $selectedProspectionResort->nom_resort ?? '') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition @error('nom_sejour') border-red-500 @enderror"
                                placeholder="Ex: Resort Alpin Val Thorens" required>
                            @error('nom_sejour')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-1">
                                Pays <span class="text-red-500">*</span>
                            </label>
                            <select name="pays" id="pays" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition @error('pays') border-red-500 @enderror"
                                required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($paysList as $pays)
                                    <option value="{{ $pays->nompays }}" 
                                        {{ old('pays', $selectedProspectionResort->pays ?? '') == $pays->nompays ? 'selected' : '' }}>
                                        {{ $pays->nompays }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pays')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 mb-1">
                                Ville
                            </label>
                            <input type="text" name="ville" id="ville" 
                                value="{{ old('ville', $selectedProspectionResort->ville ?? '') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="Ex: Val Thorens">
                        </div>

                        {{-- Tridents --}}
                        <div>
                            <label for="nb_tridents" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de tridents <span class="text-red-500">*</span>
                            </label>
                            <select name="nb_tridents" id="nb_tridents" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition" required>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('nb_tridents', 3) == $i ? 'selected' : '' }}>
                                        {{ $i }} 🔱
                                    </option>
                                @endfor
                            </select>
                        </div>

                        {{-- Capacité prévue --}}
                        <div>
                            <label for="capacite_prevue" class="block text-sm font-medium text-gray-700 mb-1">
                                Capacité (chambres)
                            </label>
                            <input type="number" name="capacite_prevue" id="capacite_prevue" 
                                value="{{ old('capacite_prevue') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="Ex: 150" min="1">
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description du projet
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="Décrivez le concept du séjour, le type de clientèle visée, etc.">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Dates et budget prévisionnels --}}
                <div class="border-b pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-clubmed-blue/10 text-clubmed-blue w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Prévisions
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Date début --}}
                        <div>
                            <label for="date_debut_prevue" class="block text-sm font-medium text-gray-700 mb-1">
                                Date d'ouverture prévue
                            </label>
                            <input type="date" name="date_debut_prevue" id="date_debut_prevue" 
                                value="{{ old('date_debut_prevue') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition">
                        </div>

                        {{-- Date fin --}}
                        <div>
                            <label for="date_fin_prevue" class="block text-sm font-medium text-gray-700 mb-1">
                                Date de fermeture prévue
                            </label>
                            <input type="date" name="date_fin_prevue" id="date_fin_prevue" 
                                value="{{ old('date_fin_prevue') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition">
                        </div>

                        {{-- Budget --}}
                        <div>
                            <label for="budget_estime" class="block text-sm font-medium text-gray-700 mb-1">
                                Budget estimé (€)
                            </label>
                            <input type="number" name="budget_estime" id="budget_estime" 
                                value="{{ old('budget_estime') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="Ex: 500000" min="0" step="1000">
                        </div>
                    </div>
                </div>

                {{-- Section 3: Liens avec les prospections --}}
                <div class="border-b pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-clubmed-blue/10 text-clubmed-blue w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                        Prospections associées
                    </h2>

                    {{-- Prospection Resort --}}
                    <div class="mb-6">
                        <label for="prospection_resort_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Prospection Resort (réponse positive)
                        </label>
                        @if($prospectionsResort->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                                Aucune prospection resort avec réponse positive disponible.
                                <a href="{{ route('marketing.prospection.index') }}" class="underline">Voir les prospections</a>
                            </div>
                        @else
                            <select name="prospection_resort_id" id="prospection_resort_id" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition">
                                <option value="">-- Aucune --</option>
                                @foreach($prospectionsResort as $prosp)
                                    <option value="{{ $prosp->numprospection }}" 
                                        {{ old('prospection_resort_id', $selectedProspectionResort->numprospection ?? '') == $prosp->numprospection ? 'selected' : '' }}>
                                        {{ $prosp->nom_resort }} - {{ $prosp->pays ?? 'N/A' }} ({{ $prosp->created_at->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Prospections Partenaires --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prospections Partenaires (réponses positives)
                        </label>
                        @if($prospectionsPartenaire->isEmpty())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                                Aucune prospection partenaire avec réponse positive disponible.
                                <a href="{{ route('marketing.prospection-partenaire.index') }}" class="underline">Voir les prospections</a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 border rounded-lg bg-slate-50">
                                @foreach($prospectionsPartenaire as $prosp)
                                    <label class="flex items-center gap-2 p-2 bg-white rounded hover:bg-slate-100 cursor-pointer transition">
                                        <input type="checkbox" name="prospection_partenaires_ids[]" value="{{ $prosp->numprospection }}"
                                            {{ in_array($prosp->numprospection, old('prospection_partenaires_ids', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-clubmed-blue focus:ring-clubmed-blue">
                                        <div class="flex-1">
                                            <span class="font-medium text-gray-800">{{ $prosp->nom_partenaire }}</span>
                                            <span class="text-xs text-gray-500 ml-2">{{ $prosp->type_activite_label ?? 'Autre' }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Section 4: Points forts et activités --}}
                <div>
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span class="bg-clubmed-blue/10 text-clubmed-blue w-8 h-8 rounded-full flex items-center justify-center text-sm">4</span>
                        Contenu du séjour
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Activités prévues --}}
                        <div>
                            <label for="activites_prevues" class="block text-sm font-medium text-gray-700 mb-1">
                                Activités prévues
                            </label>
                            <textarea name="activites_prevues" id="activites_prevues" rows="5"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="• Ski alpin&#10;• Spa et bien-être&#10;• Restaurant gastronomique&#10;• Animations enfants">{{ old('activites_prevues') }}</textarea>
                        </div>

                        {{-- Points forts --}}
                        <div>
                            <label for="points_forts" class="block text-sm font-medium text-gray-700 mb-1">
                                Points forts / Arguments de vente
                            </label>
                            <textarea name="points_forts" id="points_forts" rows="5"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-clubmed-blue focus:ring focus:ring-clubmed-blue/20 transition"
                                placeholder="• Emplacement exceptionnel&#10;• Partenaire ski réputé&#10;• Vue panoramique&#10;• Accès direct aux pistes">{{ old('points_forts') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('marketing.projet-sejour.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-8 py-3 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg font-bold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer le projet
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
