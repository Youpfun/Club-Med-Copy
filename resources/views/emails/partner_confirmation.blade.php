<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #008f4c; color: white; padding: 16px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Confirmation de réservation</h2>
    </div>
    <div class="content">
        <p>Bonjour {{ $partenaire->nompartenaire }},</p>
        <p>La réservation #{{ $reservation->numreservation }} est désormais confirmée.</p>
        <p>Resort : {{ $reservation->resort->nomresort ?? 'N/A' }}<br>
           Client : {{ $reservation->user->name ?? 'N/A' }}<br>
           Dates : {{ $reservation->datedebut }} → {{ $reservation->datefin }}</p>
        <p>Merci pour votre collaboration.</p>
    </div>
</div>
</body>
</html>
