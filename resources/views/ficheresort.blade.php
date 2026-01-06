<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort - {{ $resort->nomresort }} | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Newsreader:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen bg-clubmed-beige font-sans">
    @include('layouts.header')

    <div class="max-w-6xl mx-auto px-4 py-8">
        <a href="/resorts" class="inline-flex items-center text-sm text-clubmed-blue hover:text-clubmed-blue-dark mb-6 font-medium transition-colors">
            <span class="mr-2 text-lg">←</span>
            Retour à la liste
        </a>

        {{-- Messages de succès/erreur --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center justify-between shadow-sm">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="p-6 md:p-10 space-y-8">

                {{-- En-tête resort --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <p class="text-clubmed-gold font-semibold text-sm uppercase tracking-widest mb-2">Resort Club Med</p>
                        <h1 class="font-serif text-4xl md:text-5xl font-bold text-clubmed-blue">
                            {{ $resort->nomresort }}
                        </h1>
                        <p class="mt-4 text-gray-600 leading-relaxed text-lg">
                            {{ $resort->descriptionresort }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start md:items-end text-sm text-gray-600 gap-2 bg-clubmed-beige rounded-2xl p-5">
                        <div>Chambres : <span class="font-bold text-clubmed-blue">{{ $resort->nbchambrestotal }}</span></div>
                        <div>Note : <span class="font-bold text-clubmed-blue">{{ $resort->moyenneavis }}</span> <span class="text-clubmed-gold">★</span></div>
                        <div>Pays :
                            <span class="font-bold text-clubmed-blue">
                                {{ $resort->pays->nompays ?? 'pas de pays défini' }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Avis --}}
                <div class="space-y-6">
                    <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Avis des clients</h2>
                    @if($resort->avis->isEmpty())
                        <p class="text-gray-500 italic">Aucun avis pour le moment.</p>
                    @else
                        <div class="grid md:grid-cols-2 gap-5">
                            @foreach($resort->avis as $unAvis)
                                <div class="bg-clubmed-beige rounded-2xl p-6 space-y-4 flex flex-col">
                                    
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-clubmed-blue">{{ $unAvis->user->name ?? 'Client' }}</p>
                                            <p class="text-xs text-gray-500">
                                                Publié le : {{ \Carbon\Carbon::parse($unAvis->datepublication)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex text-clubmed-gold text-sm">
                                                @for($i=0; $i < $unAvis->noteavis; $i++) ★ @endfor
                                                @for($i=$unAvis->noteavis; $i < 5; $i++) <span class="text-gray-300">★</span> @endfor
                                            </div>
                                            <button onclick="openReportModal({{ $unAvis->numavis }})" 
                                                    class="text-xs text-red-500 hover:text-red-700 hover:underline flex items-center gap-1 transition-colors"
                                                    title="Signaler cet avis">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Signaler
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-gray-700 text-sm leading-relaxed">
                                        {{ $unAvis->commentaire }}
                                    </p>

                                    {{-- ZONE PHOTOS --}}
                                    @if($unAvis->photos->isNotEmpty())
                                        <div class="mt-2 pt-3 border-t border-gray-200">
                                            {{-- Bouton déclencheur --}}
                                            <button onclick="togglePhotos('photos-{{ $unAvis->numavis }}')" 
                                                    class="text-sm font-semibold text-clubmed-blue hover:text-clubmed-blue-dark hover:underline flex items-center gap-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Voir les {{ $unAvis->photos->count() }} photo(s)
                                            </button>

                                            {{-- Conteneur déroulant --}}
                                            <div id="photos-{{ $unAvis->numavis }}" class="hidden mt-3 grid grid-cols-4 gap-2 fade-in">
                                                @foreach($unAvis->photos as $photo)
                                                    <div class="relative aspect-square group cursor-zoom-in rounded-xl overflow-hidden shadow-sm"
                                                         onclick="openImageModal('{{ asset($photo->cheminfichierphoto) }}')">
                                                        <img src="{{ asset($photo->cheminfichierphoto) }}" 
                                                             alt="Photo avis" 
                                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <hr class="border-gray-100">

                {{-- Types de chambres --}}
                <div class="space-y-6">
                    <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Types de chambres disponibles</h2>
                    @if($resort->typechambres->isEmpty())
                        <p class="text-gray-500 italic">Aucun type de chambre défini pour ce resort.</p>
                    @else
                        <div class="grid md:grid-cols-2 gap-5">
                            @foreach($resort->typechambres as $typeChambre)
                                <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                    <h3 class="text-lg font-bold text-clubmed-blue">
                                        {{ $typeChambre->nomtype }}
                                    </h3>
                                    
                                    <div class="flex gap-6 text-sm text-gray-600">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold">Surface :</span>
                                            <span>{{ $typeChambre->surface }} m²</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold">Capacité :</span>
                                            <span>{{ $typeChambre->capacitemax }} pers.</span>
                                        </div>
                                    </div>

                                    @if($typeChambre->textepresentation)
                                        <p class="text-gray-600 leading-relaxed text-sm">
                                            {{ $typeChambre->textepresentation }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <hr class="border-gray-100">

                {{-- Domaine skiable --}}
                @if($resort->domaineskiable)
                <div class="space-y-6">
                    <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Domaine skiable</h2>
                    <div class="bg-gradient-to-br from-blue-50 to-clubmed-beige rounded-2xl p-8 space-y-6">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="space-y-2">
                                <h3 class="text-2xl font-bold text-clubmed-blue">
                                    {{ $resort->domaineskiable->nomdomaine }}
                                </h3>
                                @if($resort->domaineskiable->nomstation)
                                    <p class="text-base text-gray-600">
                                        Station : <span class="font-semibold">{{ $resort->domaineskiable->nomstation }}</span>
                                    </p>
                                @endif
                            </div>
                            
                            @if($resort->domaineskiable->skiaupied)
                                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                    <span>⛷️</span>
                                    <span>Ski au pied</span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t border-blue-100">
                            @if($resort->domaineskiable->altitudeclub)
                                <div class="bg-white rounded-xl p-5 text-center shadow-sm">
                                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Altitude club</p>
                                    <p class="text-3xl font-bold text-clubmed-blue">{{ number_format($resort->domaineskiable->altitudeclub, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-gray-600 mt-1">mètres</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->altitudestation)
                                <div class="bg-white rounded-xl p-5 text-center shadow-sm">
                                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Altitude station</p>
                                    <p class="text-3xl font-bold text-clubmed-blue">{{ number_format($resort->domaineskiable->altitudestation, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-gray-600 mt-1">mètres</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->longueurpiste)
                                <div class="bg-white rounded-xl p-5 text-center shadow-sm">
                                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Pistes</p>
                                    <p class="text-3xl font-bold text-clubmed-blue">{{ number_format($resort->domaineskiable->longueurpiste, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-gray-600 mt-1">km</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->nbpiste)
                                <div class="bg-white rounded-xl p-5 text-center shadow-sm">
                                    <p class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wide">Nombre</p>
                                    <p class="text-3xl font-bold text-clubmed-blue">{{ $resort->domaineskiable->nbpiste }}</p>
                                    <p class="text-xs text-gray-600 mt-1">pistes</p>
                                </div>
                            @endif
                        </div>

                        @if($resort->domaineskiable->descriptiondomaine)
                            <div class="pt-6 border-t border-blue-100">
                                <p class="text-gray-700 leading-relaxed">
                                    {{ $resort->domaineskiable->descriptiondomaine }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                <hr class="border-gray-100">
                @endif

                {{-- Restaurants et Bars --}}
                @if($resort->restaurants->isNotEmpty())
                <div class="space-y-6">
                    <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Restaurants et Bars</h2>
                    <div class="grid md:grid-cols-2 gap-5">
                        @foreach($resort->restaurants as $restaurant)
                            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-4 hover:shadow-lg transition-shadow">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg font-bold text-clubmed-blue flex-1">
                                        {{ $restaurant->nomrestaurant }}
                                    </h3>
                                    
                                    @if($restaurant->typerestaurant)
                                        @php
                                            $typeConfig = match($restaurant->typerestaurant) {
                                                'Gourmet' => ['icon' => '🍽️', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                                'Buffet' => ['icon' => '🍴', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
                                                'Snack' => ['icon' => '🥪', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                                'Bar' => ['icon' => '🍸', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                                default => ['icon' => '🍽️', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700']
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-4 py-2 {{ $typeConfig['bg'] }} {{ $typeConfig['text'] }} rounded-full text-xs font-bold whitespace-nowrap">
                                            <span>{{ $typeConfig['icon'] }}</span>
                                            <span>{{ $restaurant->typerestaurant }}</span>
                                        </span>
                                    @endif
                                </div>

                                @if($restaurant->descriptionrestaurant)
                                    <p class="text-gray-600 leading-relaxed text-sm">
                                        {{ $restaurant->descriptionrestaurant }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="border-gray-100">
                @endif

                {{-- Carte --}}
                <div class="space-y-4">
                    <h2 class="font-serif text-2xl font-bold text-clubmed-blue">Localisation du resort</h2>
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <iframe width="100%" height="400" class="w-full" style="border:0;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q={{ urlencode($resort->nomresort . ' ' . ($resort->pays->nompays ?? '')) }}&output=embed"></iframe>
                    </div>
                </div>

                {{-- Image principale --}}
                @php
                    $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                    $imagePath = 'img/ressort/' . $imageName;
                    $fullPath = public_path($imagePath);
                @endphp
                <div class="mt-8">
                    <div class="rounded-3xl overflow-hidden shadow-2xl">
                        @if(file_exists($fullPath))
                            <img src="{{ asset($imagePath) }}" alt="{{ $resort->nomresort }}" class="w-full h-[420px] md:h-[520px] object-cover object-center transition-transform duration-500 hover:scale-[1.02]">
                        @else
                            <div class="h-[340px] md:h-[420px] bg-gradient-to-br from-clubmed-beige to-gray-200 flex items-center justify-center text-gray-400">
                                <span class="text-center">Aucune image disponible<br><small class="text-xs">Fichier attendu : {{ $imageName }}</small></span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Boutons actions --}}
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('resort.types', ['id' => $resort->numresort]) }}" class="inline-flex items-center px-6 py-3 rounded-full bg-clubmed-blue hover:bg-clubmed-blue-dark text-white font-bold text-sm shadow-md transition-all">Voir les types d'activités disponibles</a>
                    
                    <a href="{{ route('reservation.step1', ['numresort' => $resort->numresort]) }}" class="inline-flex items-center px-8 py-3 rounded-full bg-clubmed-gold hover:bg-yellow-400 text-clubmed-blue font-bold text-sm shadow-lg transition-all hover:shadow-xl">Réserver ce resort</a>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL IMAGE (LIGHTBOX) --}}
    <div id="image-modal" class="fixed inset-0 z-[60] bg-black/90 hidden flex items-center justify-center backdrop-blur-sm p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-[70]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="modal-image-content" src="" alt="Agrandissement" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>

    {{-- MODAL SIGNALEMENT --}}
    <div id="report-modal-overlay" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden backdrop-blur-sm">
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <button id="close-report-modal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-100 transition-colors">✕</button>
            <div class="px-8 pt-10 pb-8">
                <h2 class="text-center font-serif text-2xl font-bold text-clubmed-blue mb-4">Signaler cet avis</h2>
                <p class="text-sm text-gray-600 mb-6 text-center">Aidez-nous à maintenir la qualité des avis en signalant tout contenu inapproprié.</p>
                
                <form id="report-form" method="POST" action="">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message du signalement <span class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="4" maxlength="1000" required
                                      class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-clubmed-gold focus:border-clubmed-gold transition-all"
                                      placeholder="Décrivez le problème rencontré avec cet avis..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Maximum 1000 caractères</p>
                        </div>
                        
                        <button type="submit" class="w-full px-6 py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-full shadow-md transition-all">
                            Envoyer le signalement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.footer')

    {{-- SCRIPTS --}}
    <script>
        function togglePhotos(id) {
            const container = document.getElementById(id);
            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function openImageModal(src) {
            const modal = document.getElementById('image-modal');
            const img = document.getElementById('modal-image-content');
            img.src = src;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('image-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('image-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        function openReportModal(numavis) {
            const modal = document.getElementById('report-modal-overlay');
            const form = document.getElementById('report-form');
            form.action = '{{ url("/avis") }}/' + numavis + '/signaler';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            const modal = document.getElementById('report-modal-overlay');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Réinitialiser le formulaire
            document.getElementById('report-form').reset();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Gestion du modal de signalement
            const reportCloseBtn = document.getElementById('close-report-modal');
            const reportOverlay = document.getElementById('report-modal-overlay');
            
            if (reportCloseBtn && reportOverlay) {
                reportCloseBtn.addEventListener('click', closeReportModal);
                reportOverlay.addEventListener('click', (e) => { 
                    if (e.target === reportOverlay) closeReportModal(); 
                });
            }
        });
    </script>

</body>
</html>