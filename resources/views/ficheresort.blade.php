<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort - {{ $resort->nomresort }}</title>

    {{-- Tailwind via CDN (pour simplifier sur ce projet) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Activités </title>
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

                {{-- Bouton activités --}}
                <div class="mt-6">
                    <a href="{{ route('resort.activites', ['id' => $resort->numresort]) }}"
                       class="inline-flex items-center px-5 py-2.5 rounded-full bg-sky-700 hover:bg-sky-800 text-white font-semibold text-sm shadow-md transition">
                        Voir les activités
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
