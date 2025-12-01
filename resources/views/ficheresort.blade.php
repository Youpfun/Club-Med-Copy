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
	<div class="resort-visual-container" style="margin: 40px auto; max-width: 1100px; padding: 0 20px;">
    
    <div style="
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
        border: 1px solid rgba(0,0,0,0.05);
    ">
        @if($resort->photos->isNotEmpty())
            <img src="{{ asset($resort->photos->first()->cheminfichierphoto) }}" 
                 alt="{{ $resort->nomresort }}" 
                 style="
                    display: block;
                    width: 100%; 
                    height: 600px; 
                    object-fit: cover; 
                    object-position: center;
                    transition: transform 0.5s ease;
                 "
                 onmouseover="this.style.transform='scale(1.02)'" 
                 onmouseout="this.style.transform='scale(1)'">
        @else
            <div style="height: 500px; background: linear-gradient(45deg, #f3f4f6, #e5e7eb); display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                <span>Aucune image disponible</span>
            </div>
        @endif
    </div>

</div>
</div>
</div>
</div>
</div>

        </div>
    </div>
</body>
</html>
