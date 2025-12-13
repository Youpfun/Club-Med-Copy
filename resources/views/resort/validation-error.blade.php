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
                
                <h1 class="text-3xl font-bold text-red-600 mb-4">Erreur de Validation</h1>
                
                <p class="text-gray-700 text-lg mb-6">
                    {{ $message }}
                </p>
                
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 text-left">
                    <p class="text-red-800 text-sm">
                        Ce lien de validation n'est plus valide. Si vous avez besoin d'aide, veuillez contacter le service vente de Club Méditerranée.
                    </p>
                </div>

                <div class="mt-8">
                    <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
