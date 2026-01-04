@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow text-center p-12">
            @if($status === 'available')
                <div class="text-6xl mb-4">✅</div>
                <h1 class="text-2xl font-bold text-green-700 mb-4">Réponse enregistrée</h1>
                <p class="text-gray-600 mb-6">
                    Merci d'avoir confirmé la disponibilité pour la période du 
                    <strong>{{ $demande->date_debut->format('d/m/Y') }}</strong> au 
                    <strong>{{ $demande->date_fin->format('d/m/Y') }}</strong>.
                </p>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 text-left rounded">
                    <p class="text-green-800">
                        Le service Marketing de Club Méditerranée a été informé de votre réponse et vous contactera prochainement pour finaliser les détails du séjour.
                    </p>
                </div>
            @elseif($status === 'partially_available')
                <div class="text-6xl mb-4">⚠️</div>
                <h1 class="text-2xl font-bold text-orange-700 mb-4">Réponse enregistrée</h1>
                <p class="text-gray-600 mb-6">
                    Merci d'avoir indiqué une disponibilité partielle pour la période du 
                    <strong>{{ $demande->date_debut->format('d/m/Y') }}</strong> au 
                    <strong>{{ $demande->date_fin->format('d/m/Y') }}</strong>.
                </p>
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 text-left rounded">
                    <p class="text-orange-800">
                        Le service Marketing prendra en compte vos disponibilités partielles et vous contactera pour discuter des options possibles.
                    </p>
                </div>
            @else
                <div class="text-6xl mb-4">❌</div>
                <h1 class="text-2xl font-bold text-red-700 mb-4">Réponse enregistrée</h1>
                <p class="text-gray-600 mb-6">
                    Merci d'avoir répondu. Nous avons noté que vous n'avez pas de disponibilité pour la période du 
                    <strong>{{ $demande->date_debut->format('d/m/Y') }}</strong> au 
                    <strong>{{ $demande->date_fin->format('d/m/Y') }}</strong>.
                </p>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 text-left rounded">
                    <p class="text-red-800">
                        Le service Marketing recherchera d'autres solutions. Nous vous recontacterons peut-être pour des dates alternatives.
                    </p>
                </div>
            @endif

            <div class="mt-8 pt-6 border-t text-sm text-gray-500">
                <p>Club Méditerranée - Service Marketing</p>
                <p>Vous pouvez fermer cette page.</p>
            </div>
        </div>
    </div>
</div>
@endsection
