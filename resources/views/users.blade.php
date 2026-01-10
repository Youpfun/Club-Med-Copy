<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste Complète des Utilisateurs</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; background-color: #f4f4f9; color: #333; }
        
        /* En-tête */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header h1 { color: #113559; margin: 0; font-size: 24px; }
        .btn-retour { background-color: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-size: 14px; transition: background 0.3s; }
        .btn-retour:hover { background-color: #5a6268; }
        
        /* Styles du Tableau */
        .table-container { overflow-x: auto; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; min-width: 1200px; }
        
        th { background-color: #113559; color: white; padding: 15px 10px; text-align: left; white-space: nowrap; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { border-bottom: 1px solid #eee; padding: 10px; font-size: 13px; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e2e6ea; }
        
        /* --- GESTION DES COULEURS DES RÔLES --- */
        .badge { padding: 5px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: white; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
        
        /* Vert : Le client confirmé (a acheté) */
        .role-client { background-color: #28a745; }   
        
        /* Bleu : L'utilisateur inscrit (internaute sans résa) */
        .role-utilisateur { background-color: #007BFF; } 
        
        /* Rouge : La direction */
        .role-admin { background-color: #dc3545; }    
        
        /* Cyan : Les employés (Vente, Marketing) */
        .role-staff { background-color: #17a2b8; }    
        
        /* Gris : Par défaut ou Visiteur simple */
        .role-visiteur { background-color: #6c757d; } 

        /* Utilitaires */
        .hash-password { font-family: monospace; font-size: 11px; color: #888; background: #eee; padding: 2px 5px; border-radius: 3px; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }
        .email-link { color: #006298; text-decoration: none; font-weight: 500; }
        .email-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>Annuaire Global</h1>
            <p style="font-size: 12px; color: #666; margin-top: 5px;">
                Vue administrative de la table <strong>users</strong>
            </p>
        </div>
        <a href="{{ url('/') }}" class="btn-retour">← Retour à l'accueil</a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th> 
                    <th>Rôle</th> <th>Genre</th> 
                    <th>Nom Complet</th> 
                    <th>Email</th> 
                    <th>Téléphone</th> 
                    <th>Date Naissance</th> 
                    <th>Ville</th> 
                    <th>CP</th>
                    <th>Adresse</th> 
                    <th>ID Carte</th> 
                    <th>Mot de passe (Hash)</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td><strong>{{ $user->id }}</strong></td>
                    
                    <td>
                        @php
                            $roleClass = match($user->role) {
                                'Client' => 'role-client',
                                'Utilisateur' => 'role-utilisateur',
                                'Visiteur' => 'role-visiteur',
                                'Directeur du Service Vente', 
                                'Directeur du Service Marketing' => 'role-admin',
                                'Membre du Service Vente',
                                'Membre du Service Marketing' => 'role-staff',
                                default => 'role-visiteur',
                            };
                        @endphp
                        <span class="badge {{ $roleClass }}">
                            {{ $user->role ?? 'Non défini' }}
                        </span>
                    </td>
                    
                    <td>{{ $user->genre }}</td>
                    
                    <td style="font-weight: bold; color: #333;">{{ $user->name }}</td>

                    <td><a href="mailto:{{ $user->email }}" class="email-link">{{ $user->email }}</a></td>
                    
                    <td>{{ $user->telephone }}</td>
                    
                    <td>{{ $user->datenaissance ? \Carbon\Carbon::parse($user->datenaissance)->format('d/m/Y') : '-' }}</td>

                    <td>{{ $user->ville }}</td>
                    <td>{{ $user->codepostal }}</td>
                    
                    <td>
                        @if($user->numrue && $user->nomrue)
                            {{ $user->numrue }} {{ $user->nomrue }}
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ $user->idcarte ?? '-' }}</td>

                    <td>
                        <span class="hash-password" title="{{ $user->password }}">
                            {{ $user->password }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px; display: flex; gap: 15px; font-size: 12px; color: #666; justify-content: flex-end; align-items: center;">
        <span>Légende :</span>
        <span class="badge role-utilisateur">Utilisateur (Inscrit)</span>
        <span class="badge role-client">Client (A réservé)</span>
        <span class="badge role-staff">Staff</span>
        <span class="badge role-admin">Direction</span>
        <span style="margin-left: 10px;">Total : <strong>{{ count($users) }}</strong> enregistrements.</span>
    </div>

    {{-- Chatbot BotMan --}}
    @include('layouts.chatbot')
</body>
</html>