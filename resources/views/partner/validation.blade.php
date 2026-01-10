<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Validation de disponibilité</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; color: #2d3436; }
        .card { max-width: 640px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        h1 { margin-top: 0; color: #0066cc; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .actions { display: flex; gap: 12px; margin-top: 20px; }
        button { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .accept { background: #10b981; color: #fff; }
        .refuse { background: #ef4444; color: #fff; }
    </style>
</head>
<body>
<div class="card">
    <h1>Validation de disponibilité</h1>
    <p>Bonjour {{ $partenaire->nompartenaire ?? 'Partenaire' }}, merci d'indiquer si les dates sont disponibles.</p>
    <p><strong>Réservation #{{ $reservation->numreservation }}</strong></p>
    <p>Resort : {{ $reservation->resort->nomresort ?? 'N/A' }}<br>
       Client : {{ $reservation->user->name ?? 'N/A' }}<br>
       Dates : {{ $reservation->datedebut }} → {{ $reservation->datefin }}</p>

    <h3>Activités concernées</h3>
    <table>
        <thead><tr><th>Activité</th><th>Quantité</th></tr></thead>
        <tbody>
        @foreach($reservation->activites as $ra)
            <tr>
                <td>{{ $ra->activite->nomactivite ?? 'N/A' }}</td>
                <td>{{ $ra->quantite }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <form method="POST" action="{{ url('/partner/validate/' . $token) }}">
        @csrf
        <div class="actions">
            <button name="action" value="accept" class="accept" type="submit">Valider</button>
            <button name="action" value="refuse" class="refuse" type="submit">Refuser</button>
        </div>
    </form>
</div>

{{-- Chatbot BotMan --}}
@include('layouts.chatbot')
</body>
</html>
