<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> <title>Types de Clubs</title> 
    <style>
        body { font-family: sans-serif; padding: 20px; } h1 { color: #333; }
        
        /* Style du tableau */ table { width: 50%; /* Pas besoin que ce soit trop large pour 2 
            colonnes */ border-collapse: collapse; margin-top: 20px; box-shadow: 0 0 10px 
            rgba(0,0,0,0.1);
        }
        th { background-color: #6c757d; /* Gris pro */ color: white; padding: 12px; text-align: 
            left;
        }
        td { border: 1px solid #ddd; padding: 10px;
        }
        tr:nth-child(even) { background-color: #f9f9f9; } </style> </head> <body> <h1>Types de 
    Clubs</h1> <table>
        <thead> <tr> <th>N° Type (ID)</th> <th>Nom du Type</th> </tr> </thead> <tbody> 
            @foreach($typeclubs as $type)
                <tr> <td>{{ $type->numtypeclub }}</td> <td><strong>{{ $type->nomtypeclub 
                    }}</strong></td>
                </tr> @endforeach </tbody> </table> <br> <a href="{{ url('/') }}">Retour à 
    l'accueil</a>

{{-- Chatbot BotMan --}}
@include('layouts.chatbot')
</body>
</html>
