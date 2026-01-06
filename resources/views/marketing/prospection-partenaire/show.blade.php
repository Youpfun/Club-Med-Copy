@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- EN-TÊTE --}}
        <div class="mb-8">
            <a href="{{ route('marketing.prospection-partenaire.index') }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1 mb-2">
                ← Retour à la liste
            </a>
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="font-serif text-3xl text-clubmed-blue font-bold">{{ $prospection->nom_partenaire }}</h1>
                    <p class="text-slate-500 mt-1">Prospection partenaire #{{ $prospection->numprospection }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($prospection->type_activite)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                            {{ $prospection->type_activite_label }}
                        </span>
                    @endif
                    @php
                        $color = $prospection->statut_color;
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-{{ $color }}-100 text-{{ $color }}-800">
                        @if($prospection->statut === 'envoyee')
                            📨 Envoyée
                        @elseif($prospection->statut === 'repondue')
                            ✅ Répondue
                        @elseif($prospection->statut === 'en_cours')
                            ⏳ En cours
                        @else
                            📁 Clôturée
                        @endif
                    </span>
                </div>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- COLONNE PRINCIPALE --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Informations du partenaire --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        🤝 Informations du Partenaire
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nom</p>
                            <p class="font-medium text-gray-900">{{ $prospection->nom_partenaire }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $prospection->email_partenaire }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Type d'activité</p>
                            <p class="font-medium text-gray-900">{{ $prospection->type_activite_label ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Localisation</p>
                            <p class="font-medium text-gray-900">
                                {{ $prospection->ville ?? '' }}{{ $prospection->ville && $prospection->pays ? ', ' : '' }}{{ $prospection->pays ?? '-' }}
                            </p>
                        </div>
                        @if($prospection->telephone)
                        <div>
                            <p class="text-sm text-gray-500">Téléphone</p>
                            <p class="font-medium text-gray-900">{{ $prospection->telephone }}</p>
                        </div>
                        @endif
                        @if($prospection->site_web)
                        <div>
                            <p class="text-sm text-gray-500">Site web</p>
                            <a href="{{ $prospection->site_web }}" target="_blank" class="font-medium text-blue-600 hover:underline">
                                {{ $prospection->site_web }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Message envoyé --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        ✉️ Message envoyé
                    </h2>
                    <div class="bg-slate-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-500 mb-1">Objet</p>
                        <p class="font-medium text-gray-900">{{ $prospection->objet }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-2">Contenu</p>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($prospection->message)) !!}
                        </div>
                    </div>
                </div>

                {{-- Réponse (si existante) --}}
                @if($prospection->reponse)
                <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-500">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        💬 Réponse du partenaire
                    </h2>
                    @if($prospection->date_reponse)
                    <p class="text-sm text-gray-500 mb-3">Reçue le {{ $prospection->date_reponse->format('d/m/Y à H:i') }}</p>
                    @endif
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! nl2br(e($prospection->reponse)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- COLONNE LATÉRALE --}}
            <div class="space-y-6">
                
                {{-- Actions --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">⚡ Actions</h2>
                    
                    <div class="space-y-3">
                        {{-- Renvoyer l'email --}}
                        <form action="{{ route('marketing.prospection-partenaire.resend', $prospection->numprospection) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                📤 Renvoyer l'email
                            </button>
                        </form>

                        {{-- Supprimer --}}
                        <form action="{{ route('marketing.prospection-partenaire.destroy', $prospection->numprospection) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette prospection ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mettre à jour le statut --}}
                <div class="bg-white rounded-2xl shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">📋 Mettre à jour</h2>
                    
                    <form action="{{ route('marketing.prospection-partenaire.update-statut', $prospection->numprospection) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select name="statut" id="statut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200">
                                @foreach(\App\Models\ProspectionPartenaire::getStatuts() as $key => $label)
                                    <option value="{{ $key }}" {{ $prospection->statut === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="reponse" class="block text-sm font-medium text-gray-700 mb-1">Notes / Réponse</label>
                            <textarea name="reponse" id="reponse" rows="4" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200"
                                placeholder="Notez ici la réponse du partenaire ou vos remarques...">{{ $prospection->reponse }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition">
                            Mettre à jour
                        </button>
                    </form>
                </div>

                {{-- Infos supplémentaires --}}
                <div class="bg-slate-100 rounded-xl p-4">
                    <h3 class="font-medium text-gray-700 mb-2">📅 Historique</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Créée le</span>
                            <span class="font-medium">{{ $prospection->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Par</span>
                            <span class="font-medium">{{ $prospection->user->name ?? 'N/A' }}</span>
                        </div>
                        @if($prospection->date_reponse)
                        <div class="flex justify-between">
                            <span>Réponse le</span>
                            <span class="font-medium">{{ $prospection->date_reponse->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
