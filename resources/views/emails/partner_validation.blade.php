<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #0066cc; color: white; padding: 16px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .btn { display: inline-block; padding: 10px 16px; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Validation des dates demandée</h2>
    </div>
    <div class="content">
        <p>Bonjour {{ $partenaire->nompartenaire }},</p>
        <p>Une réservation contenant vos activités est en attente de votre validation des dates.</p>
        <p><strong>Réservation #{{ $reservation->numreservation }}</strong><br>
           Resort : {{ $reservation->resort->nomresort ?? 'N/A' }}<br>
           Client : {{ $reservation->user->name ?? 'N/A' }}<br>
           Dates : {{ $reservation->datedebut }} → {{ $reservation->datefin }}</p>
        <p><a class="btn" href="{{ $tokenLink }}">Valider ou refuser la disponibilité</a></p>
        <p>Ce lien expirera bientôt. Merci de répondre rapidement.</p>
    </div>
</div>
</body>
</html>
