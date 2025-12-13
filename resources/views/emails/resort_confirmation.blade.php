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
        .alert { background: #d1ecf1; padding: 15px; border-left: 4px solid #17a2b8; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmation de Réservation</h1>
        </div>

        <div class="content">
            <p>Bonjour {{ $reservation->resort->nomresort }},</p>
            <p>Nous avons le plaisir de vous notifier qu'une réservation a été confirmée pour votre établissement.</p>

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
                    <tr>
                        <th>Client</th>
                        <td>{{ $reservation->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email du client</th>
                        <td>{{ $reservation->user->email ?? 'N/A' }}</td>
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
                            <li><strong>{{ $chambre->typechambre->nomtype ?? 'N/A' }}</strong></li>
                        @endforeach
                    </ul>
                @else
                    <p>Aucune chambre spécifique réservée.</p>
                @endif
            </div>

            @if($reservation->activites->isNotEmpty())
                <div class="section">
                    <h2>Activités à la carte réservées</h2>
                    <ul>
                        @foreach($reservation->activites as $activite)
                            <li>
                                <strong>{{ $activite->activite->nomactivite ?? 'N/A' }}</strong><br>
                                Quantité : {{ $activite->quantite }} personne(s) - 
                                Prix : {{ number_format($activite->prix_unitaire, 2, ',', ' ') }} € / personne
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($reservation->transport)
                <div class="section">
                    <h2>Transport</h2>
                    <p>Transport inclus : {{ $reservation->transport->nomtransport ?? 'Oui' }}</p>
                </div>
            @endif

            <div class="alert">
                <h3 style="color: #0c5460; margin-top: 0;">Action requise</h3>
                <p>Merci de préparer l'accueil pour ce client et de vous assurer que toutes les dispositions nécessaires sont prises pour son arrivée.</p>
                <p>En cas de question ou de problème, veuillez contacter le service vente immédiatement.</p>
            </div>

            <p style="margin-top: 30px;">
                Cordialement,<br>
                <strong>Service Vente Club Méditerranée</strong>
            </p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement. Pour toute question, contactez le service vente.</p>
        </div>
    </div>
</body>
</html>
