<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> <title>Liste des Clients</title> 
 <a href="{{ url('/') }}">Retour à l'accueil</a>
    <style>
        /* Un peu de style pour que ce soit lisible */ table { width: 100%; border-collapse: 
        collapse; margin-top: 20px; font-family: Arial, sans-serif; } th { background-color: 
        #007BFF; color: white; padding: 10px; text-align: left; }
        td { border: 1px solid #ddd; padding: 8px; } tr:nth-child(even) { background-color: 
        #f2f2f2; }
    </style> </head> <body> <h1>Annuaire des Clients</h1> <table> <thead> <tr> <th>N° 
                Client</th> <th>Nom & Prénom</th> <th>Login</th> <th>Email</th> 
                <th>Téléphone</th> <th>Ville</th> <th>Date Naissance</th>
            </tr> </thead> <tbody> @foreach($clients as $client) <tr> <td>{{ $client->numclient 
                    }}</td>
                    
                    <td> {{ $client->nomclient }} {{ $client->prenomclient }} <br> <small>({{ 
                        $client->genreclient }})</small>
                    </td> <td>{{ $client->login }}</td> <td>{{ $client->emailclient }}</td> 
                    <td>{{ $client->telephone }}</td>
                    
                    <td> {{ $client->numrue }} {{ $client->nomrue }}<br> {{ $client->codepostal 
                        }} <strong>{{ $client->ville }}</strong>
                    </td> <td>{{ $client->datenaissance }}</td> </tr> @endforeach </tbody> 
    </table>
</body>
</html>
