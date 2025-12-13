@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8 text-center">
                @if($status === 'accepted')
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-green-600 mb-4">Réservation Validée !</h1>
                    
                    <p class="text-gray-700 text-lg mb-6">
                        Merci d'avoir validé la réservation <strong>#{{ $reservation->numreservation }}</strong>.
                    </p>
                    
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 text-left">
                        <p class="text-green-800">
                            <strong>Prochaines étapes :</strong><br>
                            Les partenaires des activités ont été automatiquement contactés par email pour confirmer leur disponibilité. 
                            Le service vente procédera à la confirmation finale une fois toutes les validations reçues.
                        </p>
                    </div>
                @else
                    <div class="mb-6">
                        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-red-600 mb-4">Réservation Refusée</h1>
                    
                    <p class="text-gray-700 text-lg mb-6">
                        Vous avez refusé la réservation <strong>#{{ $reservation->numreservation }}</strong>.
                    </p>
                    
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 text-left">
                        <p class="text-red-800">
                            Le client et le service vente ont été notifiés de votre refus. 
                            La réservation ne sera pas confirmée.
                        </p>
                    </div>
                @endif

                @if($comment)
                    <div class="bg-gray-50 rounded-lg p-4 text-left">
                        <p class="text-sm text-gray-600 mb-1"><strong>Votre commentaire :</strong></p>
                        <p class="text-gray-800">{{ $comment }}</p>
                    </div>
                @endif

                <div class="mt-8 pt-6 border-t">
                    <p class="text-gray-600 text-sm">
                        Réservation traitée le {{ now()->format('d/m/Y à H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
