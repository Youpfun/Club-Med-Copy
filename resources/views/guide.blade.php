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
        <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-20 px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Guide Utilisateur</h1>
                <p class="text-xl text-blue-100 mb-6">Tout ce que vous devez savoir pour profiter pleinement de votre expérience Club Med</p>
                <p class="text-base text-blue-200 max-w-2xl mx-auto">
                    Bienvenue sur votre guide utilisateur Club Med ! Découvrez comment naviguer sur notre plateforme, rechercher le resort idéal, gérer vos réservations et profiter au maximum de votre séjour. Ce guide interactif vous accompagne pas à pas dans toutes vos démarches.
                </p>
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
                    {{-- Image placeholder --}}
                    <div class="mb-4 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/recherche-resort.png" alt="Capture d'écran - Recherche de resort" class="w-full h-auto">
                    </div>
                    <p>Pour trouver le resort parfait, utilisez les filtres disponibles sur la page de recherche. Notre système de filtrage avancé vous permet de personnaliser votre recherche selon vos préférences :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Type de Club :</strong> Choisissez entre montagne, mer, campagne ou ville selon vos envies</li>
                        <li><strong>Localisation :</strong> Sélectionnez une région spécifique (Alpes, Méditerranée, Antilles...)</li>
                        <li><strong>Pays :</strong> Filtrez par pays de destination pour cibler votre recherche</li>
                        <li><strong>Activités :</strong> Recherchez par type d'activités proposées (ski, sports nautiques, spa...)</li>
                    </ul>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                        <p class="font-semibold text-blue-900">💡 Astuce :</p>
                        <p class="text-blue-800">Vous pouvez combiner plusieurs filtres simultanément pour affiner votre recherche et trouver exactement le resort qui correspond à vos attentes.</p>
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
                    {{-- Image placeholder --}}
                    <div class="mb-4 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/types-chambres.png" alt="Capture d'écran - Types de chambres" class="w-full h-auto">
                    </div>
                    <p>Chaque resort propose différents types de chambres adaptés à vos besoins et à la composition de votre groupe. Consultez les caractéristiques détaillées de chaque type de chambre pour faire le meilleur choix :</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">🛏️ Surface</h3>
                            <p class="text-sm">Indique la superficie de la chambre en m². Choisissez une chambre spacieuse pour plus de confort.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">👥 Capacité</h3>
                            <p class="text-sm">Nombre maximum de personnes pouvant séjourner confortablement dans la chambre.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">🏆 Équipements</h3>
                            <p class="text-sm">Vérifiez les équipements inclus : balcon, vue mer, climatisation, etc.</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-bold text-lg mb-2">💰 Tarifs</h3>
                            <p class="text-sm">Les prix varient selon le type de chambre et la saison de réservation.</p>
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
                    {{-- Image placeholder --}}
                    <div class="mb-4 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/domaine-skiable.png" alt="Capture d'écran - Domaine skiable" class="w-full h-auto">
                    </div>
                    <p>Pour les resorts en montagne, consultez les informations détaillées du domaine skiable. Ces données vous permettent d'évaluer si le resort correspond à votre niveau et vos attentes en matière de ski :</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li><strong>Altitude Club :</strong> Hauteur à laquelle se trouve le resort (garantit l'enneigement)</li>
                        <li><strong>Altitude Station :</strong> Point culminant du domaine skiable</li>
                        <li><strong>Longueur des pistes :</strong> Total en kilomètres de pistes disponibles</li>
                        <li><strong>Nombre de pistes :</strong> Diversité du domaine avec répartition par niveau (vertes, bleues, rouges, noires)</li>
                        <li><strong>Ski au pied :</strong> Accès direct aux pistes depuis le resort, sans navette</li>
                        <li><strong>Remontées mécaniques :</strong> Nombre et types de remontées disponibles</li>
                    </ul>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                        <p class="font-semibold text-blue-900">💡 Bon à savoir :</p>
                        <p class="text-blue-800">Les forfaits de ski peuvent être inclus dans certaines formules Club Med. Renseignez-vous lors de la réservation.</p>
                    </div>
                </div>
            </div>

            {{-- Section 4: Restaurants et Bars --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="restaurant bar nourriture repas buffet gourmet snack">
                <h2 class="text-2xl font-bold text-[#113559] mb-4 flex items-center gap-2">
                    <span class="text-3xl">🍽️</span>
                    Restaurants et Bars
                </h2>
                <div class="space-y-4 text-gray-700">
                    {{-- Image placeholder --}}
                    <div class="mb-4 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/restaurants-bars.png" alt="Capture d'écran - Restaurants et bars" class="w-full h-auto">
                    </div>
                    <p>Chaque resort dispose de plusieurs options de restauration pour satisfaire tous les goûts. La formule Club Med inclut généralement les repas et boissons :</p>
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
                    {{-- Image placeholder --}}
                    <div class="mb-4 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/avis-notes.png" alt="Capture d'écran - Avis et notes" class="w-full h-auto">
                    </div>
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
                <h2 class="text-2xl font-bold text-[#113559] mb-6 flex items-center gap-2">
                    <span class="text-3xl">📅</span>
                    Gérer vos Réservations
                </h2>
                <div class="space-y-8 text-gray-700">
                    
                    {{-- Image du bouton d'accès au menu réservations --}}
                    <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                        <img src="/img/guide/reservation.png" alt="Bouton d'accès au menu réservations" class="w-full h-auto">
                    </div>
                    
                    {{-- Sous-section: Prochain départ --}}
                    <div>
                        <h3 class="text-xl font-bold text-[#113559] mb-3">🛫 Prochain départ</h3>
                        <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                            <img src="/img/guide/reservation-prochain-depart.png" alt="Prochains départs" class="w-full h-auto">
                        </div>
                    </div>

                    {{-- Sous-section: Séjour actuel --}}
                    <div>
                        <h3 class="text-xl font-bold text-[#113559] mb-3">🏖️ Séjour actuel</h3>
                        <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                            <img src="/img/guide/reservation-sejour-actuel.png" alt="Séjour en cours" class="w-full h-auto">
                        </div>
                    </div>

                    {{-- Sous-section: Anciens voyages --}}
                    <div>
                        <h3 class="text-xl font-bold text-[#113559] mb-3">🗓️ Anciens voyages</h3>
                        <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                            <img src="/img/guide/reservation-anciens-voyages.png" alt="Historique des voyages" class="w-full h-auto">
                        </div>
                    </div>

                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mt-4">
                        <p class="font-semibold text-amber-900">⚠️ Important :</p>
                        <p class="text-amber-800">Vous pouvez modifier ou annuler vos réservations depuis ce menu selon les conditions applicables.</p>
                    </div>
                </div>
            </div>

            {{-- Section 7: Compte Utilisateur --}}
            <div class="bg-white rounded-xl shadow-md p-6 guide-section" data-keywords="compte profil connexion inscription mot de passe">
                <h2 class="text-2xl font-bold text-[#113559] mb-6 flex items-center gap-2">
                    <span class="text-3xl">👤</span>
                    Gérer votre Compte
                </h2>
                <div class="space-y-8 text-gray-700">
                    
                    {{-- Image du menu de connexion/inscription --}}
                    <div>
                        <h3 class="text-xl font-bold text-[#113559] mb-3">🤵 Connexion / Inscription</h3>
                        <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                            <img src="/img/guide/compte-utilisateur.png" alt="Bouton d'accès au menu de connexion/inscription" class="w-full h-auto">
                        </div>
                    </div>
                    
                    {{-- Sous-section: Informations personnelles --}}
                    <div>
                        <h3 class="text-xl font-bold text-[#113559] mb-3">💾 Sauvegarde de vos informations personnelles</h3>
                        <div class="rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                            <img src="/img/guide/compte-infos-personnelles.png" alt="Informations personnelles" class="w-full h-auto">
                        </div>
                    </div>
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
                            Vous pouvez nous contacter via le téléphone au numéro indiqué dans le pied de page. Notre service client est disponible pour répondre à toutes vos questions.
                        </div>
                    </details>

                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Puis-je ajouter des compagnons à ma réservation ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            Oui, lors de la réservation vous pouvez enregistrer les informations de vos compagnons de voyage. Cela facilite les futures réservations en gardant leurs coordonnées en mémoire.
                        </div>
                    </details>

                    <details class="group border border-gray-200 rounded-lg">
                        <summary class="cursor-pointer p-4 font-semibold hover:bg-gray-50 flex justify-between items-center">
                            <span>Comment utiliser la barre de recherche du guide ?</span>
                            <span class="transform group-open:rotate-180 transition-transform">▼</span>
                        </summary>
                        <div class="p-4 pt-0 text-gray-700">
                            Tapez simplement un mot-clé dans la barre de recherche en haut de cette page. Le guide filtrera automatiquement les sections correspondantes pour vous montrer uniquement les informations pertinentes.
                        </div>
                    </details>
                </div>
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
