<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{{asset('css/header.css')}}"/>
    <title>@yield('title')</title>
</head>
<body>

    <header>
        <div class="logo">Club Med Logo</div>
        <nav class="nav-menu-left">
            <ul>
                <!-- Menu Découvrir Club Med -->
                <li class="dropdown">
                    <a href="{{ url('/') }}" class="dropdown-toggle">Découvrir Club Med</a>
                    <div class="dropdown-menu">
                        <div class="dropdown-column">
                            <h4>Tous nos types de séjours</h4>
                            <a href="#">Vacances en Resorts</a>
                            <a href="#">Circuits</a>
                            <a href="#">Escapades</a>
                            <a href="#">Les Croisières</a>
                            <a href="#">Villas & Chalets</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Notre sélection d'expériences</h4>
                            <a href="#">Vacances en famille</a>
                            <a href="#">Courts Séjours</a>
                            <a href="#">Voyage de noces</a>
                            <a href="#">Vacances en solo</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Notre offre de sports</h4>
                            <a href="#">Sports d'hiver</a>
                            <a href="#">Sports terrestres</a>
                            <a href="#">Sports nautiques</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Tourisme Responsable</h4>
                            <a href="#">Respect des sites naturels</a>
                            <a href="#">Développement local</a>
                            <a href="#">Employeur responsable</a>
                        </div>
                    </div>
                </li>

                <!-- Menu Destinations -->
                <li class="dropdown">
                    <a href="{{ url('/resorts') }}" class="dropdown-toggle">Destinations</a>
                    <div class="dropdown-menu">
                        <div class="dropdown-column">
                            <h4>TROUVEZ VOTRE SÉJOUR ></h4>
                            <h5>Europe & Méditerranée</h5>
                            <a href="#">France</a>
                            <a href="#">Grèce</a>
                            <a href="#">Espagne</a>
                            <a href="#">Italie</a>
                            <a href="#">Portugal</a>
                            <a href="#">Sicile</a>
                            <a href="#">Turquie</a>
                            <h5>Alpes</h5>
                            <a href="#">France</a>
                            <a href="#">Italie</a>
                            <a href="#">Suisse</a>
                            <a href="#">Les Alpes en été</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Afrique & Moyen Orient</h4>
                            <a href="#">Maroc</a>
                            <a href="#">Tunisie</a>
                            <a href="#">Sénégal</a>
                            <a href="#">Afrique du Sud</a>
                            <a href="#">Oman (2028)</a>
                            <h5>Océan Indien</h5>
                            <a href="#">Île Maurice</a>
                            <a href="#">Maldives</a>
                            <a href="#">Seychelles</a>
                            <h5>Les Caraïbes</h5>
                            <a href="#">Bahamas</a>
                            <a href="#">Guadeloupe</a>
                            <a href="#">Martinique</a>
                            <a href="#">République Dominicaine</a>
                            <a href="#">Turks et Caïcos</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Amérique du sud</h4>
                            <a href="#">Brésil</a>
                            <h5>Amérique du Nord & Centrale</h5>
                            <a href="#">Canada</a>
                            <a href="#">Mexique</a>
                            <h5>Asie</h5>
                            <a href="#">Chine</a>
                            <a href="#">Corée du sud</a>
                            <a href="#">Japon</a>
                            <a href="#">Thaïlande</a>
                            <a href="#">Malaisie (2026)</a>
                            <a href="#">Indonésie</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Nos destinations croisières</h4>
                            <a href="#">Croisières en Méditerranée</a>
                            <a href="#">Croisières aux Caraïbes</a>
                            <h5>Nos destinations Circuits Club Med Découverte</h5>
                            <a href="#">Europe & Méditerranée</a>
                            <a href="#">Caraïbes</a>
                            <a href="#">Amérique du Nord & Centrale</a>
                            <a href="#">Amérique du Sud</a>
                            <a href="#">Afrique & Moyen-Orient</a>
                            <a href="#">Asie & Océanie</a>
                            <a href="#">Océan Indien</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Nos nouveautés</h4>
                            <a href="#">South Africa Beach & Safari, Afrique du Sud</a>
                            <a href="#">Serre-Chevalier, Alpes</a>
                            <a href="#">Les Boucaniers, Martinique</a>
                            <a href="#">Cap Skirring, Sénégal</a>
                            <a href="#">Phuket, Thaïlande</a>
                            <a href="#">Borneo, Malaisie</a>
                            <a href="#">Toutes nos nouveautés</a>
                            <h5>Nos Best-sellers</h5>
                            <a href="#">Palmiye, Turquie</a>
                            <a href="#">Magna Marbella, Espagne</a>
                            <a href="#">Djerba La Douce, Tunisie</a>
                            <a href="#">Seychelles, Les Seychelles</a>
                        </div>
                    </div>
                </li>

                <!-- Menu Notre gamme Luxe -->
                <li class="dropdown">
                    <a href="{{ url('/') }}" class="dropdown-toggle">Notre gamme Luxe</a>
                    <div class="dropdown-menu">
                        <div class="dropdown-column">
                            <h4>Notre Offre Exclusive Collection</h4>
                            <a href="#">Tout savoir sur notre gamme luxe</a>
                            <h5>Nos Resorts gamme Luxe</h5>
                            <a href="#">Cefalù - Sicile</a>
                            <a href="#">La Plantation d'Albion - Île Maurice</a>
                            <a href="#">Michès Playa Esmeralda - Rep. Dominicaine</a>
                            <a href="#">Seychelles</a>
                            <a href="#">Val d'Isère</a>
                            <a href="#">Tous nos Resorts Exclusive Collection</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Nos espaces Luxe</h4>
                            <a href="#">La Rosière</a>
                            <a href="#">Les Arcs Panorama</a>
                            <a href="#">Tignes</a>
                            <a href="#">Valmorel</a>
                            <a href="#">Marrakech la Palmeraie</a>
                            <a href="#">Punta Cana - Rep. Dominicaine</a>
                            <a href="#">Cancún - Mexique</a>
                            <a href="#">Rio das Pedras - Brésil</a>
                            <a href="#">Kani - Maldives</a>
                            <a href="#">Quebec Charlevoix - Canada</a>
                            <a href="#">Kiroro Peak - Japon</a>
                            <a href="#">Tous nos Espaces Exclusive Collection</a>
                        </div>
                        <div class="dropdown-column">
                            <h4>Nos Croisières</h4>
                            <a href="#">Notre voilier Club Med 2</a>
                            <a href="#">Nos croisières en Méditerranée</a>
                            <a href="#">Nos croisières aux Caraïbes</a>
                            <a href="#">Toutes nos croisières</a>
                            <h5>Nos Villas & Chalets</h5>
                            <a href="#">Appartements-Chalets de Grand Massif Samoëns Morillon</a>
                            <a href="#">Appartements-Chalets de Valmorel</a>
                            <a href="#">Villas de Finolhu</a>
                            <a href="#">Villas d'Albion</a>
                            <a href="#">Tous nos Villas & Chalets</a>
                        </div>
                    </div>
                </li>
        </nav>
        <nav class="nav-menu-right">
            <ul class="right-menu">
                
                <li><a href="{{ url('/') }}" class="btn-offres">Nos Offres</a></li>
                <li><a href="{{ url('/') }}" class="icon-btn" title="Se connecter">👤</a></li>
                <li><a href="{{ url('/') }}" class="icon-btn" title="Besoin d'un conseil">📞</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>