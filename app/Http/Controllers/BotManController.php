<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // Pour la mémoire de l'IA
use App\Models\Reservation;

class BotManController extends Controller
{
    /**
     * Génère des boutons HTML jolis et fonctionnels
     */
    private function makeLinks($links) {
        $html = '<div style="margin-top:10px;">';
        foreach ($links as $text => $url) {
            $html .= '<a href="'.$url.'" target="_top" style="display:inline-block;margin:4px;padding:8px 14px;background:#00457C;color:white;border-radius:20px;text-decoration:none;font-size:13px;font-family:Arial,sans-serif;">'.$text.'</a>';
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

    /**
     * Construit le "Cerveau" de l'IA avec le contexte de l'utilisateur
     */
    private function getSystemPrompt() {
        $basePrompt = "Tu es l'assistant virtuel officiel du Club Med. Tu es poli, concis et tu incites à la réservation.";
        
        if ($this->isLoggedIn()) {
            $user = $this->getUser();
            $context = " L'utilisateur s'appelle {$user->name}.";
            
            // Récupérer la prochaine réservation future pour donner du contexte
            $nextResa = Reservation::where('user_id', $user->id)
                        ->where('datedebut', '>=', now())
                        ->with('resort') // Assurez-vous d'avoir la relation dans le modèle
                        ->orderBy('datedebut', 'asc')
                        ->first();

            if ($nextResa && $nextResa->resort) {
                $context .= " Il a un voyage prévu à {$nextResa->resort->nomresort} le " . date('d/m/Y', strtotime($nextResa->datedebut)) . ".";
            } else {
                $context .= " Il n'a pas de réservation future pour le moment.";
            }
            
            return $basePrompt . $context;
        }

        return $basePrompt . " L'utilisateur est un visiteur non connecté. Incite-le à se connecter.";
    }

    public function handle()
    {
        $botman = app('botman');

        // -----------------------------------------------------------
        // 1. GESTION DES SALUTATIONS (Mots clés)
        // -----------------------------------------------------------
        $botman->hears('(bonjour|bjr|salut|slt|hello|hey|hi|coucou)', function (BotMan $bot) {
            $user = $this->getUser();
            $name = $user ? $user->name : "visiteur";
            
            // Boutons dynamiques selon l'état de connexion
            $linksData = $this->isLoggedIn() 
                ? ['Voir les Resorts' => '/resorts', 'Mon Compte' => '/dashboard'] 
                : ['Voir les Resorts' => '/resorts', 'Connexion' => '/login'];

            $bot->reply("Bonjour $name ! Je suis l'IA du Club Med. Comment puis-je vous aider aujourd'hui ? 🌴" . $this->makeLinks($linksData));
        });

        // -----------------------------------------------------------
        // 2. BONUS : COMMANDE SPÉCIALE "MON SÉJOUR" (Sans IA)
        // -----------------------------------------------------------
        // Répond directement depuis la BDD -> Rapide et précis (Points Bonus)
        $botman->hears('(mon séjour|ma réservation|ma resa|quand je pars|mon voyage)', function (BotMan $bot) {
            if (!$this->isLoggedIn()) {
                $bot->reply("Veuillez vous connecter pour voir vos voyages. " . $this->makeLinks(['Connexion' => '/login']));
                return;
            }

            $user = $this->getUser();
            $resa = Reservation::where('user_id', $user->id)
                        ->where('datedebut', '>=', now())
                        ->with('resort')
                        ->orderBy('datedebut', 'asc')
                        ->first();

            if ($resa && $resa->resort) {
                $bot->reply("✈️ Votre prochain départ est prévu pour <b>{$resa->resort->nomresort}</b> le " . date('d/m/Y', strtotime($resa->datedebut)) . ". Préparez vos valises !");
            } else {
                $bot->reply("Je ne trouve pas de réservation future. C'est le moment de craquer pour une destination ! ☀️" . $this->makeLinks(['Voir les offres' => '/resorts']));
            }
        });

        // -----------------------------------------------------------
        // 3. INTELLIGENCE ARTIFICIELLE (OpenAI)
        // -----------------------------------------------------------
        $botman->fallback(function (BotMan $bot) {
            $message = $bot->getMessage();
            
            // Gestion de l'historique (Mémoire immédiate)
            $history = Session::get('chat_history', []);
            
            // On prépare les messages pour OpenAI
            $messagesPayload = [];
            $messagesPayload[] = ['role' => 'system', 'content' => $this->getSystemPrompt()];
            
            // On ajoute les 4 derniers échanges pour le contexte
            foreach (array_slice($history, -4) as $msg) {
                $messagesPayload[] = $msg;
            }
            
            // On ajoute la question actuelle
            $messagesPayload[] = ['role' => 'user', 'content' => $message->getText()];

            try {
                // Appel API sécurisé
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('MISTRAL_API_KEY'),
                ])->post('https://api.mistral.ai/v1/chat/completions', [
                    'model' => 'mistral-tiny', 
                    'messages' => $messagesPayload,
                    'temperature' => 0.7,
                    'max_tokens' => 150,
                ]);

                if ($response->successful()) {
                    $texteIA = $response->json()['choices'][0]['message']['content'];
                    
                    $history[] = ['role' => 'user', 'content' => $message->getText()];
                    $history[] = ['role' => 'assistant', 'content' => $texteIA];
                    Session::put('chat_history', $history);

                    $links = $this->isLoggedIn() 
                        ? $this->makeLinks(['Nos Destinations' => '/resorts', 'Mes réservations' => '/mes-reservations']) 
                        : $this->makeLinks(['Nos Destinations' => '/resorts', 'Connexion' => '/login']);

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
        $links = $this->isLoggedIn() 
            ? $this->makeLinks(['Resorts' => '/resorts', 'Support' => '/contact']) 
            : $this->makeLinks(['Resorts' => '/resorts', 'Connexion' => '/login']);
            
        $bot->reply("Je rencontre une petite difficulté technique. Mais je peux vous guider via ces liens :" . $links);
    }
}