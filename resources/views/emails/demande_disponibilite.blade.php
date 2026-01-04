<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0066cc; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        .highlight-box { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .btn { display: inline-block; padding: 15px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; margin: 10px 5px; }
        .btn-primary { background: #0066cc; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Demande de Disponibilité</h1>
            <p style="margin: 0;">Club Méditerranée - Service Marketing</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            
            <p>Le service Marketing de Club Méditerranée souhaite connaître vos disponibilités pour un <strong>potentiel nouveau séjour</strong>.</p>

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">📅 Période demandée</h2>
                <table>
                    <tr>
                        <th>Resort</th>
                        <td><strong>{{ $demande->resort->nomresort ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <th>Date de début</th>
                        <td>{{ $demande->date_debut->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Date de fin</th>
                        <td>{{ $demande->date_fin->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Durée</th>
                        <td>{{ $demande->date_debut->diffInDays($demande->date_fin) }} nuits</td>
                    </tr>
                    @if($demande->nb_chambres)
                    <tr>
                        <th>Nombre de chambres souhaitées</th>
                        <td>{{ $demande->nb_chambres }}</td>
                    </tr>
                    @endif
                    @if($demande->nb_personnes)
                    <tr>
                        <th>Nombre de personnes prévues</th>
                        <td>{{ $demande->nb_personnes }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @if($demande->message)
                <div class="highlight-box">
                    <h3 style="margin-top: 0;">💬 Message du service Marketing</h3>
                    <p style="margin-bottom: 0;">{!! nl2br(e($demande->message)) !!}</p>
                </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <p><strong>Merci de nous indiquer vos disponibilités pour cette période :</strong></p>
                <a href="{{ $tokenLink }}" class="btn btn-primary" style="color: white;">
                    📝 Répondre à la demande
                </a>
            </div>

            <div class="info-box">
                <h3 style="margin-top: 0;">ℹ️ À propos de cette demande</h3>
                <p style="margin-bottom: 0;">
                    Cette demande est <strong>exploratoire</strong>. Elle nous permet d'évaluer la faisabilité d'un nouveau séjour avant de le proposer à nos clients. 
                    Aucune réservation n'est encore effectuée.
                </p>
            </div>

            <p style="margin-top: 30px;">
                Cordialement,<br>
                <strong>{{ $demande->user->name ?? 'Service Marketing' }}</strong><br>
                Club Méditerranée - Direction Marketing
            </p>
        </div>

        <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0;">Ce lien expire dans 7 jours</p>
            <p style="margin: 5px 0 0 0;">Club Méditerranée - Service Marketing</p>
        </div>
    </div>
</body>
</html>
