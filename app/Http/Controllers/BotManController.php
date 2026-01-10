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

        $botman->hears('(bonjour|salut|hello|hey|coucou|bonsoir)', function (BotMan $bot) {
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

        $botman->hears('(menu|aide|help|assistance)', function (BotMan $bot) {
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

        $botman->hears('(problème|probleme|souci|bug|erreur|marche pas|fonctionne pas|ça marche pas|ca marche pas)', function (BotMan $bot) {
            $bot->reply("<b>Support technique</b>\n\nQuel type de probleme rencontrez-vous ?\n\nTapez :\n- <b>connexion</b> - Probleme de connexion/mot de passe\n- <b>paiement</b> - Erreur de paiement\n- <b>reservation</b> - Probleme avec une reservation\n- <b>site</b> - Probleme d'affichage/navigation\n\nOu decrivez votre probleme en detail.");
        });

        $botman->hears('(mot de passe|password|mdp|oublié mot|connexion impossible|pas connecter|login impossible|identifiant|authentification|me connecter)', function (BotMan $bot) {
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

        $botman->hears('(paiement|payment|carte|bancaire|refusé|refusee|transaction|stripe|payer impossible|erreur paiement|carte refusée)', function (BotMan $bot) {
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

        $botman->hears('(resort|resorts|destination|destinations|village|villages|où partir|ou partir)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir tous les resorts' => '/resorts',
            ]);
            $bot->reply("<b>Nos resorts</b>\n\n80 destinations dans le monde : plage, montagne, luxe..." . $links);
        });

        $botman->hears('(réserver|réservation|booking|comment réserver|reserver)', function (BotMan $bot) {
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

        $botman->hears('(activité|activités|sport|sports|loisir|loisirs|faire|activites)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir les resorts' => '/resorts',
            ]);
            $bot->reply("<b>60+ activites incluses !</b>\n\nSports nautiques, tennis, ski, yoga, spa, golf..." . $links);
        });

        $botman->hears('(enfant|enfants|famille|bébé|kids|club enfant|mini club|ado)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Resorts famille' => '/resorts',
            ]);
            $bot->reply("<b>Clubs enfants inclus</b>\n\n- 2-3 ans : Petit Club\n- 4-10 ans : Mini Club\n- 11-17 ans : Passworld" . $links);
        });

        $botman->hears('(prix|tarif|tarifs|coût|combien|cher|budget)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Voir les tarifs' => '/resorts',
            ]);
            $bot->reply("<b>Formule tout inclus</b>\n\nHebergement + repas + open bar + 60 activites !" . $links);
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

        $botman->hears('(merci|thanks|parfait|super|génial|top|excellent)', function (BotMan $bot) {
            $links = $this->makeLinks([
                'Accueil' => '/',
                'Resorts' => '/resorts',
            ]);
            $bot->reply("Avec plaisir ! Bonnes vacances au Club Med !" . $links);
        });

        $botman->hears('(au revoir|bye|à bientôt|ciao|adieu)', function (BotMan $bot) {
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
                        'Authorization' => 'Bearer xIqtgRu4xODr2bKRjUsz9MbLJvAOartV',
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
                                Sinon, suggere des mots-cles : resorts, reserver, activites, prix, enfants, ski, plage."
                            ],
                            [
                                "role" => "user", 
                                "content" => $message
                            ]
                        ],
                        "max_tokens" => 80
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
