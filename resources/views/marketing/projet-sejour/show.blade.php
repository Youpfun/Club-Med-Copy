@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <a href="{{ route('marketing.projet-sejour.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                ← Retour à la liste
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="font-serif text-3xl text-clubmed-blue font-bold">{{ $projet->nom_sejour }}</h1>
                    <p class="text-slate-500 mt-1">Projet #{{ $projet->numprojet }} • Créé le {{ $projet->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-{{ $projet->statut_color }}-100 text-{{ $projet->statut_color }}-800">
                    {{ $projet->statut_icon }} {{ $projet->statut_label }}
                </span>
            </div>
        </div>

        {{-- MESSAGES --}}
        @if(session('success'))
            <div class="p-4 mb-6 bg-green-100 text-green-700 rounded-lg border-l-4 border-green-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">✅</span> {!! session('success') !!}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 mb-6 bg-red-100 text-red-700 rounded-lg border-l-4 border-red-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">❌</span> {!! session('error') !!}
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 mb-6 bg-blue-100 text-blue-700 rounded-lg border-l-4 border-blue-500 shadow-sm flex items-center">
                <span class="text-xl mr-2">ℹ️</span> {!! session('info') !!}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLONNE PRINCIPALE --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Informations générales --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        🏨 Informations du Séjour
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nom</p>
                            <p class="font-medium text-gray-900">{{ $projet->nom_sejour }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Destination</p>
                            <p class="font-medium text-gray-900">
                                {{ $projet->ville ? $projet->ville . ', ' : '' }}{{ $projet->pays }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tridents</p>
                            <p class="font-medium text-yellow-500">
                                @for($i = 0; $i < $projet->nb_tridents; $i++)🔱@endfor
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Capacité prévue</p>
                            <p class="font-medium text-gray-900">{{ $projet->capacite_prevue ?? '-' }} chambres</p>
                        </div>
                    </div>
                    @if($projet->description)
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm text-gray-500 mb-2">Description</p>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($projet->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Prévisions --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        📅 Prévisions
                    </h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Ouverture prévue</p>
                            <p class="font-medium text-gray-900">
                                {{ $projet->date_debut_prevue ? $projet->date_debut_prevue->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fermeture prévue</p>
                            <p class="font-medium text-gray-900">
                                {{ $projet->date_fin_prevue ? $projet->date_fin_prevue->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Budget estimé</p>
                            <p class="font-medium text-gray-900">
                                {{ $projet->budget_estime ? number_format($projet->budget_estime, 0, ',', ' ') . ' €' : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Prospections liées --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        🔗 Prospections Associées
                    </h2>
                    
                    {{-- Prospection Resort --}}
                    @if($projet->prospectionResort)
                        <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-purple-700">🏨 Resort</span>
                                    <p class="font-bold text-purple-900">{{ $projet->prospectionResort->nom_resort }}</p>
                                    <p class="text-sm text-purple-600">{{ $projet->prospectionResort->pays }} {{ $projet->prospectionResort->ville ? '- ' . $projet->prospectionResort->ville : '' }}</p>
                                </div>
                                <a href="{{ route('marketing.prospection.show', $projet->prospectionResort->numprospection) }}" 
                                   class="text-purple-600 hover:text-purple-800 text-sm underline">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mb-4">Aucune prospection resort liée.</p>
                    @endif

                    {{-- Prospections Partenaires --}}
                    @if($projet->prospections_partenaires && $projet->prospections_partenaires->count() > 0)
                        <div class="space-y-2">
                            <span class="text-sm font-medium text-emerald-700">🤝 Partenaires</span>
                            @foreach($projet->prospections_partenaires as $part)
                                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200 flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-emerald-900">{{ $part->nom_partenaire }}</p>
                                        <p class="text-xs text-emerald-600">{{ $part->type_activite_label ?? 'Autre' }}</p>
                                    </div>
                                    <a href="{{ route('marketing.prospection-partenaire.show', $part->numprospection) }}" 
                                       class="text-emerald-600 hover:text-emerald-800 text-sm underline">
                                        Voir
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Aucune prospection partenaire liée.</p>
                    @endif
                </div>

                {{-- Contenu du séjour --}}
                @if($projet->activites_prevues || $projet->points_forts)
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        ⭐ Contenu du Séjour
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($projet->activites_prevues)
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Activités prévues</p>
                                <div class="prose prose-sm max-w-none text-gray-700 bg-slate-50 p-3 rounded-lg">
                                    {!! nl2br(e($projet->activites_prevues)) !!}
                                </div>
                            </div>
                        @endif
                        @if($projet->points_forts)
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Points forts</p>
                                <div class="prose prose-sm max-w-none text-gray-700 bg-slate-50 p-3 rounded-lg">
                                    {!! nl2br(e($projet->points_forts)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Commentaire du directeur --}}
                @if($projet->commentaire_directeur)
                <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-{{ $projet->statut_color }}-500">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        💬 Commentaire du Directeur
                    </h2>
                    @if($projet->directeur)
                        <p class="text-sm text-gray-500 mb-2">
                            Par {{ $projet->directeur->name }} le {{ $projet->date_decision->format('d/m/Y à H:i') }}
                        </p>
                    @endif
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($projet->commentaire_directeur)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- COLONNE LATÉRALE --}}
            <div class="space-y-6">

                {{-- Informations --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">📋 Informations</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé par</span>
                            <span class="font-medium text-gray-800">{{ $projet->createur->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Créé le</span>
                            <span class="font-medium text-gray-800">{{ $projet->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if($projet->date_soumission)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Soumis le</span>
                                <span class="font-medium text-gray-800">{{ $projet->date_soumission->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if($projet->date_decision)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Décision le</span>
                                <span class="font-medium text-gray-800">{{ $projet->date_decision->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">⚡ Actions</h2>
                    
                    <div class="space-y-3">
                        {{-- Modifier (si éditable) --}}
                        @if($canEdit)
                            <a href="{{ route('marketing.projet-sejour.edit', $projet->numprojet) }}" 
                               class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                ✏️ Modifier
                            </a>
                        @endif

                        {{-- Soumettre (brouillon ou en révision) --}}
                        @if($projet->canBeSubmitted() && ($projet->user_id === Auth::id() || $isDirecteur))
                            <form action="{{ route('marketing.projet-sejour.submit', $projet->numprojet) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    📤 Soumettre au Directeur
                                </button>
                            </form>
                        @endif

                        {{-- Actions Directeur (si soumis) --}}
                        @if($canReview)
                            <div class="border-t pt-3 mt-3">
                                <p class="text-sm text-gray-500 mb-3 font-medium">Actions Directeur :</p>
                                
                                {{-- Approuver --}}
                                <form action="{{ route('marketing.projet-sejour.approve', $projet->numprojet) }}" method="POST" class="mb-2">
                                    @csrf
                                    <input type="hidden" name="commentaire" value="">
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                        ✅ Approuver
                                    </button>
                                </form>

                                {{-- Demander révision --}}
                                <button onclick="document.getElementById('revisionModal').classList.remove('hidden')" 
                                        class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2 mb-2">
                                    🔄 Demander révision
                                </button>

                                {{-- Refuser --}}
                                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                                        class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    ❌ Refuser
                                </button>
                            </div>
                        @endif

                        {{-- Créer le resort (si approuvé) --}}
                        @if($projet->statut === 'approuve')
                            <form action="{{ route('marketing.projet-sejour.start-creation', $projet->numprojet) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    🏗️ Créer le Resort
                                </button>
                            </form>
                        @endif

                        {{-- Voir le resort (si créé) --}}
                        @if($projet->numresort)
                            <a href="{{ route('resort.step2', $projet->numresort) }}" 
                               class="w-full px-4 py-2 bg-clubmed-blue hover:bg-clubmed-blue/90 text-white rounded-lg font-medium transition flex items-center justify-center gap-2">
                                🏨 Voir le Resort
                            </a>
                        @endif

                        {{-- Supprimer (brouillon uniquement) --}}
                        @if($projet->statut === 'brouillon' && ($projet->user_id === Auth::id() || $isDirecteur))
                            <form action="{{ route('marketing.projet-sejour.destroy', $projet->numprojet) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Révision --}}
<div id="revisionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">🔄 Demander une révision</h3>
        <form action="{{ route('marketing.projet-sejour.request-revision', $projet->numprojet) }}" method="POST">
            @csrf
            <textarea name="commentaire" rows="4" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200 mb-4"
                placeholder="Expliquez les modifications demandées..."></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium">
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Refus --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-800 mb-4">❌ Refuser le projet</h3>
        <form action="{{ route('marketing.projet-sejour.reject', $projet->numprojet) }}" method="POST">
            @csrf
            <textarea name="commentaire" rows="4" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 mb-4"
                placeholder="Motif du refus..."></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                        class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium">
                    Annuler
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium">
                    Refuser
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
