@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-red-100 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-xl p-8 text-center">
                <div class="mb-6">
                    <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                
                <h1 class="text-3xl font-bold text-red-600 mb-4">Lien Invalide</h1>
                
                <p class="text-gray-600 mb-6">
                    {{ $message }}
                </p>

                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-gray-800 mb-2">Besoin d'aide ?</h3>
                    <p class="text-gray-600 text-sm">
                        Si vous avez des questions concernant votre réservation, 
                        veuillez contacter notre service commercial.
                    </p>
                </div>

                <div class="mt-8">
                    <a href="{{ url('/') }}" class="inline-block px-6 py-3 bg-clubmed-blue hover:bg-blue-900 text-white font-bold rounded-lg transition">
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
