<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0066cc; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { background: #333; color: white; padding: 10px; text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #0066cc; color: white; }
        .section { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($recipientType === 'client')
                <h1>Confirmation de Votre Séjour</h1>
            @else
                <h1>Confirmation de Séjour Club Méditerranée</h1>
            @endif
        </div>

        <div class="content">
            @if($recipientType === 'client')
                <p>Cher(e) {{ $reservation->user->name }},</p>
                <p>Nous avons le plaisir de confirmer votre séjour auprès de <strong>{{ $reservation->resort->nomresort ?? 'N/A' }}</strong>. Nous vous remercions de votre confiance !</p>
            @else
                <p>Bonjour,</p>
                <p>Nous avons le plaisir de confirmer un séjour auprès de nos partenaires.</p>
            @endif

            <div class="section">
                <h2>Détails de la réservation</h2>
                <table>
                    <tr>
                        <th>Numéro de réservation</th>
                        <td>{{ $reservation->numreservation }}</td>
                    </tr>
                    <tr>
                        <th>Resort</th>
                        <td>{{ $reservation->resort->nomresort ?? 'N/A' }}</td>
                    </tr>
                    @if($recipientType !== 'client')
                    <tr>
                        <th>Client</th>
                        <td>{{ $reservation->user->name ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Arrivée</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Départ</th>
                        <td>{{ $reservation->datefin->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nombre de personnes</th>
                        <td>{{ $reservation->nbpersonnes }}</td>
                    </tr>
                    <tr>
                        <th>Prix total</th>
                        <td>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Chambres réservées</h2>
                @if($reservation->chambres->isNotEmpty())
                    <ul>
                        @foreach($reservation->chambres as $chambre)
                            <li>{{ $chambre->typechambre->nomtype ?? 'N/A' }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>Aucune chambre trouvée.</p>
                @endif
            </div>

            <div class="section">
                <h2>Activités à la carte</h2>
                @if($reservation->activites->isNotEmpty())
                    <ul>
                        @foreach($reservation->activites as $activite)
                            <li>{{ $activite->activite->nomactivite ?? 'N/A' }} ({{ $activite->quantite }} personne(s) - {{ number_format($activite->prix_unitaire, 2, ',', ' ') }} € par personne)</li>
                        @endforeach
                    </ul>
                @else
                    <p>Aucune activité à la carte réservée.</p>
                @endif
            </div>

            @if($recipientType === 'client')
                <div class="section" style="background: #d4edda; padding: 15px; border-left: 4px solid #28a745; border-radius: 4px;">
                    <h3 style="color: #155724; margin-top: 0;">Votre Séjour Confirmé ✓</h3>
                    <p>Merci pour votre réservation ! Nous vous souhaitons un magnifique séjour à {{ $reservation->resort->nomresort ?? 'Club Méditerranée' }}. Si vous avez des questions ou besoin d'assistance, n'hésitez pas à nous contacter.</p>
                </div>
            @elseif($recipientType === 'resort')
                <div class="section" style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; border-radius: 4px;">
                    <h3 style="color: #856404; margin-top: 0;">Pour le Resort</h3>
                    <p>Merci de confirmer la disponibilité et la faisabilité de ce séjour. Veuillez nous contacter en cas de problème ou si vous avez besoin de précisions supplémentaires.</p>
                </div>
            @elseif($recipientType === 'partenaire')
                <div class="section" style="background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; border-radius: 4px;">
                    <h3 style="color: #0c5460; margin-top: 0;">Pour le Partenaire</h3>
                    <p>Ce client a réservé des activités auprès de votre structure. Merci de confirmer votre disponibilité pour les dates mentionnées ci-dessus et de nous contacter si vous avez des questions.</p>
                </div>
            @endif

            <p style="margin-top: 30px;">
                Cordialement,<br>
                <strong>Service Vente Club Méditerranée</strong>
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement. Veuillez ne pas répondre à cet email.</p>
        </div>
    </div>
</body>
</html>
