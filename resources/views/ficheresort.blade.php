<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="/resorts">← Retour à la liste</a>

    <div class="detail-resort">
        
        <h1>{{ $resort->nomresort }}</h1>
        
        <p>{{ $resort->descriptionresort }}</p>
        
        <div class="info">
            <div>nombre de chambre : {{ $resort->nbchambrestotal }}</div>
            <div>moyenne des avis : {{ $resort->moyenneavis }}</div>
            <hr>
            @if($resort->avis->isEmpty())
                <p>Aucun avis pour le moment.</p>
            @else
                @foreach($resort->avis as $unAvis)
                    <div class="avis">
                        <p>Note : {{ $unAvis->noteavis }}/5</p>
                        <p>{{ $unAvis->commentaire }}</p>
                        <p>Publié le : {{ $unAvis->datepublication }}</p>
                    </div>
                @endforeach
            @endif
            <hr>
            <div>pays : {{ $resort->pays->nompays ?? 'pas de pays défini' }}</div> 
            <hr>
            @if($resort->documentation)
                <img src="{{ $resort->documentation->url }}">
            @else
                <p>Pas d'image disponible</p>
            @endif

        </div>
    </div>
</body>
</html>