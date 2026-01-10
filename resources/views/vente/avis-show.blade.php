@extends('layouts.app')

@section('title', 'Repondre a l\'avis - Service Vente')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <a href="{{ route('vente.avis') }}" class="inline-flex items-center text-gray-600 hover:text-clubmed-blue mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour a la liste
        </a>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6">
            <div class="p-6 border-b bg-gradient-to-r from-clubmed-blue to-blue-700">
                <h1 class="text-2xl font-bold text-white">Avis Client</h1>
                <p class="text-blue-100">{{ $avis->resort->nomresort ?? 'Resort' }}</p>
            </div>

            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="font-bold text-lg text-gray-900">{{ $avis->user->name ?? 'Client anonyme' }}</p>
                        <p class="text-sm text-gray-500">
                            Publie le {{ \Carbon\Carbon::parse($avis->datepublication)->format('d/m/Y a H:i') }}
                        </p>
                    </div>
                    <div class="flex text-clubmed-gold text-xl">
                        @for($i = 0; $i < $avis->noteavis; $i++)
                            <span>*</span>
                        @endfor
                        @for($i = $avis->noteavis; $i < 5; $i++)
                            <span class="text-gray-300">*</span>
                        @endfor
                        <span class="ml-2 text-gray-600 text-base">({{ $avis->noteavis }}/5)</span>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-gray-700 leading-relaxed">{{ $avis->commentaire }}</p>
                </div>

                @if($avis->photos->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-600 mb-2">Photos jointes :</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($avis->photos as $photo)
                                <img src="{{ asset($photo->cheminfichierphoto) }}" 
                                     alt="Photo avis" 
                                     class="w-24 h-24 object-cover rounded-lg border">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($avis->reponse)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">V</span>
                        <h2 class="text-xl font-bold text-gray-900">Reponse du Service Vente</h2>
                    </div>

                    <div class="bg-green-50 rounded-xl p-4 mb-4">
                        <p class="text-gray-700 leading-relaxed">{{ $avis->reponse }}</p>
                    </div>

                    <div class="text-sm text-gray-500">
                        Repondu par <strong>{{ $avis->repondeur->name ?? 'Service Vente' }}</strong>
                        le {{ $avis->date_reponse ? $avis->date_reponse->format('d/m/Y a H:i') : '-' }}
                    </div>
                </div>
            </div>
        @endif

        @if(!$avis->reponse)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-900">Rediger une reponse</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Votre reponse sera visible publiquement sur la fiche du resort
                    </p>
                </div>

                <form action="{{ route('vente.avis.repondre', $avis->numavis) }}" method="POST" class="p-6">
                    @csrf

                    <div class="mb-6">
                        <label for="reponse" class="block text-sm font-medium text-gray-700 mb-2">
                            Votre reponse
                        </label>
                        <textarea 
                            name="reponse" 
                            id="reponse" 
                            rows="6" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-clubmed-blue focus:border-clubmed-blue"
                            placeholder="Bonjour et merci pour votre retour d'experience...

Nous prenons bonne note de vos remarques concernant..."
                            required
                        >{{ old('reponse') }}</textarea>
                        @error('reponse')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
                        <p class="text-sm text-blue-800">
                            <strong>Conseil :</strong> Restez professionnel et courtois. 
                            Remerciez le client pour son avis et apportez des precisions si necessaire.
                        </p>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('vente.avis') }}" 
                           class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-clubmed-blue text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Publier la reponse
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h3 class="font-bold text-gray-900 mb-4">Modifier la reponse</h3>
                <form action="{{ route('vente.avis.repondre', $avis->numavis) }}" method="POST">
                    @csrf
                    <textarea 
                        name="reponse" 
                        rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-clubmed-blue mb-4"
                    >{{ old('reponse', $avis->reponse) }}</textarea>
                    <button type="submit" class="px-4 py-2 bg-clubmed-blue text-white rounded-lg hover:bg-blue-700">
                        Mettre a jour
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
