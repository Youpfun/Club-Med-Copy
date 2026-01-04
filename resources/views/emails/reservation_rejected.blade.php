<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #0066cc; border-radius: 4px; }
        .alert-box { background: #f8d7da; padding: 15px; margin: 15px 0; border-left: 4px solid #dc3545; border-radius: 4px; }
        .help-box { background: #d4edda; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; border-radius: 4px; }
        .success-box { background: #d4edda; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
        .resort-card { background: white; border: 2px solid #28a745; border-radius: 8px; padding: 15px; margin: 10px 0; }
        .resort-card h4 { margin: 0 0 10px 0; color: #28a745; }
        .resort-card p { margin: 5px 0; font-size: 14px; }
        .trident { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Annulation de Réservation</h1>
            <p style="margin: 0;">Réservation #{{ $reservation->numreservation }}</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $reservation->user->name ?? 'Cher client' }}</strong>,</p>
            
            <p>Nous sommes au regret de vous informer que votre réservation a dû être annulée.</p>

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">Détails de la réservation annulée</h2>
                <table>
                    <tr>
                        <th>Numéro de réservation</th>
                        <td>#{{ $reservation->numreservation }}</td>
                    </tr>
                    <tr>
                        <th>Resort</th>
                        <td>{{ $reservation->resort->nomresort ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Dates prévues</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nombre de personnes</th>
                        <td>{{ $reservation->nbpersonnes }}</td>
                    </tr>
                    <tr>
                        <th>Montant</th>
                        <td>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</td>
                    </tr>
                </table>
            </div>

            <div class="alert-box">
                <h3 style="margin-top: 0; color: #721c24;">📋 Motif de l'annulation</h3>
                <p style="margin-bottom: 0;"><strong>{{ $reasonLabel }}</strong></p>
            </div>

            <div class="help-box">
                <h3 style="margin-top: 0; color: #155724;">💰 Remboursement</h3>
                <p style="margin-bottom: 0;">
                    Si vous avez déjà effectué un paiement, celui-ci sera remboursé intégralement dans un délai de 5 à 10 jours ouvrés sur le moyen de paiement utilisé lors de la réservation.
                </p>
            </div>

            @if(isset($alternativeResorts) && $alternativeResorts->count() > 0)
                <div class="success-box">
                    <h3 style="margin-top: 0; color: #155724;">🏨 Resorts alternatifs suggérés</h3>
                    <p>Nous vous proposons les resorts suivants qui pourraient vous intéresser :</p>
                    
                    @foreach($alternativeResorts as $altResort)
                        <div class="resort-card">
                            <h4>{{ $altResort->nomresort }}</h4>
                            <p>
                                <strong>📍 Pays :</strong> {{ $altResort->pays->nompays ?? 'N/A' }}
                            </p>
                            @if($altResort->nbtridents)
                                <p>
                                    <strong>Classement :</strong> 
                                    <span class="trident">
                                        @for($i = 0; $i < $altResort->nbtridents; $i++)🔱@endfor
                                    </span>
                                </p>
                            @endif
                            @if($altResort->descriptionresort)
                                <p style="font-size: 13px; color: #666;">
                                    {{ Str::limit($altResort->descriptionresort, 150) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                    
                    <p style="margin-top: 15px; font-weight: bold;">
                        N'hésitez pas à nous contacter pour réserver l'un de ces resorts !
                    </p>
                </div>
            @else
                <div class="info-box">
                    <h3 style="margin-top: 0;">🏖️ Envie de réserver à nouveau ?</h3>
                    <p style="margin-bottom: 0;">
                        Nous vous invitons à consulter nos autres destinations disponibles sur notre site. 
                        Notre équipe reste à votre disposition pour vous aider à trouver le séjour idéal.
                    </p>
                </div>
            @endif

            <p style="margin-top: 30px;">
                Nous vous prions de nous excuser pour ce désagrément et espérons avoir le plaisir de vous accueillir prochainement dans l'un de nos resorts.<br><br>
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
