<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeActivite->nomtypeactivite }} - {{ $resort->nomresort }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        ul { list-style-type: none; padding: 0; }
        li { background: #f9f9f9; margin: 10px 0; padding: 20px; border-radius: 8px; border-left: 5px solid #00457C; }
        h2 { margin-top: 0; color: #00457C; }
        .tag { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.9em; }
        .incluse { color: #155724; background-color: #d4edda; }
        .carte { color: #856404; background-color: #fff3cd; }
        a { text-decoration: none; color: #666; }
    </style>
</head>
<body>
      
    <a href="{{ route('resort.types', ['id' => $resort->numresort]) }}">← Retour aux types d'activités</a>

    <h1>{{ $typeActivite->nomtypeactivite }} à {{ $resort->nomresort }}</h1>
    <p><i>{{ $typeActivite->desctypeactivite }}</i></p>

    @if($activites->isEmpty())
        <p>Aucune activité n'est listée dans cette catégorie pour le moment.</p>
    @else
        <ul>
            @foreach($activites as $activite)
                <li>
                    <h2>{{ $activite->nomactivite }}</h2>
                    <p>{{ $activite->descriptionactivite }}</p>
                    <p><strong>Durée :</strong> {{ $activite->dureeactivite }} minutes</p>
                    <p>
                        @if($activite->estincluse)
                            <span class="tag incluse">Incluse dans le forfait</span>
                        @else
                            <span class="tag carte">À la carte (supplément)</span>
                        @endif
                    </p>
                </li>
            @endforeach
        </ul>
    @endif

</body>
</html>