<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class BotManController extends Controller
{
    private function makeLinks($links) {
        $html = '<div style="margin-top:10px;">';
        foreach ($links as $text => $url) {
            $html .= '<a href="'.$url.'" target="_top" style="display:inline-block;margin:4px;padding:8px 14px;background:#00457C;color:white;border-radius:20px;text-decoration:none;font-size:13px;">'.$text.'</a>';
        }
        $html .= '</div>';
        return $html;
    }

    private function getUser() {
        return Auth::user();
    }

    private function isLoggedIn(): bool {
        return Auth::check();
    }

    private function getGreeting(): string {
        $user = $this->getUser();
        if ($user) {
            return "Bonjour {$user->name} !";
        }
        return "Bonjour !";
    }

    public function handle()
    {
        $botman = app('botman');

        $botman->hears('(bonjour|bjr|salut|slt|hello|hey|hi|coucou|bonsoir|yo|wesh)', function (BotMan $bot) {
            $bot->typesAndWaits(1);
            if ($this->isLoggedIn()) {
                $links = $this->makeLinks([
                    'Voir les resorts' => '/resorts',
                    'Mes reservations' => '/mes-reservations',
                    'Mon profil' => '/user/profile',
                ]);
                $bot->reply($this->getGreeting() . " Comment puis-je vous aider ?\n\nTapez : <b>aide</b>, resorts, reserver, probleme..." . $links);
            } else {
                $links = $this->makeLinks([
                    'Voir les resorts' => '/resorts',
                    'Se connecter' => '/login',
                    'S\'inscrire' => '/inscription',
                ]);
                $bot->reply($this->getGreeting() . " Comment puis-je vous aider ?\n\nTapez : <b>aide</b>, resorts, reserver, probleme..." . $links);
            }
        });

        $botman->hears('(menu|aide|help|assistance|aidez|aider|besoin aide|soutien)', function (BotMan $bot) {
            $bot->typesAndWaits(1);
            if ($this->isLoggedIn()) {
                $links = $this->makeLinks([
                    'Resorts' => '/resorts',
                    'Mes reservations' => '/mes-reservations',
                    'Mon panier' => '/panier',
                    'Mon profil' => '/user/profile',
                ]);
                $bot->reply($this->getGreeting() . " Je peux vous aider avec :\n- <b>resorts</b> - Decouvrir les destinations\n- <b>reserver</b> - Faire une reservation\n- <b>probleme</b> - Support technique\n- <b>mon compte</b> - Gerer votre profil" . $links);
            } else {
                $links = $this->makeLinks([
                    'Resorts' => '/resorts',
                    'Se connecter' => '/login',
                    'S\'inscrire' => '/inscription',
                ]);
                $bot->reply("Bonjour ! Je peux vous aider avec :\n- <b>resorts</b> - Decouvrir les destinations\n- <b>reserver</b> - Faire une reservation\n- <b>probleme</b> - Support technique\n- <b>connexion</b> - Se connecter" . $links);
            }
        });

        $botman->hears('(problème|probleme|pb|souci|soucis|bug|erreur|marche pas|fonctionne pas|ça marche pas|ca marche pas|bloqué|bloque|planté)', function (BotMan $bot) {
            $bot->typesAndWaits(1);
            $bot->reply("<b>Support technique</b>\n\nQuel type de probleme rencontrez-vous ?\n\nTapez :\n- <b>connexion</b> - Probleme de connexion/mot de passe\n- <b>paiement</b> - Erreur de paiement\n- <b>reservation</b> - Probleme avec une reservation\n- <b>site</b> - Probleme d'affichage/navigation\n\nOu decrivez votre probleme en detail.");
        });

        $botman->hears('(mot de passe|password|mdp|oublié mot|oublie mot|connexion impossible|pas connecter|login impossible|identifiant|authentification|me connecter|reset password|reinitialiser)', function (BotMan $bot) {
            $bot->typesAndWaits(1);
            if ($this->isLoggedIn()) {
                $user = $this->getUser();
                $links = $this->makeLinks([
                    'Mon profil' => '/user/profile',
                ]);
                $bot->reply("Vous etes connecte(e) en tant que <b>{$user->name}</b> !\n\nPour modifier votre mot de passe, accedez a votre profil." . $links);
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                    'Creer un compte' => '/inscription',
                ]);
                $bot->reply("<b>Probleme de connexion ?</b>\n\n1. Allez sur 'Se connecter'\n2. Cliquez sur 'Mot de passe oublie'\n3. Entrez votre email\n4. Verifiez vos spams\n\nSi vous n'avez pas de compte, creez-en un !" . $links);
            }
        });

        $botman->hears('(paiement|payment|carte|bancaire|refusé|refusee|refuse|transaction|stripe|payer impossible|erreur paiement|carte refusée|cb|visa|mastercard|paypal)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Mon panier' => '/panier',
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Probleme de paiement ?</b>\n\nVerifiez :\n- Numero de carte correct (16 chiffres)\n- Date d'expiration valide\n- Code CVV (3 chiffres au dos)\n- Fonds suffisants\n\nEssayez une autre carte si le probleme persiste." . $links);
        });

        $botman->hears('(problème réservation|reservation probleme|annuler réservation|modifier réservation|reservation impossible|pas réservé)', function (BotMan $bot) {
            if ($this->isLoggedIn()) {
                $user = $this->getUser();
                $count = Reservation::where('user_id', $user->id)->count();
                $links = $this->makeLinks([
                    'Mes reservations' => '/mes-reservations',
                    'Nouvelle reservation' => '/resorts',
                ]);
                $bot->reply("<b>Vos reservations</b>\n\nVous avez <b>{$count}</b> reservation(s).\n\nConsultez 'Mes reservations' pour :\n- Voir les details\n- Modifier\n- Annuler" . $links);
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                ]);
                $bot->reply("Pour gerer vos reservations, vous devez etre connecte(e)." . $links);
            }
        });

        $botman->hears('(site|affichage|page|chargement|lent|bloque|bloqué)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Accueil' => '/',
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Probleme d'affichage ?</b>\n\n1. Actualisez la page (F5)\n2. Videz le cache du navigateur\n3. Essayez un autre navigateur\n4. Desactivez les bloqueurs de pub" . $links);
        });

        $botman->hears('(mon compte|profil|mes infos|modifier email|mes données|mon profil)', function (BotMan $bot) {
            if ($this->isLoggedIn()) {
                $user = $this->getUser();
                $links = $this->makeLinks([
                    'Mon profil' => '/user/profile',
                    'Mes reservations' => '/mes-reservations',
                    'Mon panier' => '/panier',
                ]);
                $bot->reply("<b>Votre compte</b>\n\n- Nom : {$user->name}\n- Email : {$user->email}\n\nAccedez a votre profil pour modifier vos informations." . $links);
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                    'Creer un compte' => '/inscription',
                ]);
                $bot->reply("Vous n'etes pas connecte(e).\n\nConnectez-vous pour acceder a votre compte et vos reservations." . $links);
            }
        });

        $botman->hears('(mes réservations|mes reservations|ma réservation|suivi|historique réservation)', function (BotMan $bot) {
            $bot->typesAndWaits(1);
            if ($this->isLoggedIn()) {
                $user = $this->getUser();
                $reservations = Reservation::where('user_id', $user->id)->latest()->take(3)->get();
                $count = Reservation::where('user_id', $user->id)->count();
                
                $links = $this->makeLinks([
                    'Voir tout' => '/mes-reservations',
                    'Nouvelle reservation' => '/resorts',
                ]);
                
                if ($count > 0) {
                    $bot->reply("<b>Vos reservations</b>\n\nVous avez <b>{$count}</b> reservation(s).\nConsultez la page pour les details." . $links);
                } else {
                    $bot->reply("Vous n'avez aucune reservation.\n\nDecouvrez nos resorts et reservez votre prochain sejour !" . $links);
                }
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                ]);
                $bot->reply("Connectez-vous pour voir vos reservations." . $links);
            }
        });

        $botman->hears('(resort|resorts|destination|destinations|village|villages|où partir|ou partir|vacances|sejour|séjour|voyage)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir tous les resorts' => '/resorts',
            ]);
            $bot->reply("<b>Nos resorts</b>\n\n80 destinations dans le monde : plage, montagne, luxe..." . $links);
        });

        $botman->hears('(réserver|réservation|reservation|booking|comment réserver|reserver|booker|book|dispo|disponibilité|disponibilite)', function (BotMan $bot) {
            if ($this->isLoggedIn()) {
                $links = $this->makeLinks([
                    'Choisir un resort' => '/resorts',
                    'Mon panier' => '/panier',
                ]);
                $bot->reply("<b>Reserver un sejour</b>\n\n1. Choisissez un resort\n2. Selectionnez vos dates\n3. Ajoutez au panier\n4. Payez en ligne" . $links);
            } else {
                $links = $this->makeLinks([
                    'Voir les resorts' => '/resorts',
                    'Se connecter' => '/login',
                ]);
                $bot->reply("<b>Reserver un sejour</b>\n\nConnectez-vous d'abord, puis :\n1. Choisissez un resort\n2. Selectionnez vos dates\n3. Payez en ligne" . $links);
            }
        });

        $botman->hears('(activité|activités|activite|sport|sports|loisir|loisirs|faire|activites|animation|animations|divertissement)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir les resorts' => '/resorts',
            ]);
            $bot->reply("<b>60+ activites incluses !</b>\n\n<b>Nautiques :</b> Voile, paddle, plongee, ski nautique\n<b>Terre :</b> Tennis, golf, tir a l'arc, VTT\n<b>Fitness :</b> Yoga, aquagym, musculation\n<b>Montagne :</b> Ski, snowboard, raquettes\n\nTous niveaux, cours collectifs inclus !" . $links);
        });

        $botman->hears('(enfant|enfants|famille|familial|bébé|bebe|baby|kids|club enfant|mini club|ado|adolescent|teen|garderie|creche)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts famille' => '/resorts',
            ]);
            $bot->reply("<b>Clubs enfants inclus</b>\n\n- <b>4 mois-2 ans :</b> Baby Club (supplement)\n- <b>2-3 ans :</b> Petit Club Med\n- <b>4-10 ans :</b> Mini Club Med\n- <b>11-17 ans :</b> Club Med Passworld\n\nEncadrement professionnel toute la journee !" . $links);
        });

        $botman->hears('(prix|tarif|tarifs|coût|cout|combien|cher|budget|pas cher|promo|promotion|reduction|solde)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir les tarifs' => '/resorts',
            ]);
            $bot->reply("<b>Formule tout inclus</b>\n\nA partir de <b>~800EUR/semaine</b> selon destination et saison.\n\n<b>Inclus :</b> Hebergement + repas + open bar + 60 activites + clubs enfants\n\nConsultez les resorts pour les tarifs exacts." . $links);
        });

        $botman->hears('(tout compris|all inclusive|formule|inclus|comprend)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Reserver' => '/resorts',
                'Guide' => '/guide',
            ]);
            $bot->reply("<b>Tout est compris !</b>\n\n- Hebergement\n- Repas & open bar\n- 60+ activites\n- Clubs enfants" . $links);
        });

        $botman->hears('(ski|montagne|neige|alpes|hiver|snowboard|piste)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts montagne' => '/resorts',
            ]);
            $bot->reply("<b>Ski tout inclus</b>\n\nForfait + cours + materiel inclus !" . $links);
        });

        $botman->hears('(plage|mer|soleil|tropical|caraïbes|maldives|île|océan)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts soleil' => '/resorts',
            ]);
            $bot->reply("<b>Destinations soleil</b>\n\nCaraibes, Maldives, Mediterranee..." . $links);
        });

        $botman->hears('(connexion|connecter|inscription|inscrire|login|register|créer compte)', function (BotMan $bot) {
            if ($this->isLoggedIn()) {
                $user = $this->getUser();
                $links = $this->makeLinks([
                    'Mon profil' => '/user/profile',
                    'Se deconnecter' => '/logout',
                ]);
                $bot->reply("Vous etes connecte(e) en tant que <b>{$user->name}</b>" . $links);
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                    'Creer un compte' => '/inscription',
                ]);
                $bot->reply("<b>Accedez a votre espace</b>\n\nConnectez-vous ou creez un compte pour reserver." . $links);
            }
        });

        $botman->hears('(panier|cart|commande|achats)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir mon panier' => '/panier',
                'Ajouter un sejour' => '/resorts',
            ]);
            $bot->reply("<b>Votre panier</b>\n\nConsultez votre panier pour finaliser votre commande." . $links);
        });

        $botman->hears('(contact|contacter|téléphone|appeler|email|humain|conseiller)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Guide' => '/guide',
                'Accueil' => '/',
            ]);
            $bot->reply("<b>Nous contacter</b>\n\n0810 810 810 (lun-sam 9h-19h)\nOu utilisez ce chatbot pour une aide rapide !" . $links);
        });

        $botman->hears('(G\\.?O|gentil organisateur|animateur|staff|équipe)', function (BotMan $bot) {
            $bot->reply("<b>Les G.O</b>\n\nGentils Organisateurs : moniteurs et animateurs Club Med qui font vivre l'esprit Club Med !");
        });

        $botman->hears('(great members|fidélité|membre|avantage|points|statut)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Mon compte' => '/user/profile',
            ]);
            $bot->reply("<b>Great Members</b>\n\nProgramme de fidelite avec avantages exclusifs !" . $links);
        });

        $botman->hears('(annuler|annulation|modifier|modification|rembours|flexible)', function (BotMan $bot) {
            if ($this->isLoggedIn()) {
                $links = $this->makeLinks([
                    'Mes reservations' => '/mes-reservations',
                ]);
                $bot->reply("<b>Modifier/Annuler</b>\n\nAccedez a vos reservations pour modifier ou annuler selon les conditions." . $links);
            } else {
                $links = $this->makeLinks([
                    'Se connecter' => '/login',
                ]);
                $bot->reply("Connectez-vous pour gerer vos reservations." . $links);
            }
        });

        // === NOUVEAUX SUJETS FAQ ===

        $botman->hears('(assurance|assurances|garantie|annulation voyage|couverture|protege|protection)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Assurance voyage</b>\n\nUne assurance annulation est proposee lors de la reservation.\n\nElle couvre : maladie, accident, deces d'un proche, licenciement.\n\nConsultez les conditions lors du paiement." . $links);
        });

        $botman->hears('(transfert|navette|aeroport|transport|arrivee|depart|avion|vol)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Transferts aeroport</b>\n\nSelon le resort, les transferts peuvent etre :\n- Inclus (ex: Maldives)\n- En option\n- A organiser vous-meme\n\nVerifiez sur la fiche du resort choisi." . $links);
        });

        $botman->hears('(wifi|internet|connexion internet|4g|reseau|connecte)', function (BotMan $bot) {
            $bot->reply("<b>Wifi dans les resorts</b>\n\nLe wifi est disponible dans tous nos resorts !\n\n- Gratuit dans les espaces communs\n- Souvent gratuit en chambre\n- Debit variable selon destination");
        });

        $botman->hears('(animal|animaux|chien|chat|pet|compagnie)', function (BotMan $bot) {
            $bot->reply("<b>Animaux de compagnie</b>\n\nLes animaux ne sont malheureusement <b>pas acceptes</b> dans nos resorts.\n\nPensez a les faire garder pendant votre sejour !");
        });

        $botman->hears('(valise|bagages|emporter|emmener|vetement|dress code|tenue|quoi prendre|quoi emmener)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Que mettre dans sa valise ?</b>\n\n<b>Basiques :</b> Maillot, creme solaire, chapeau\n<b>Soirees :</b> Tenue decontractee elegante\n<b>Sports :</b> Chaussures adaptees\n\nAmbiance decontractee, pas de dress code strict !" . $links);
        });

        $botman->hears('(pourboire|tips|tip|argent liquide|cash|especes)', function (BotMan $bot) {
            $bot->reply("<b>Pourboires</b>\n\nLes pourboires ne sont <b>pas obligatoires</b> car tout est inclus !\n\nSi vous souhaitez en laisser, c'est apprecie mais jamais attendu.");
        });

        $botman->hears('(restaurant|repas|manger|nourriture|cuisine|buffet|bar|boisson|alcool|open bar)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Restauration tout inclus</b>\n\n- <b>Petit-dejeuner :</b> Buffet copieux\n- <b>Dejeuner :</b> Buffet ou restaurant\n- <b>Diner :</b> Buffet + restos de specialites\n- <b>Open bar :</b> Cocktails, vins, softs\n\nRestaurants gastronomiques en supplement dans certains resorts." . $links);
        });

        $botman->hears('(spa|massage|bien-etre|bienetre|detente|relaxation|soins)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Spa & Bien-etre</b>\n\nLa plupart de nos resorts disposent d'un spa.\n\n- Acces espace detente souvent inclus\n- Soins et massages en supplement\n- Reservation sur place recommandee" . $links);
        });

        $botman->hears('(covid|sanitaire|vaccin|test|pcr|masque|protocole)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
            ]);
            $bot->reply("<b>Conditions sanitaires</b>\n\nLes conditions evoluent selon les pays.\n\nVerifiez les exigences de votre destination avant le depart sur les sites officiels." . $links);
        });

        $botman->hears('(horaire|heure|check.?in|check.?out|arriver|partir|depart chambre)', function (BotMan $bot) {
            $bot->reply("<b>Horaires</b>\n\n- <b>Check-in :</b> A partir de 15h\n- <b>Check-out :</b> Avant 10h\n\nPossibilite d'early check-in ou late check-out selon disponibilite (supplement eventuel).");
        });

        // === FIN NOUVEAUX SUJETS ===

        $botman->hears('(merci|thanks|parfait|super|génial|genial|top|excellent|cool|nickel)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Accueil' => '/',
                'Resorts' => '/resorts',
            ]);
            $bot->reply("Avec plaisir ! Bonnes vacances au Club Med !" . $links);
        });

        $botman->hears('(au revoir|bye|à bientôt|a bientot|ciao|adieu|salut bye)', function (BotMan $bot) {
            $bot->reply("A bientot au Club Med !");
        });

        $botman->fallback(function (BotMan $bot) {
            $message = $bot->getMessage()->getText();
            $user = $this->getUser();
            
            $bot->typesAndWaits(1);

            $userContext = $user 
                ? "L'utilisateur s'appelle {$user->name} et est connecte." 
                : "L'utilisateur n'est pas connecte.";

            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . env('MISTRAL_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.mistral.ai/v1/chat/completions', [
                        "model" => "mistral-tiny",
                        "messages" => [
                            [
                                "role" => "system",
                                "content" => "Tu es l'assistant support du site Club Med (projet etudiant). {$userContext} 
                                Reponds en 1-2 phrases courtes MAX. 
                                N'invente JAMAIS de numero de telephone, email ou site web.
                                Si c'est un probleme technique, guide l'utilisateur avec des mots-cles : connexion, paiement, reservation, aide.
                                Sinon, suggere des mots-cles : resorts, reserver, activites, prix, enfants, ski, plage, spa, restaurant, wifi, transfert, assurance."
                            ],
                            [
                                "role" => "user", 
                                "content" => $message
                            ]
                        ],
                        "max_tokens" => 100
                    ]);

                if ($response->successful()) {
                    $texteIA = $response->json()['choices'][0]['message']['content'];
                    
                    if ($this->isLoggedIn()) {
                        $links = $this->makeLinks([
                            'Resorts' => '/resorts',
                            'Mes reservations' => '/mes-reservations',
                        ]);
                    } else {
                        $links = $this->makeLinks([
                            'Resorts' => '/resorts',
                            'Connexion' => '/login',
                        ]);
                    }
                    $bot->reply($texteIA . $links);
                } else {
                    $this->sendFallbackMessage($bot);
                }

            } catch (\Exception $e) {
                $this->sendFallbackMessage($bot);
            }
        });

        $botman->listen();
    }

    private function sendFallbackMessage(BotMan $bot) {
        if ($this->isLoggedIn()) {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
                'Mes reservations' => '/mes-reservations',
            ]);
        } else {
            $links = $this->makeLinks([
                'Resorts' => '/resorts',
                'Connexion' => '/login',
            ]);
        }
        $bot->reply("Je peux vous aider avec : <b>aide</b>, resorts, reserver, probleme, mon compte..." . $links);
    }
}
