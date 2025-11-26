<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> <title>Nos Resorts</title> 
 <a href="{{ url('/') }}">Retour à l'accueil</a>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: 
        sans-serif; } th { background-color: #28a745; color: white; padding: 10px; text-align: 
        left; } td { border: 1px solid #ddd; padding: 8px; } tr:nth-child(even) { 
        background-color: #f2f2f2; }
    </style> </head> <body> <h1>Liste des Resorts Club Med</h1> <table> <thead> <tr> <th>Nom du 
                Resort</th> <th>Pays (Code)</th> <th>Tridents</th> <th>Note Moyenne</th> 
                <th>Chambres</th> <th>Description</th>
            </tr> </thead> <tbody> @foreach($resorts as $resort) <tr> <td><a href="/ficheresort/{{ $resort->numresort }}"><strong>{{ 
                    $resort->nomresort }}</strong></td></a>
                    
                    <td>{{ $resort->codepays }}</td>
                    
                    <td>{{ $resort->nbtridents }} 🔱</td> <td>{{ $resort->moyenneavis }} / 
                    5</td> <td>{{ $resort->nbchambrestotal }}</td> <td>{{ 
                    $resort->descriptionresort }}</td>
                </tr> @endforeach </tbody> </table> </body>
</html>
