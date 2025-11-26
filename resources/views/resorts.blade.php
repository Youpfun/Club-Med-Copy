<!DOCTYPE html> <html lang="fr"> <head> <meta charset="UTF-8"> <title>Nos Resorts</title> 
 <a href="{{ url('/') }}">Retour à l'accueil</a>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: 
        sans-serif; } th { background-color: #28a745; color: white; padding: 10px; text-align: 
        left; } td { border: 1px solid #ddd; padding: 8px; } tr:nth-child(even) { 
        background-color: #f2f2f2; }
        .filter-form { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px; }
        .filter-form select { padding: 8px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .filter-form button { padding: 8px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .filter-form button:hover { background: #218838; }
        .filter-form a { padding: 8px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px; }
    </style> </head> <body> <h1>Liste des Resorts Club Med</h1>
    
    <form action="{{ url('/resorts') }}" method="GET" class="filter-form">
        <label for="typeclub">Type de club ?</label>
        <select name="typeclub" id="typeclub">
            <option value="">Tous les types de clubs</option>
            @foreach($typeclubs ?? [] as $id => $nom)
                <option value="{{ $id }}" {{ request('typeclub') == $id ? 'selected' : '' }}>{{ $nom }}</option>
            @endforeach
        </select>
        <button type="submit">Rechercher</button>
        @if(request('typeclub'))
            <a href="{{ url('/resorts') }}">Réinitialiser</a>
        @endif
    </form>
    
    @if(request('typeclub'))
        <p><strong>{{ count($resorts) }}</strong> resort(s) trouvé(s)</p>
    @endif <table> <thead> <tr> <th>Nom du 
                Resort</th> <th>Pays (Code)</th> <th>Tridents</th> <th>Note Moyenne</th> 
                <th>Chambres</th> <th>Description</th>
            </tr> </thead> <tbody> @foreach($resorts as $resort) <tr> <td><button><strong>{{ 
                    $resort->nomresort }}</strong></td></button>
                    
                    <td>{{ $resort->codepays }}</td>
                    
                    <td>{{ $resort->nbtridents }} 🔱</td> <td>{{ $resort->moyenneavis }} / 
                    5</td> <td>{{ $resort->nbchambrestotal }}</td> <td>{{ 
                    $resort->descriptionresort }}</td>
                </tr> @endforeach </tbody> </table> </body>
</html>
