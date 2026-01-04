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
        .warning-box { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; border-radius: 4px; }
        .resort-comparison { display: flex; gap: 20px; margin: 20px 0; }
        .resort-card { flex: 1; background: white; padding: 15px; border-radius: 8px; border: 2px solid #ddd; }
        .resort-card.original { border-color: #dc3545; opacity: 0.7; }
        .resort-card.alternative { border-color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Proposition de Resort Alternatif</h1>
            <p style="margin: 0;">Votre avis est requis</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $reservation->user->name ?? 'Cher client' }}</strong>,</p>
            
            <div class="warning-box">
                <p style="margin: 0;"><strong>⚠️ Information importante</strong></p>
                <p style="margin: 10px 0 0 0;">
                    Le resort <strong>{{ $originalResort->nomresort }}</strong> n'est malheureusement pas disponible pour les dates de votre séjour.
                    Notre équipe commerciale vous propose un resort alternatif de qualité équivalente.
                </p>
            </div>

            @if($message)
                <div class="info-box">
                    <h3 style="margin-top: 0;">💬 Message de notre équipe</h3>
                    <p style="margin-bottom: 0;">{!! nl2br(e($message)) !!}</p>
                </div>
            @endif

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">Réservation #{{ $reservation->numreservation }}</h2>
                <table>
                    <tr>
                        <th>Dates du séjour</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th>Nombre de personnes</th>
                        <td>{{ $reservation->nbpersonnes }}</td>
                    </tr>
                    <tr>
                        <th>Prix total</th>
                        <td><strong>{{ number_format($reservation->prixtotal, 2, ',', ' ') }} €</strong></td>
                    </tr>
                </table>
            </div>

            <h3>Comparaison des Resorts</h3>
            
            <table>
                <tr>
                    <th></th>
                    <th style="color: #dc3545;">❌ Resort initial (indisponible)</th>
                    <th style="color: #28a745;">✅ Resort proposé</th>
                </tr>
                <tr>
                    <th>Nom</th>
                    <td style="text-decoration: line-through; opacity: 0.7;">{{ $originalResort->nomresort }}</td>
                    <td><strong>{{ $alternativeResort->nomresort }}</strong></td>
                </tr>
                <tr>
                    <th>Pays</th>
                    <td>{{ $originalResort->pays->nompays ?? 'N/A' }}</td>
                    <td>{{ $alternativeResort->pays->nompays ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Classement</th>
                    <td>
                        @if($originalResort->nbtridents)
                            @for($i = 0; $i < $originalResort->nbtridents; $i++)🔱@endfor
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($alternativeResort->nbtridents)
                            @for($i = 0; $i < $alternativeResort->nbtridents; $i++)🔱@endfor
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </table>

            @if($alternativeResort->descriptionresort)
                <div class="info-box">
                    <h4 style="margin-top: 0;">À propos de {{ $alternativeResort->nomresort }}</h4>
                    <p style="margin-bottom: 0;">{{ Str::limit($alternativeResort->descriptionresort, 300) }}</p>
                </div>
            @endif

            <div style="text-align: center; margin: 30px 0;">
                <p style="font-size: 18px; margin-bottom: 20px;"><strong>Acceptez-vous ce resort alternatif ?</strong></p>
                <a href="{{ $tokenLink }}?action=accept" class="button accept">✓ Accepter ce resort</a>
                <a href="{{ $tokenLink }}?action=refuse" class="button refuse">✗ Refuser</a>
            </div>

            <div class="warning-box">
                <p style="margin: 0;"><strong>⏰ Ce lien expire dans 7 jours</strong></p>
                <p style="margin: 10px 0 0 0;">
                    Si vous ne répondez pas dans ce délai, nous vous contacterons pour discuter d'autres options.
                </p>
            </div>

            <p style="margin-top: 30px;">
                Si vous avez des questions, n'hésitez pas à nous contacter.<br><br>
                Cordialement,<br>
                <strong>Service Vente Club Méditerranée</strong>
            </p>
        </div>

        <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0;">Ce lien de réponse expire dans 7 jours</p>
            <p style="margin: 5px 0 0 0;">Cet email a été généré automatiquement</p>
        </div>
    </div>
</body>
</html>
