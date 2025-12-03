<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort - {{ $resort->nomresort }}</title>

    {{-- Tailwind via CDN (pour simplifier sur ce projet) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">

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
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-2">
                                    <p class="font-semibold text-amber-600">
                                        Note : {{ $unAvis->noteavis }}/5
                                    </p>
                                    <p class="text-slate-700">
                                        {{ $unAvis->commentaire }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Publié le : {{ $unAvis->datepublication }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <hr class="border-slate-200">

                {{-- Carte --}}
                <div class="space-y-3">
                    <h2 class="text-xl font-semibold text-slate-900">Localisation du resort</h2>
                    <p class="text-slate-600">
                        La carte ci-dessous indique l’emplacement approximatif de
                        <span class="font-semibold">{{ $resort->nomresort }}</span>
                        @if($resort->pays && $resort->pays->nompays)
                            , {{ $resort->pays->nompays }}
                        @endif
                        .
                    </p>
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200">
                        <iframe
                            width="100%"
                            height="400"
                            class="w-full"
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

                {{-- Image principale --}}
                <div class="mt-6">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
                        @if(file_exists($fullPath))
                            <img src="{{ asset($imagePath) }}"
                                 alt="{{ $resort->nomresort }}"
                                 class="w-full h-[420px] md:h-[520px] object-cover object-center transition-transform duration-500 hover:scale-[1.02]">
                        @else
                            <div class="h-[340px] md:h-[420px] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400">
                                <span class="text-center">
                                    Aucune image disponible<br>
                                    <small class="text-xs">Fichier attendu : {{ $imageName }}</small>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Boutons actions --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('resort.activites', ['id' => $resort->numresort]) }}"
                       class="inline-flex items-center px-5 py-2.5 rounded-full bg-sky-700 hover:bg-sky-800 text-white font-semibold text-sm shadow-md transition">
                        Voir les types d'activités disponibles
                    </a>

                    {{-- Bouton réserver : si non connecté, ouvre le pop-up de connexion --}}
                    @guest
                        <button type="button"
                                id="open-login-modal"
                                class="inline-flex items-center px-6 py-2.5 rounded-full bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm shadow-md transition">
                            Réserver ce resort
                        </button>
                    @endguest

                    @auth
                        {{-- Si connecté : envoie une requête POST pour ajouter au panier --}}
                        <form action="{{ route('cart.addResort', ['numresort' => $resort->numresort]) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-2.5 rounded-full bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm shadow-md transition">
                                Réserver ce resort
                            </button>
                        </form>
                    @endauth
                </div>

            </div>
        </div>
    </div>

    {{-- Pop-up de connexion si l'utilisateur n'est pas connecté --}}
    @guest
        <div id="login-modal-overlay"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-40 hidden">
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
                {{-- Bouton fermer --}}
                <button id="close-login-modal"
                        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 hover:bg-slate-100">
                    ✕
                </button>

                <div class="px-8 pt-10 pb-8">
                    <div class="flex justify-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 text-3xl">
                            👤
                        </div>
                    </div>

                    <h2 class="text-center text-xl font-semibold text-[#b46320] mb-4">
                        Déjà client ? Gagnez du temps !
                    </h2>

                    <ul class="space-y-2 text-sm text-slate-700 mb-5">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-green-500">✔</span>
                            <span>Vos informations seront complétées automatiquement</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-green-500">✔</span>
                            <span>Sélectionnez vos accompagnants parmi vos compagnons enregistrés</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-green-500">✔</span>
                            <span>Passez à l'étape suivante en quelques clics</span>
                        </li>
                    </ul>

                    <div class="mb-6">
                        <p class="text-xs text-slate-500 text-center">
                            Connectez-vous et utilisez vos informations enregistrées pour finaliser votre réservation plus rapidement.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('login', ['reserve_resort' => $resort->numresort]) }}"
                           class="block w-full text-center px-6 py-3 bg-[#ffc000] hover:bg-[#e0a800] text-[#113559] font-bold text-sm rounded-full shadow-md transition">
                            SE CONNECTER
                        </a>
                        <p class="text-xs text-slate-500 text-center">
                            Vous n'avez pas encore de compte ? Vous recevrez un e-mail pour créer votre compte une fois votre réservation finalisée.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openBtn = document.getElementById('open-login-modal');
                const closeBtn = document.getElementById('close-login-modal');
                const overlay = document.getElementById('login-modal-overlay');

                if (openBtn && closeBtn && overlay) {
                    openBtn.addEventListener('click', () => {
                        overlay.classList.remove('hidden');
                    });

                    closeBtn.addEventListener('click', () => {
                        overlay.classList.add('hidden');
                    });

                    overlay.addEventListener('click', (e) => {
                        if (e.target === overlay) {
                            overlay.classList.add('hidden');
                        }
                    });
                }
            });
        </script>
    @endguest

</body>
</html>
