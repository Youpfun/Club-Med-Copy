@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-3xl mx-auto">
        {{-- En-tête --}}
        <a href="{{ route('marketing.demandes.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Retour à la liste
        </a>
        
        <div class="bg-white rounded-lg shadow">
            <div class="border-b p-6 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg">
                <h1 class="text-2xl font-bold text-white">Nouvelle Demande de Disponibilité</h1>
                <p class="text-blue-100 mt-1">Envoyez une demande au resort pour connaître ses disponibilités</p>
            </div>

            <form action="{{ route('marketing.demandes.store') }}" method="POST" class="p-6">
                @csrf

                {{-- Sélection du Resort --}}
                <div class="mb-6">
                    <label for="numresort" class="block text-sm font-medium text-gray-700 mb-2">
                        Resort <span class="text-red-500">*</span>
                    </label>
                    <select name="numresort" id="numresort" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Sélectionnez un resort --</option>
                        @foreach($resorts as $resort)
                            <option value="{{ $resort->numresort }}" 
                                    {{ old('numresort') == $resort->numresort ? 'selected' : '' }}>
                                {{ $resort->nomresort }} 
                                @if($resort->pays) - {{ $resort->pays->nompays }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('numresort')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        📧 Les demandes sont envoyées à : <strong>clubmedsae@gmail.com</strong>
                    </p>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_debut" id="date_debut" required
                               value="{{ old('date_debut') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('date_debut')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de fin <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_fin" id="date_fin" required
                               value="{{ old('date_fin') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('date_fin')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Capacité --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nb_chambres" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de chambres souhaitées
                        </label>
                        <input type="number" name="nb_chambres" id="nb_chambres" min="1"
                               value="{{ old('nb_chambres') }}"
                               placeholder="Ex: 10"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nb_chambres')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nb_personnes" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de personnes prévues
                        </label>
                        <input type="number" name="nb_personnes" id="nb_personnes" min="1"
                               value="{{ old('nb_personnes') }}"
                               placeholder="Ex: 20"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nb_personnes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Message --}}
                <div class="mb-6">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Message (optionnel)
                    </label>
                    <textarea name="message" id="message" rows="4"
                              placeholder="Précisez vos besoins spécifiques (types de chambres, équipements, etc.)"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Info box --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <div class="flex items-start">
                        <span class="text-2xl mr-3">ℹ️</span>
                        <div>
                            <h4 class="font-bold text-blue-800">Comment ça marche ?</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Un email sera envoyé au resort avec un lien unique. Le resort pourra indiquer ses disponibilités 
                                directement via ce lien. Vous recevrez une notification dès que le resort aura répondu.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('marketing.demandes.index') }}" 
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-bold transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Mettre à jour date_fin min quand date_debut change
document.getElementById('date_debut').addEventListener('change', function() {
    document.getElementById('date_fin').min = this.value;
});
</script>
@endsection
