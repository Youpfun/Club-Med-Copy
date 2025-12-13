<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réponse enregistrée</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f6fa; color: #2d3436; }
        .card { max-width: 520px; margin: 60px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: center; }
        .ok { color: #10b981; }
        .ko { color: #ef4444; }
    </style>
</head>
<body>
<div class="card">
    @if($status === 'accepted')
        <h1 class="ok">Merci, disponibilité validée</h1>
        <p>Votre réponse a été enregistrée.</p>
    @else
        <h1 class="ko">Disponibilité refusée</h1>
        <p>Votre réponse a été enregistrée.</p>
    @endif
</div>
</body>
</html>
