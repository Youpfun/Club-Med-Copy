<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $typeActivite->nomtypeactivite }} - {{ $resort->nomresort }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        clubmed: '#00457C',
                        clubmedHover: '#003366',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-800 pb-20">

    <div class="max-w-4xl mx-auto p-6 pt-8">
        <a href="{{ route('resort.types', ['id' => $resort->numresort]) }}" 
           class="inline-flex items-center text-gray-500 hover:text-clubmed transition-colors duration-300 font-medium mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux types d'activités
        </a>

        <div class="border-b border-gray-200 pb-6 mb-8">
            <h1 class="text-3xl font-bold text-clubmed mb-2">
                {{ $typeActivite->nomtypeactivite }} 
                <span class="text-gray-400 font-normal text-xl ml-2">à {{ $resort->nomresort }}</span>
            </h1>
            <p class="text-lg text-gray-600 italic">
                {{ $typeActivite->desctypeactivite }}
            </p>
        </div>

        @if($activites->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-10 text-center border border-gray-100">
                <p class="text-gray-500 text-lg">Aucune activité n'est listée dans cette catégorie pour le moment.</p>
            </div>
        @else
            <ul class="space-y-6">
                @foreach($activites as $activite)
                    <li class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 relative">
                        
                        <div class="absolute left-0 top-0 bottom-0 w-2 bg-clubmed"></div>

                        <div class="p-6 pl-8"> <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                                
                                <div class="flex-grow">
                                    <h2 class="text-xl font-bold text-clubmed mb-2">
                                        {{ $activite->nomactivite }}
                                    </h2>
                                    <p class="text-gray-600 mb-4 leading-relaxed">
                                        {{ $activite->descriptionactivite }}
                                    </p>
                                    
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-clubmed" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Durée : <span class="font-semibold ml-1">{{ $activite->dureeactivite }} minutes</span>
                                    </div>
                                </div>

                                <div class="flex-shrink-0">
                                    @if($activite->estincluse)
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Inclus
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd" />
                                            </svg>
                                            À la carte (Supplément)
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</body>
</html>