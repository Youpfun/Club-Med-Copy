<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Utilisateur | Club Med</title>
    <link rel="icon" type="image/png" href="/img/logo-clubmed.png"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="min-h-screen">
        {{-- Hero Section --}}
        <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Guide Utilisateur</h1>
                <p class="text-xl text-blue-100">Tout ce que vous devez savoir pour profiter pleinement de votre expérience Club Med</p>
            </div>
        </section>

        {{-- Recherche --}}
        <section class="max-w-4xl mx-auto px-4 -mt-8">
            <div class="bg-white rounded-lg shadow-lg p-4">
                <input type="text" id="searchGuide" placeholder="Rechercher dans le guide..." 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </section>

        {{-- Contenu du Guide --}}
        <section class="max-w-4xl mx-auto px-4 py-12 space-y-8">

            {{-- Section 1: Recherche de Resorts --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="recherche resort filtrer localisation pays type club">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">🔍</span>
                    Comment rechercher un Resort ?
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Pour trouver le resort parfait, utilisez les filtres disponibles sur la page de recherche :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Type de Club :</strong> Choisissez entre montagne, mer, etc.</li>
                        <li><strong>Localisation :</strong> Sélectionnez une région (Alpes, Méditerranée...)</li>
                        <li><strong>Pays :</strong> Filtrez par pays de destination</li>
                    </ul>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                        <p class="font-semibold text-blue-900">💡 Astuce :</p>
                        <p class="text-blue-800">Vous pouvez combiner plusieurs filtres pour affiner votre recherche.</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Types de Chambres --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="chambre type surface capacité réservation logement">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">🏨</span>
                    Comprendre les Types de Chambres
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Chaque resort propose différents types de chambres adaptés à vos besoins :</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">🛏️ Surface</h3>
                            <p class="text-sm">Indique la superficie de la chambre en m²</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">👥 Capacité</h3>
                            <p class="text-sm">Nombre maximum de personnes pouvant séjourner</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Domaine Skiable --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="ski domaine skiable piste altitude neige montagne">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">⛷️</span>
                    Informations Domaine Skiable
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Pour les resorts en montagne, consultez les informations du domaine skiable :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Altitude Club :</strong> Hauteur à laquelle se trouve le resort</li>
                        <li><strong>Altitude Station :</strong> Point culminant du domaine</li>
                        <li><strong>Longueur des pistes :</strong> Total en kilomètres</li>
                        <li><strong>Nombre de pistes :</strong> Diversité du domaine</li>
                        <li><strong>Ski au pied :</strong> Accès direct aux pistes depuis le resort</li>
                    </ul>
                </div>
            </div>

            {{-- Section 4: Restaurants et Bars --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="restaurant bar nourriture repas buffet gourmet snack">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">🍽️</span>
                    Restaurants et Bars
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Chaque resort dispose de plusieurs options de restauration :</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">🍽️</span>
                                <h3 class="font-bold">Gourmet</h3>
                            </div>
                            <p class="text-sm">Cuisine raffinée et service à table</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">🍴</span>
                                <h3 class="font-bold">Buffet</h3>
                            </div>
                            <p class="text-sm">Formule buffet variée à volonté</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">🥪</span>
                                <h3 class="font-bold">Snack</h3>
                            </div>
                            <p class="text-sm">Restauration rapide et décontractée</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-2xl">🍸</span>
                                <h3 class="font-bold">Bar</h3>
                            </div>
                            <p class="text-sm">Boissons et ambiance conviviale</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 5: Avis et Notes --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="avis note commentaire évaluation retour expérience">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">⭐</span>
                    Lire et Comprendre les Avis
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Les avis clients vous aident à choisir votre resort :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Note :</strong> Évaluation sur 5 étoiles</li>
                        <li><strong>Commentaire :</strong> Retour d'expérience détaillé</li>
                        <li><strong>Date :</strong> Date de publication de l'avis</li>
                    </ul>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-4">
                        <p class="font-semibold text-green-900">✓ Conseil :</p>
                        <p class="text-green-800">Consultez plusieurs avis récents pour avoir une vision d'ensemble.</p>
                    </div>
                </div>
            </div>

            {{-- Section 6: Réservation --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="réserver réservation paiement panier commander">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">📅</span>
                    Effectuer une Réservation
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Pour réserver un resort, suivez ces étapes :</p>
                    <ol class="list-decimal list-inside space-y-3 ml-4">
                        <li>Trouvez le resort qui vous convient</li>
                        <li>Consultez les détails (chambres, domaine skiable, restaurants)</li>
                        <li>Cliquez sur "Réserver ce resort"</li>
                        <li>Connectez-vous ou créez un compte</li>
                        <li>Remplissez les informations de réservation</li>
                        <li>Validez et payez</li>
                    </ol>
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mt-4">
                        <p class="font-semibold text-amber-900">⚠️ Important :</p>
                        <p class="text-amber-800">Vérifiez bien les dates et le nombre de personnes avant de valider.</p>
                    </div>
                </div>
            </div>

            {{-- Section 7: Compte Utilisateur --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="compte profil connexion inscription mot de passe">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">👤</span>
                    Gérer votre Compte
                </h2>
                <div class="space-y-4 text-gray-700">
                    <p>Créez un compte pour bénéficier d'avantages :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Sauvegarde de vos informations personnelles</li>
                        <li>Historique de vos réservations</li>
                        <li>Réservation rapide avec vos compagnons enregistrés</li>
                        <li>Accès à vos avis et commentaires</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-[#113559] mb-6 flex items-center gap-2">
                    <span class="text-3xl">❓</span>
                    Questions Fréquentes (FAQ)
                </h2>
                <div class="space-y-4">
                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Comment modifier une réservation ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            Connectez-vous à votre compte, accédez à "Mes réservations" et cliquez sur "Modifier" à côté de la réservation concernée.
                        </div>
                    </details>

                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Puis-je annuler ma réservation ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            Oui, les conditions d'annulation dépendent du tarif choisi. Consultez les conditions dans votre confirmation de réservation.
                        </div>
                    </details>

                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Les repas sont-ils inclus ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            La formule Club Med inclut généralement les repas. Vérifiez les détails de votre forfait lors de la réservation.
                        </div>
                    </details>

                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Comment contacter le service client ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            Vous pouvez nous contacter via le formulaire de contact ou par téléphone au numéro indiqué dans le pied de page.
                        </div>
                    </details>
                </div>
            </div>

            {{-- Besoin d'aide supplémentaire --}}
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl shadow-lg p-8 text-center">
                <h2 class="text-2xl font-bold mb-4">Vous ne trouvez pas votre réponse ?</h2>
                <p class="mb-6 text-blue-100">Notre équipe est là pour vous aider</p>
                <a href="/contact" class="inline-block px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-blue-50 transition-colors shadow-lg">
                    Contactez-nous
                </a>
            </div>

        </section>
    </main>

    @include('layouts.footer')

    {{-- Script de recherche --}}
    <script>
        document.getElementById('searchGuide').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const sections = document.querySelectorAll('.guide-section');
            
            sections.forEach(section => {
                const keywords = section.dataset.keywords.toLowerCase();
                const text = section.textContent.toLowerCase();
                
                if (keywords.includes(searchTerm) || text.includes(searchTerm) || searchTerm === '') {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
