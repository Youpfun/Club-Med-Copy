<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donner mon avis | Club Med</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 5px; }
        .rating input { display: none; }
        .rating label { cursor: pointer; font-size: 2rem; color: #ccc; transition: color 0.2s; }
        .rating input:checked ~ label, .rating label:hover, .rating label:hover ~ label { color: rgb(var(--color-clubmed-gold)); }
    </style>
</head>
<body class="bg-gray-50 font-sans text-clubmed-blue">
    @include('layouts.header')

    <main class="max-w-2xl mx-auto px-4 py-12">
        <a href="{{ route('reservations.index') }}" class="text-sm text-gray-500 hover:text-clubmed-blue mb-6 inline-block">← Retour</a>

        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8">
            <h1 class="text-3xl font-serif font-bold mb-2 text-center">Votre séjour à {{ $reservation->nomresort }}</h1>
            <p class="text-center text-gray-500 mb-8">Partagez votre expérience.</p>

            <form action="{{ route('avis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="numresort" value="{{ $reservation->numresort }}">

                {{-- Note --}}
                <div class="mb-8 text-center">
                    <label class="block text-sm font-bold uppercase tracking-wide mb-2">Votre note</label>
                    <div class="rating">
                        <input type="radio" id="star5" name="note" value="5" required /><label for="star5">★</label>
                        <input type="radio" id="star4" name="note" value="4" /><label for="star4">★</label>
                        <input type="radio" id="star3" name="note" value="3" /><label for="star3">★</label>
                        <input type="radio" id="star2" name="note" value="2" /><label for="star2">★</label>
                        <input type="radio" id="star1" name="note" value="1" /><label for="star1">★</label>
                    </div>
                </div>

                {{-- Commentaire --}}
                <div class="mb-6">
                    <label for="commentaire" class="block text-sm font-bold uppercase tracking-wide mb-2">Votre commentaire</label>
                    <textarea name="commentaire" id="commentaire" rows="4" class="w-full p-4 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-clubmed-blue" placeholder="Racontez-nous..."></textarea>
                </div>

                {{-- Photo Upload (MODIFIÉ) --}}
                <div class="mb-8">
                    <label class="block text-sm font-bold uppercase tracking-wide mb-2">Une photo souvenir ?</label>
                    <div class="flex items-center justify-center w-full">
                        <label for="photo" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors relative overflow-hidden">
                            
                            {{-- Contenu par défaut (Texte + Icone) --}}
                            <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm text-gray-500"><span class="font-semibold">Cliquez pour ajouter</span> une photo</p>
                                <p class="text-xs text-gray-500">PNG, JPG (Max 5Mo)</p>
                            </div>

                            {{-- Aperçu de l'image (Caché par défaut) --}}
                            <img id="image-preview" class="hidden absolute inset-0 w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity" />
                            
                            {{-- Input fichier --}}
                            <input id="photo" type="file" name="photo" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-clubmed-blue hover:bg-blue-900 text-white rounded-xl font-bold uppercase tracking-wide shadow-md transition-all">
                    Publier l'avis
                </button>
            </form>
        </div>
    </main>
    @include('layouts.footer')

    {{-- SCRIPT POUR LA PREVISUALISATION --}}
    <script>
        const photoInput = document.getElementById('photo');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const imagePreview = document.getElementById('image-preview');

        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    uploadPlaceholder.classList.add('hidden');
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                
                reader.readAsDataURL(file);
            } else {
                uploadPlaceholder.classList.remove('hidden');
                imagePreview.classList.add('hidden');
                imagePreview.src = '';
            }
        });
    </script>
</body>
</html>