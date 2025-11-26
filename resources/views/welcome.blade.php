@extends('layouts.header')

@section('title', 'Séjour tout compris dans les plus belles destinations | Club Med')

@section('content')
    <h1 style="text-align: center;">Bienvenue sur le site du Club Med</h1>
    
    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ url('/resorts') }}" 
           style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;">
            Accéder à la liste des Resorts
        </a>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ url('/clients') }}" 
           style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;">
            Accéder à la liste des Clients
        </a>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ url('/typeclubs') }}" 
           style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;">
            Accéder à la liste des TypeClubs
        </a>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <a href="{{ url('/localisations') }}" 
           style="background-color: #007BFF; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px; font-size: 18px; font-family: sans-serif;">
            Accéder à la liste des localisations
        </a>
    </div>
@endsection