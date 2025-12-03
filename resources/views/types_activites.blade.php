<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types d'activités - {{ $resort->nomresort }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,1,0" />
    
    <script>
        tailwind.config = {
            theme: { extend: { colors: { clubmed: '#00457C', clubmedHover: '#003366', } } }
        }
    </script>
    <style>
        /* Règle pour les icônes Google */
        .material-symbols-rounded { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col font-sans text-gray-800">
    @include('layouts.header')

    <div class="w-full max-w-7xl mx-auto p-6">
        <a href="/ficheresort/{{ $resort->numresort }}" class="inline-flex items-center text-gray-500 hover:text-clubmed transition-colors duration-300 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au resort
        </a>
    </div>

    <div class="flex-grow flex flex-col items-center justify-center p-6 pb-20">
        <h1 class="text-3xl md:text-4xl font-bold text-clubmed mb-12 text-center">
            Quelles activités cherchez-vous à <br><span class="text-black">{{ $resort->nomresort }}</span> ?
        </h1>

        <div class="flex flex-wrap justify-center gap-8 w-full max-w-6xl">
            
            {{-- TABLEAU DE CORRESPONDANCE NOMS GOOGLE ICONS --}}
            @php
                $googleIcons = [
                    'Ski' => 'downhill_skiing',
                    'Hiver' => 'ac_unit',
                    'Nautique' => 'sailing',
                    'Plage' => 'beach_access',
                    'Bien-être' => 'spa',
                    'Spa' => 'self_improvement',
                    'Enfant' => 'child_care',
                    'Famille' => 'family_restroom',
                    'Tennis' => 'sports_tennis',
                    'Golf' => 'golf_course',
                    'default' => 'sports' // Icône générique
                ];
            @endphp

            @forelse($types as $type)
                @php
                    $monIcone = $googleIcons['default'];
                    foreach ($googleIcons as $cle => $iconeName) {
                        if (stripos($type->nomtypeactivite, $cle) !== false) {
                            $monIcone = $iconeName;
                            break;
                        }
                    }
                @endphp

                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 p-8 w-full sm:w-80 flex flex-col items-center text-center border border-gray-100">
                    
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                       <span class="material-symbols-rounded text-5xl text-clubmed">
                           {{ $monIcone }}
                       </span>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $type->nomtypeactivite }}</h2>
                    <p class="text-gray-500 mb-8 flex-grow leading-relaxed">{{ $type->desctypeactivite }}</p>
                    <a href="{{ route('resort.activites.detail', ['id' => $resort->numresort, 'typeId' => $type->numtypeactivite]) }}" class="bg-clubmed hover:bg-clubmedHover text-white px-8 py-3 rounded-full font-semibold transition-colors duration-300 shadow-md w-full">
                        Voir les activités
                    </a>
                </div>
            @empty
                <p>Aucun type d'activité.</p>
            @endforelse
        </div>
    </div>
</body>
</html>