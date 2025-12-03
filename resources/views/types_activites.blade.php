<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types d'activités - {{ $resort->nomresort }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        .type-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
        .type-card { 
            background: #f4f4f4; 
            padding: 30px; 
            border-radius: 12px; 
            width: 250px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h2 { color: #00457C; }
        .btn-activite {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #00457C;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-activite:hover { background-color: #003366; }
        .back-link { text-decoration: none; color: #666; display: block; margin-bottom: 20px; text-align: left;}
    </style>
</head>
<body>
      
    <a href="/ficheresort/{{ $resort->numresort }}" class="back-link">← Retour au resort</a>

    <h1>Quelles activités cherchez-vous à {{ $resort->nomresort }} ?</h1>

    <div class="type-container">
        @forelse($types as $type)
            <div class="type-card">
                <h2>{{ $type->nomtypeactivite }}</h2>
                <p>{{ $type->desctypeactivite }}</p>
                
                <a href="{{ route('resort.activites.detail', ['id' => $resort->numresort, 'typeId' => $type->numtypeactivite]) }}" class="btn-activite">
                    Voir les activités
                </a>
            </div>
        @empty
            <p>Aucun type d'activité défini pour ce resort.</p>
        @endforelse
    </div>

</body>
</html>