<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Complète des Clients</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f9; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-retour { background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        
        /* Table Styles */
        .table-container { overflow-x: auto; } /* Permet de scroller si le tableau est trop large */
        table { width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        th { background-color: #007BFF; color: white; padding: 12px; text-align: left; white-space: nowrap; font-size: 14px; }
        td { border: 1px solid #ddd; padding: 8px; font-size: 13px; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        
        /* Utilitaires pour les champs longs */
        .hash-password { font-family: monospace; font-size: 10px; color: #666; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Annuaire Complet des Clients</h1>
        <a href="{{ url('/') }}" class="btn-retour">← Retour à l'accueil</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th> <th>ID Carte</th> <th>Genre</th> <th>Prénom</th> <th>Nom</th> <th>Login</th> <th>Email</th> <th>Mot de passe (Hash)</th> <th>Téléphone</th> <th>Date Naissance</th> <th>N° Rue</th> <th>Nom Rue</th> <th>CP</th> <th>Ville</th> </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td><strong>{{ $client->numclient }}</strong></td>
                    <td>{{ $client->idcarte ?? '-' }}</td>
                    
                    <td>{{ $client->genreclient }}</td>
                    <td>{{ $client->prenomclient }}</td>
                    <td>{{ $client->nomclient }}</td>

                    <td>{{ $client->login }}</td>
                    <td><a href="mailto:{{ $client->emailclient }}">{{ $client->emailclient }}</a></td>
                    <td>
                        <span class="hash-password" title="{{ $client->password }}">
                            {{ $client->password }}
                        </span>
                    </td>

                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->datenaissance }}</td>

                    <td>{{ $client->numrue }}</td>
                    <td>{{ $client->nomrue }}</td>
                    <td>{{ $client->codepostal }}</td>
                    <td>{{ $client->ville }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <p style="margin-top: 10px; font-size: 12px; color: #666;">
        Total : {{ count($clients) }} client(s) enregistré(s).
    </p>

</body>
</html>