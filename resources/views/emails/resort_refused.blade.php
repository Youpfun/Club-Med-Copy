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
        .warning-box { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; border-radius: 4px; }
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
            <h1>⚠️ Mise à jour de votre réservation</h1>
            <p style="margin: 0;">Resort indisponible</p>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $reservation->user->name ?? 'Cher client' }}</strong>,</p>
            
            <p>Nous vous informons que le resort <strong>{{ $resort->nomresort }}</strong> n'est malheureusement pas disponible pour les dates de votre séjour.</p>

            <div class="info-box">
                <h2 style="margin-top: 0; color: #0066cc;">Réservation #{{ $reservation->numreservation }}</h2>
                <table>
                    <tr>
                        <th>Resort demandé</th>
                        <td>{{ $resort->nomresort }}</td>
                    </tr>
                    <tr>
                        <th>Dates demandées</th>
                        <td>{{ $reservation->datedebut->format('d/m/Y') }} - {{ $reservation->datefin->format('d/m/Y') }}</td>
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

            @if($comment)
                <div class="info-box">
                    <h3 style="margin-top: 0;">💬 Commentaire du resort</h3>
                    <p style="margin-bottom: 0;">{!! nl2br(e($comment)) !!}</p>
                </div>
            @endif

            @if($alternativeResorts && $alternativeResorts->count() > 0)
                <div class="success-box">
                    <h3 style="margin-top: 0; color: #155724;">🏨 Resorts alternatifs suggérés</h3>
                    <p>Nous vous proposons les resorts suivants, disponibles aux mêmes dates :</p>
                    
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
                        Notre équipe commerciale vous contactera très prochainement pour vous proposer l'une de ces alternatives.
                    </p>
                </div>
            @else
                <div class="warning-box">
                    <h3 style="margin-top: 0;">📞 Que se passe-t-il maintenant ?</h3>
                    <p style="margin-bottom: 0;">
                        Notre équipe commerciale va étudier votre dossier et vous proposer très prochainement un resort alternatif de qualité équivalente.
                        Vous recevrez un email avec notre proposition sous 24 à 48 heures.
                    </p>
                </div>
            @endif

            <p style="margin-top: 30px;">
                Si vous avez des questions, n'hésitez pas à nous contacter.<br><br>
                Cordialement,<br>
                <strong>Service Client Club Méditerranée</strong>
            </p>
        </div>

        <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px;">
            <p style="margin: 0;">Cet email a été généré automatiquement</p>
            <p style="margin: 5px 0 0 0;">Club Méditerranée - Service Réservations</p>
        </div>
    </div>
</body>
</html>
