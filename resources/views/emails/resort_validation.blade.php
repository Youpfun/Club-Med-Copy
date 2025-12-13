<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0066cc; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .button { display: inline-block; padding: 15px 30px; margin: 10px 5px; text-decoration: none; border-radius: 5px; font-weight: bold; text-align: center; }
        .accept { background: #28a745; color: white; }
        .refuse { background: #dc3545; color: white; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .alert { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 4px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Nouvelle Réservation</h1>
            <p style="margin: 0;">Validation requise</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $resort->nomresort }}</strong>,</p>
            
            <p>Vous avez reçu une nouvelle demande de réservation qui nécessite votre validation.</p>

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">Réservation #{{ $reservation->numreservation }}</h2>
                <table>
                    <tr>
                        <th>Client</th>
                        <td>{{ $reservation->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email du client</th>
                        <td>{{ $reservation->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Nombre de personnes</th>
                        <td>{{ $reservation->nbpersonnes }}</td>
                    </tr>
                    <tr>
                        <th>Date d'arrivée</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Date de départ</th>
                        <td>{{ $reservation->datefin->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Durée du séjour</th>
                        <td>{{ $reservation->datedebut->diffInDays($reservation->datefin) }} jours</td>
                    </tr>
                    <tr>
                        <th>Prix total</th>
                        <td><strong>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</strong></td>
                    </tr>
                </table>
            </div>

            @if($reservation->chambres && $reservation->chambres->isNotEmpty())
                <div class="info-box">
                    <h3 style="margin-top: 0;">Chambres réservées</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($reservation->chambres as $chambre)
                            <li>{{ $chambre->typechambre->nomtype ?? 'N/A' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($reservation->activites && $reservation->activites->isNotEmpty())
                <div class="info-box">
                    <h3 style="margin-top: 0;">Activités à la carte</h3>
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($reservation->activites as $activite)
                            <li>
                                <strong>{{ $activite->activite->nomactivite ?? 'N/A' }}</strong><br>
                                {{ $activite->quantite }} personne(s) - {{ number_format($activite->prix_unitaire, 2, ',', ' ') }} € / personne
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert">
                <strong>⏰ Action requise</strong><br>
                Merci de valider ou refuser cette réservation dans les <strong>3 jours</strong>. 
                Après validation de votre part, les partenaires des activités seront automatiquement contactés.
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p style="font-size: 18px; margin-bottom: 20px;"><strong>Pouvez-vous accepter cette réservation ?</strong></p>
                <a href="{{ $tokenLink }}" class="button accept">✓ Valider la réservation</a>
            </div>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
                <strong>Important :</strong> Si vous refusez cette réservation, le client et le service vente seront notifiés automatiquement.
            </p>

            <p style="margin-top: 30px;">
                Cordialement,<br>
                <strong>Service Vente Club Méditerranée</strong>
            </p>
        </div>

        <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0;">Ce lien de validation expire dans 3 jours</p>
            <p style="margin: 5px 0 0 0;">Cet email a été généré automatiquement</p>
        </div>
    </div>
</body>
</html>
