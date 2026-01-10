@extends('layouts.app')

@section('title', 'Gestion des Avis - Service Vente')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Avis Clients</h1>
                <p class="text-gray-600 mt-1">Repondez aux avis pour ameliorer la satisfaction client</p>
            </div>
            <a href="{{ route('vente.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Retour au Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-4">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <span class="font-semibold text-gray-700">{{ $avis->total() }} avis au total</span>
                    <span class="text-sm text-gray-500">
                        {{ $avis->where('reponse', null)->count() }} sans reponse
                    </span>
                </div>

                <div class="divide-y">
                    @forelse($avis as $unAvis)
                        <div class="p-6 hover:bg-gray-50 transition {{ $unAvis->reponse ? 'bg-green-50/30' : '' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="font-bold text-gray-900">{{ $unAvis->user->name ?? 'Client' }}</span>
                                        <span class="text-gray-400">|</span>
                                        <span class="text-clubmed-blue font-medium">{{ $unAvis->resort->nomresort ?? 'Resort' }}</span>
                                        <span class="text-gray-400">|</span>
                                        <div class="flex text-clubmed-gold text-sm">
                                            @for($i = 0; $i < $unAvis->noteavis; $i++)
                                                <span>*</span>
                                            @endfor
                                            @for($i = $unAvis->noteavis; $i < 5; $i++)
                                                <span class="text-gray-300">*</span>
                                            @endfor
                                        </div>
                                    </div>

                                    <p class="text-gray-700 mb-2">{{ Str::limit($unAvis->commentaire, 150) }}</p>

                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span>Publie le {{ \Carbon\Carbon::parse($unAvis->datepublication)->format('d/m/Y') }}</span>
                                        @if($unAvis->reponse)
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                Repondu
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">
                                                En attente
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('vente.avis.show', $unAvis->numavis) }}" 
                                   class="px-4 py-2 {{ $unAvis->reponse ? 'bg-gray-100 text-gray-700' : 'bg-clubmed-blue text-white' }} rounded-lg hover:opacity-90 transition">
                                    {{ $unAvis->reponse ? 'Voir' : 'Repondre' }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500">
                            Aucun avis pour le moment
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t">
                    {{ $avis->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
