@extends('layouts.app')

@section('content')
<div class="container mx-auto py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow text-center p-12">
            <div class="text-6xl mb-4">⚠️</div>
            <h1 class="text-2xl font-bold text-red-700 mb-4">Lien invalide</h1>
            <p class="text-gray-600 mb-6">{{ $message }}</p>
            <div class="bg-gray-50 border-l-4 border-gray-500 p-4 text-left rounded">
                <p class="text-gray-700">
                    Si vous pensez qu'il s'agit d'une erreur, veuillez contacter le service Marketing de Club Méditerranée.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
