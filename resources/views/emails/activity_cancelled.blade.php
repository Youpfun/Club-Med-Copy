<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f59e0b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header.partner-issue { background: #6366f1; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        .warning-box { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; border-radius: 4px; }
        .partner-notice { background: #e0e7ff; padding: 15px; margin: 15px 0; border-left: 4px solid #6366f1; border-radius: 4px; }
        .activity-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .activity-card h4 { margin: 0 0 10px 0; color: #dc3545; }
        .activity-card p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .refund-box { background: #d4edda; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; border-radius: 4px; }
        .total { font-size: 18px; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ ($cancellationReason ?? 'default') === 'partner_no_response' ? 'partner-issue' : '' }}">
            @if(($cancellationReason ?? 'default') === 'partner_no_response')
                <h1>Activité(s) Non Disponible(s)</h1>
                <p style="margin: 0;">Partenaire non disponible - Réservation #{{ $reservation->numreservation }}</p>
            @else
                <h1>{{ $isSingleActivity ? 'Activité Annulée' : 'Activités Annulées' }}</h1>
                <p style="margin: 0;">Réservation #{{ $reservation->numreservation }}</p>
            @endif
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $reservation->user->name ?? 'Cher client' }}</strong>,</p>
            
            @if(($cancellationReason ?? 'default') === 'partner_no_response')
                <p>Nous vous contactons au sujet de votre réservation. Malheureusement, {{ count($cancelledActivities) > 1 ? 'certaines activités que vous avez réservées ne sont pas disponibles' : 'une activité que vous avez réservée n\'est pas disponible' }} car le partenaire prestataire n'a pas confirmé sa disponibilité dans les délais impartis.</p>

                <div class="partner-notice">
                    <h3 style="margin-top: 0; color: #4338ca;">Pourquoi cette annulation ?</h3>
                    <p style="margin-bottom: 0;">
                        Nos activités sont proposées par des partenaires locaux qui doivent confirmer leur disponibilité. 
                        Malgré nos relances, {{ count($cancelledActivities) > 1 ? 'les partenaires concernés n\'ont' : 'le partenaire concerné n\'a' }} pas répondu à notre demande de disponibilité pour les dates de votre séjour.
                        Nous vous prions de nous excuser pour ce désagrément.
                    </p>
                </div>
            @else
                <p>Nous vous informons que {{ $isSingleActivity ? 'l\'activité suivante a été annulée' : 'les activités suivantes ont été annulées' }} de votre réservation.</p>
            @endif

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">Votre séjour</h2>
                <table>
                    <tr>
                        <th>Resort</th>
                        <td>{{ $reservation->resort->nomresort ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Dates</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #dc3545;">{{ ($cancellationReason ?? 'default') === 'partner_no_response' ? 'Activité(s) non disponible(s)' : ($isSingleActivity ? 'Activité annulée' : 'Activités annulées') }}</h3>
            
            @foreach($cancelledActivities as $activity)
                <div class="activity-card">
                    <h4>{{ $activity['nom'] }}</h4>
                    <p><strong>Quantité :</strong> {{ $activity['quantite'] }}</p>
                    <p><strong>Prix unitaire :</strong> {{ number_format($activity['prix_unitaire'], 2, ',', ' ') }} €</p>
                    <p><strong>Sous-total :</strong> {{ number_format($activity['total'], 2, ',', ' ') }} €</p>
                    @if(isset($activity['partenaire']) && ($cancellationReason ?? 'default') === 'partner_no_response')
                        <p><strong>Partenaire :</strong> {{ $activity['partenaire'] }}</p>
                    @endif
                </div>
            @endforeach

            <div class="refund-box">
                <h3 style="margin-top: 0; color: #155724;">Remboursement</h3>
                <p>
                    Le montant correspondant sera déduit de votre facture ou remboursé si le paiement a déjà été effectué.
                </p>
                <p class="total">
                    Montant à rembourser : {{ number_format($totalRefund, 2, ',', ' ') }} €
                </p>
            </div>

            <div class="warning-box">
                <h3 style="margin-top: 0;">Votre séjour est maintenu</h3>
                <p style="margin-bottom: 0;">
                    Cette annulation concerne uniquement {{ $isSingleActivity ? 'l\'activité mentionnée' : 'les activités mentionnées' }}. 
                    Votre réservation de séjour au resort <strong>{{ $reservation->resort->nomresort ?? 'N/A' }}</strong> reste valide.
                </p>
            </div>

            @if(($cancellationReason ?? 'default') === 'partner_no_response')
                <div class="info-box">
                    <h3 style="margin-top: 0; color: #0066cc;">Besoin d'autres activités ?</h3>
                    <p style="margin-bottom: 0;">
                        Si vous souhaitez réserver d'autres activités pour votre séjour, n'hésitez pas à consulter notre catalogue ou à nous contacter. 
                        Notre équipe se fera un plaisir de vous proposer des alternatives.
                    </p>
                </div>
            @endif

            <p style="margin-top: 30px;">
                Si vous avez des questions concernant cette annulation, n'hésitez pas à nous contacter.<br><br>
                Cordialement,<br>
                <strong>Service Commercial Club Méditerranée</strong>
            </p>
        </div>

        <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0;">Cet email a été généré automatiquement</p>
            <p style="margin: 5px 0 0 0;">Club Méditerranée - Service Réservations</p>
        </div>
    </div>
</body>
</html>
