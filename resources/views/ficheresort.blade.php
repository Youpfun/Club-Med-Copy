<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort - {{ $resort->nomresort }}</title>

    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-sans">
    @include('layouts.header')

    <div class="max-w-6xl mx-auto px-4 py-8">
        <a href="/resorts" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-900 mb-6">
            <span class="mr-2 text-lg">←</span>
            Retour à la liste
        </a>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-6 md:p-8 space-y-6">

                {{-- En-tête resort --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
                            {{ $resort->nomresort }}
                        </h1>
                        <p class="mt-3 text-slate-600 leading-relaxed">
                            {{ $resort->descriptionresort }}
                        </p>
                    </div>
                    <div class="flex flex-col items-start md:items-end text-sm text-slate-700 gap-1">
                        <div>Nombre de chambres : <span class="font-semibold">{{ $resort->nbchambrestotal }}</span></div>
                        <div>Moyenne des avis : <span class="font-semibold">{{ $resort->moyenneavis }}</span></div>
                        <div>Pays :
                            <span class="font-semibold">
                                {{ $resort->pays->nompays ?? 'pas de pays défini' }}
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200">

                {{-- Avis --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">Avis des clients</h2>
                    @if($resort->avis->isEmpty())
                        <p class="text-slate-500 italic">Aucun avis pour le moment.</p>
                    @else
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($resort->avis as $unAvis)
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3 flex flex-col">
                                    
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-bold text-[#113559]">{{ $unAvis->user->name ?? 'Client' }}</p>
                                            <p class="text-xs text-slate-500">
                                                Publié le : {{ \Carbon\Carbon::parse($unAvis->datepublication)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="flex text-[#ffc000] text-sm">
                                            @for($i=0; $i < $unAvis->noteavis; $i++) ★ @endfor
                                            @for($i=$unAvis->noteavis; $i < 5; $i++) <span class="text-slate-300">★</span> @endfor
                                        </div>
                                    </div>

                                    <p class="text-slate-700 text-sm leading-relaxed">
                                        {{ $unAvis->commentaire }}
                                    </p>

                                    {{-- ZONE PHOTOS (Modification Ici) --}}
                                    @if($unAvis->photos->isNotEmpty())
                                        <div class="mt-2 pt-2 border-t border-slate-200">
                                            {{-- Bouton déclencheur --}}
                                            <button onclick="togglePhotos('photos-{{ $unAvis->numavis }}')" 
                                                    class="text-sm font-semibold text-sky-700 hover:text-sky-900 hover:underline flex items-center gap-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Voir les {{ $unAvis->photos->count() }} photo(s)
                                            </button>

                                            {{-- Conteneur déroulant (Caché par défaut) --}}
                                            <div id="photos-{{ $unAvis->numavis }}" class="hidden mt-3 grid grid-cols-4 gap-2 fade-in">
                                                @foreach($unAvis->photos as $photo)
                                                    <div class="relative aspect-square group cursor-zoom-in rounded-lg overflow-hidden border border-slate-200 shadow-sm"
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

                <hr class="border-slate-200">

                {{-- Types de chambres --}}
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">Types de chambres disponibles</h2>
                    @if($resort->typechambres->isEmpty())
                        <p class="text-slate-500 italic">Aucun type de chambre défini pour ce resort.</p>
                    @else
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($resort->typechambres as $typeChambre)
                                <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-3 hover:shadow-md transition-shadow">
                                    <h3 class="text-lg font-bold text-sky-700">
                                        {{ $typeChambre->nomtype }}
                                    </h3>
                                    
                                    <div class="flex gap-6 text-sm text-slate-600">
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
                                        <p class="text-slate-700 leading-relaxed text-sm">
                                            {{ $typeChambre->textepresentation }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <hr class="border-slate-200">

                {{-- Domaine skiable --}}
                @if($resort->domaineskiable)
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-slate-900">Domaine skiable</h2>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 space-y-4">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="space-y-1">
                                <h3 class="text-2xl font-bold text-[#113559]">
                                    {{ $resort->domaineskiable->nomdomaine }}
                                </h3>
                                @if($resort->domaineskiable->nomstation)
                                    <p class="text-base text-slate-600">
                                        Station : <span class="font-semibold">{{ $resort->domaineskiable->nomstation }}</span>
                                    </p>
                                @endif
                            </div>
                            
                            @if($resort->domaineskiable->skiaupied)
                                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                                    <span>⛷️</span>
                                    <span>Ski au pied</span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-blue-200">
                            @if($resort->domaineskiable->altitudeclub)
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <p class="text-xs text-slate-500 mb-1">Altitude du club</p>
                                    <p class="text-2xl font-bold text-[#113559]">{{ number_format($resort->domaineskiable->altitudeclub, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-600">mètres</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->altitudestation)
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <p class="text-xs text-slate-500 mb-1">Altitude station</p>
                                    <p class="text-2xl font-bold text-[#113559]">{{ number_format($resort->domaineskiable->altitudestation, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-600">mètres</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->longueurpiste)
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <p class="text-xs text-slate-500 mb-1">Longueur des pistes</p>
                                    <p class="text-2xl font-bold text-[#113559]">{{ number_format($resort->domaineskiable->longueurpiste, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-600">km</p>
                                </div>
                            @endif
                            @if($resort->domaineskiable->nbpiste)
                                <div class="bg-white rounded-lg p-4 text-center shadow-sm">
                                    <p class="text-xs text-slate-500 mb-1">Nombre de pistes</p>
                                    <p class="text-2xl font-bold text-[#113559]">{{ $resort->domaineskiable->nbpiste }}</p>
                                    <p class="text-xs text-slate-600">pistes</p>
                                </div>
                            @endif
                        </div>

                        @if($resort->domaineskiable->descriptiondomaine)
                            <div class="pt-4 border-t border-blue-200">
                                <p class="text-slate-700 leading-relaxed">
                                    {{ $resort->domaineskiable->descriptiondomaine }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                <hr class="border-slate-200">
                @endif

                {{-- Carte --}}
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-900">Localisation du resort</h2>
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200">
                        <iframe width="100%" height="400" class="w-full" style="border:0;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q={{ urlencode($resort->nomresort . ' ' . ($resort->pays->nompays ?? '')) }}&output=embed"></iframe>
                    </div>
                </div>

                {{-- Image principale --}}
                @php
                    $imageName = strtolower(str_replace(' ', '', $resort->nomresort)) . '.webp';
                    $imagePath = 'img/ressort/' . $imageName;
                    $fullPath = public_path($imagePath);
                @endphp
                <div class="mt-6">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
                        @if(file_exists($fullPath))
                            <img src="{{ asset($imagePath) }}" alt="{{ $resort->nomresort }}" class="w-full h-[420px] md:h-[520px] object-cover object-center transition-transform duration-500 hover:scale-[1.02]">
                        @else
                            <div class="h-[340px] md:h-[420px] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400">
                                <span class="text-center">Aucune image disponible<br><small class="text-xs">Fichier attendu : {{ $imageName }}</small></span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Boutons actions --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('resort.types', ['id' => $resort->numresort]) }}" class="inline-flex items-center px-5 py-2.5 rounded-full bg-sky-700 hover:bg-sky-800 text-white font-semibold text-sm shadow-md transition">Voir les types d'activités disponibles</a>
                    
                    @guest
                        <button type="button" id="open-login-modal" class="inline-flex items-center px-6 py-2.5 rounded-full bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm shadow-md transition">Réserver ce resort</button>
                    @endguest

                    @auth
                        <form action="{{ route('cart.addResort', ['numresort' => $resort->numresort]) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 rounded-full bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm shadow-md transition">Réserver ce resort</button>
                        </form>
                    @endauth
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

    {{-- MODAL CONNEXION --}}
    @guest
        <div id="login-modal-overlay" class="fixed inset-0 bg-black/40 flex items-center justify-center z-40 hidden">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
                <button id="close-login-modal" class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-100">✕</button>
                <div class="px-8 pt-10 pb-8">
                    <h2 class="text-center text-xl font-semibold text-[#b46320] mb-4">Déjà client ?</h2>
                    <a href="{{ route('login', ['reserve_resort' => $resort->numresort]) }}" class="block w-full text-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm rounded-full shadow-md transition">SE CONNECTER</a>
                </div>
            </div>
        </div>
    @endguest

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

        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('open-login-modal');
            const closeBtn = document.getElementById('close-login-modal');
            const overlay = document.getElementById('login-modal-overlay');

            if (openBtn && closeBtn && overlay) {
                openBtn.addEventListener('click', () => overlay.classList.remove('hidden'));
                closeBtn.addEventListener('click', () => overlay.classList.add('hidden'));
                overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.add('hidden'); });
            }
        });
    </script>

</body>
</html>