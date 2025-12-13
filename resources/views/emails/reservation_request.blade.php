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
            <h1>Demande de Confirmation de Dates</h1>
        </div>

        <div class="content">
            <p>Cher partenaire {{ $partenaire->nompartenaire }},</p>
            <p>Une nouvelle réservation a été effectuée pour des activités que vous proposez. Veuillez confirmer si les dates sont disponibles.</p>

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
                        <th>Date de début</th>
                        <td>{{ $reservation->datedebut }}</td>
                    </tr>
                    <tr>
                        <th>Date de fin</th>
                        <td>{{ $reservation->datefin }}</td>
                    </tr>
                    <tr>
                        <th>Nombre de personnes</th>
                        <td>{{ $reservation->nbpersonnes }}</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h2>Activités réservées</h2>
                <table>
                    <tr>
                        <th>Activité</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                    </tr>
                    @foreach($reservation->activites as $reservationActivite)
                        @if($reservationActivite->numpartenaire == $partenaire->numpartenaire && $reservationActivite->activite)
                            <tr>
                                <td>{{ $reservationActivite->activite->nomactivite }}</td>
                                <td>{{ $reservationActivite->prix_unitaire }} €</td>
                                <td>{{ $reservationActivite->quantite }}</td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>

            <p>Veuillez nous répondre au plus vite pour confirmer la disponibilité.</p>
            <p>Cordialement,<br>L'équipe Club Méditerranée</p>
        </div>

        <div class="footer">
            <p>&copy; 2025 Club Méditerranée. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>