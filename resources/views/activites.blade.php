<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activités - {{ $resort->nomresort }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        ul { list-style-type: none; padding: 0; }
        li { background: #f4f4f4; margin: 10px 0; padding: 15px; border-radius: 8px; }
        h2 { margin-top: 0; color: #00457C; }
        a { text-decoration: none; color: #666; }
        a:hover { color: #000; }
    </style>
</head>
<body>
      
    <a href="/ficheresort/{{ $resort->numresort }}">← Retour à la liste des resorts</a>

    <h1>Activités pour le resort : {{ $resort->nomresort }}</h1>

    @if($activites->isEmpty())
        <p>Aucune activité n'est listée pour ce resort pour le moment.</p>
    @else
        <ul>
            @foreach($activites as $activite)
                <li>
                    <h2>{{ $activite->nomactivite }}</h2>
                    <p>Description : {{ $activite->descriptionactivite }}</p>
                    <p>Durée :  {{ $activite->dureeactivite }} minutes</p>
                    <p>
                        @if($activite->estincluse)
                            <span style="color:green; font-weight:bold;"> Incluse dans le forfait</span>
                        @else
                            <span style="color:orange; font-weight:bold;"> À la carte (supplément)</span>
                        @endif
                    </p>
                </li>
            @endforeach
        </ul>
    @endif

</body>
</html>