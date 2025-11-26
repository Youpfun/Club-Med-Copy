<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> <title>Liste des Clients</title> 
    <style>
        /* Un peu de style pour que ce soit lisible */ table { width: 100%; border-collapse: 
        collapse; margin-top: 20px; font-family: Arial, sans-serif; } th { background-color: 
        #007BFF; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; } tr:nth-child(even) { background-color: 
        #f2f2f2; }
    </style> </head> <body> <h1>Annuaire des Resorts</h1> <table> <thead> <tr> <th>N° Resort</th> 
                <th>Nom Resort</th> <th>Code Pays</th> <th>Moyenne Avis</th> <th>Nbs Chambres Total</th> 
            </tr> </thead> <tbody> @foreach($resorts as $resort) <tr> <td>{{ $resort->numresort 
                    }}</td>
                    
                    <td> {{ $resort->nomresort }}
                    </td> <td>{{ $resort->codepays }}</td> <td>{{ $resort->moyenneavis}}</td> <td>{{ 
                    $resort->nbchambrestotal }}</td>
                     </tr> @endforeach </tbody> </table> 
</body>
</html>
