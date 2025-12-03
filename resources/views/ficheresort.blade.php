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

            <div class="resort-map-container" style="margin: 30px 0;">
                <h2 style="margin-bottom: 10px; color:#00457C;">Localisation du resort</h2>
                <p style="margin-bottom: 10px; color:#4b5563;">
                    La carte ci-dessous indique l’emplacement approximatif de
                    <strong>{{ $resort->nomresort }}</strong>
                    @if($resort->pays && $resort->pays->nompays)
                        , {{ $resort->pays->nompays }}
                    @endif
                    .
                </p>
                <div style="
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
                    border: 1px solid rgba(0,0,0,0.05);
                ">
                    <iframe
                        width="100%"
                        height="400"
                        style="border:0;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q={{ urlencode($resort->nomresort . ' ' . ($resort->pays->nompays ?? '')) }}&output=embed">
                    </iframe>
                </div>
            </div>

            @php
                // Correspondance nom resort -> fichier image (en minuscules sans espaces)
                $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                $imagePath = 'img/ressort/' . $imageName;
                $fullPath = public_path($imagePath);
            @endphp

            <a href="{{ route('resort.activites', ['id' => $resort->numresort]) }}" 
            style="display: inline-block; padding: 10px 20px; background-color: #00457C; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Voir les activités
            </a>
            
	<div class="resort-visual-container" style="margin: 40px auto; max-width: 1100px; padding: 0 20px;">
    
    <div style="
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.15); 
        border: 1px solid rgba(0,0,0,0.05);
    ">
        @if(file_exists($fullPath))
            <img src="{{ asset($imagePath) }}" 
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
                <span>Aucune image disponible<br><small style="font-size: 0.8em;">Fichier attendu : {{ $imageName }}</small></span>
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
