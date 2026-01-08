<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        // Salutations
        $botman->hears('(bonjour|salut|hello|hey|coucou|bonsoir)', function (BotMan $bot) {
            $bot->reply("Bonjour et bienvenue au Club Med ! 🌴 Je suis votre assistant virtuel. Comment puis-je vous aider ? Vous pouvez me poser des questions sur nos resorts, les activités, les réservations ou les formules tout compris.");
        });

        // Questions sur le concept Club Med / All Inclusive
        $botman->hears('(tout compris|all inclusive|formule|inclus|comprend)', function (BotMan $bot) {
            $bot->reply("La formule tout compris Club Med, c'est la liberté absolue ! ✨ Elle inclut :\n• L'hébergement en chambre confortable\n• La pension complète (petit-déjeuner, déjeuner, dîner + snacks)\n• Open bar (boissons à volonté)\n• Plus de 60 activités sportives et loisirs\n• Clubs enfants (de 4 mois à 17 ans)\n• Soirées et spectacles\n\nLe tout sans supplément !");
        });

        // Questions sur les resorts / destinations
        $botman->hears('(resort|resorts|destination|destinations|village|villages|où partir|ou partir)', function (BotMan $bot) {
            $bot->reply("Club Med vous propose près de 80 resorts d'exception dans le monde entier ! 🌍\n• Soleil & Plage : Caraïbes, Maldives, Méditerranée...\n• Montagne & Ski : Alpes françaises, Suisse, Japon...\n• Exclusive Collection : nos resorts 5 Tridents de luxe\n\n👉 <a href='/resorts'>Découvrez tous nos resorts</a>");
        });

        // Questions sur les réservations
        $botman->hears('(réserver|réservation|réservations|booking|comment réserver)', function (BotMan $bot) {
            $bot->reply("Réserver votre séjour Club Med est simple ! 🎯\n\n1️⃣ Choisissez votre resort sur notre site\n2️⃣ Sélectionnez vos dates et votre type de chambre\n3️⃣ Indiquez le nombre de participants (adultes/enfants)\n4️⃣ Personnalisez avec des activités optionnelles\n5️⃣ Procédez au paiement sécurisé\n\n👉 <a href='/resorts'>Commencer ma réservation</a>");
        });

        // Questions sur les activités
        $botman->hears('(activité|activités|sport|sports|loisir|loisirs|faire)', function (BotMan $bot) {
            $bot->reply("Au Club Med, les activités sont incluses et encadrées par nos G.O® ! 🏄‍♂️\n\n• Sports nautiques : voile, plongée, ski nautique, paddle...\n• Fitness & Bien-être : yoga, aquagym, spa...\n• Sports terrestres : tennis, golf, tir à l'arc...\n• En montagne : ski, snowboard, cours collectifs...\n• Pour les enfants : clubs par âge, cirque, activités créatives...\n\nChaque resort a ses spécialités !");
        });

        // Questions sur les enfants / famille
        $botman->hears('(enfant|enfants|famille|bébé|kids|club enfant|mini club|ado)', function (BotMan $bot) {
            $bot->reply("Club Med est le paradis des familles ! 👨‍👩‍👧‍👦\n\nNos clubs par âge (inclus dans le séjour) :\n• Petit Club Med® : 2-3 ans\n• Mini Club Med® : 4-10 ans\n• Club Med Passworld® : 11-17 ans\n\n🍼 Baby Club Med® (4-23 mois) disponible en supplément dans certains resorts.\n\nActivités adaptées, repas équilibrés et G.O® qualifiés pour des vacances sereines !");
        });

        // Questions sur les prix / tarifs
        $botman->hears('(prix|tarif|tarifs|coût|combien|cher|budget|payer)', function (BotMan $bot) {
            $bot->reply("Nos tarifs varient selon le resort, la saison et le type de chambre. 💰\n\nCe qui est TOUJOURS inclus :\n✅ Hébergement + Pension complète\n✅ Open bar\n✅ 60+ activités\n✅ Clubs enfants (2-17 ans)\n✅ Spectacles & soirées\n\n💡 Astuce : réservez tôt pour bénéficier des meilleurs tarifs !\n\n👉 Consultez les prix sur la fiche de chaque resort.");
        });

        // Questions sur l'annulation / modification
        $botman->hears('(annuler|annulation|modifier|modification|rembours|flexible)', function (BotMan $bot) {
            $bot->reply("Nous comprenons que vos plans peuvent changer. 📋\n\nConditions générales :\n• Modification possible selon disponibilité\n• Annulation avec remboursement partiel selon délai\n• Assurance annulation recommandée à la réservation\n\n💡 Consultez nos conditions de vente ou contactez notre service client pour votre situation spécifique.");
        });

        // Questions sur le ski / montagne
        $botman->hears('(ski|montagne|neige|alpes|hiver|snowboard|piste)', function (BotMan $bot) {
            $bot->reply("Vivez la montagne version Club Med ! ⛷️🏔️\n\nInclus dans votre séjour ski :\n• Forfait remontées mécaniques\n• Cours collectifs de ski/snowboard (tous niveaux)\n• Matériel de ski (dans la plupart des resorts)\n• Après-ski festif !\n\nNos resorts : Val Thorens, Tignes, La Rosière, Valmorel, Arcs Extrême...\n\n👉 <a href='/resorts'>Voir nos resorts de montagne</a>");
        });

        // Questions sur la plage / mer / soleil
        $botman->hears('(plage|mer|soleil|tropical|caraïbes|maldives|île|océan|bronzer)', function (BotMan $bot) {
            $bot->reply("Cap sur le soleil avec Club Med ! ☀️🏝️\n\nNos destinations soleil :\n• Caraïbes : Punta Cana, Martinique, Guadeloupe...\n• Océan Indien : Maldives, Maurice, Seychelles...\n• Méditerranée : Grèce, Turquie, Sicile...\n• Asie : Bali, Thaïlande...\n\nPlages de rêve, eaux turquoise et cocotiers vous attendent !\n\n👉 <a href='/resorts'>Explorer nos resorts balnéaires</a>");
        });

        // Questions sur les G.O / personnel
        $botman->hears('(G\\.?O|gentil organisateur|animateur|staff|équipe)', function (BotMan $bot) {
            $bot->reply("Les G.O® (Gentils Organisateurs) sont l'âme du Club Med ! 💫\n\nVenus du monde entier, ils sont :\n• Moniteurs sportifs diplômés\n• Animateurs des clubs enfants\n• Artistes des spectacles du soir\n• Toujours disponibles et souriants\n\nIls partagent leurs repas avec vous et créent cette ambiance unique Club Med !");
        });

        // Contact / aide
        $botman->hears('(contact|contacter|téléphone|appeler|email|aide|humain|conseiller)', function (BotMan $bot) {
            $bot->reply("Notre équipe est à votre écoute ! 📞\n\n☎️ 0810 810 810 (service 0,05€/min + prix appel)\n📅 Du lundi au samedi : 9h-19h\n\nOu posez-moi directement vos questions, je ferai de mon mieux pour vous aider !");
        });

        // Remerciements
        $botman->hears('(merci|thanks|parfait|super|génial|top)', function (BotMan $bot) {
            $bot->reply("Avec plaisir ! 😊 N'hésitez pas si vous avez d'autres questions. Je vous souhaite de merveilleuses vacances Club Med ! 🌴✨");
        });

        // Au revoir
        $botman->hears('(au revoir|bye|à bientôt|ciao)', function (BotMan $bot) {
            $bot->reply("Au revoir et à très bientôt au Club Med ! 👋🌴 Bonnes vacances !");
        });

        // Great Members / fidélité
        $botman->hears('(great members|fidélité|membre|avantage|points|statut)', function (BotMan $bot) {
            $bot->reply("Découvrez Great Members, notre programme de fidélité ! 🌟\n\nPlus vous voyagez, plus vous gagnez :\n• Turquoise → Argent → Or → Platine\n• Réductions exclusives\n• Surclassements selon disponibilité\n• Cadeaux de bienvenue\n• Accès prioritaire aux nouveautés\n\nChaque séjour vous rapproche du niveau supérieur !");
        });

        // Réponse par défaut - utilise l'IA Mistral
        $botman->fallback(function (BotMan $bot, $message) {
            $bot->typesAndWaits(1);

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
                                "content" => "Tu es l'assistant virtuel du Club Med, le pionnier des vacances tout compris depuis 1950. Tu dois répondre de manière chaleureuse, professionnelle et concise aux questions des clients. Tu connais les resorts Club Med dans le monde entier, les formules tout inclus (hébergement, pension complète, open bar, activités, clubs enfants), les G.O® (Gentils Organisateurs), et le programme de fidélité Great Members. Réponds toujours en français et de façon positive."
                            ],
                            [
                                "role" => "user", 
                                "content" => $message
                            ]
                        ]
                    ]);

                if ($response->successful()) {
                    $texteIA = $response->json()['choices'][0]['message']['content'];
                    $bot->reply($texteIA);
                } else {
                    $bot->reply("Je n'ai pas bien compris votre demande. 🤔\n\nVoici ce que je peux faire pour vous :\n• Infos sur nos **resorts** et destinations\n• Détails sur la formule **tout compris**\n• Questions sur les **activités**\n• Aide pour votre **réservation**\n• Infos sur les **clubs enfants**\n\nEssayez avec des mots-clés comme : resort, activités, prix, ski, plage, enfants...");
                }

            } catch (\Exception $e) {
                $bot->reply("Je n'ai pas bien compris votre demande. 🤔\n\nVoici ce que je peux faire pour vous :\n• Infos sur nos **resorts** et destinations\n• Détails sur la formule **tout compris**\n• Questions sur les **activités**\n• Aide pour votre **réservation**\n• Infos sur les **clubs enfants**\n\nEssayez avec des mots-clés comme : resort, activités, prix, ski, plage, enfants...");
            }
        });

        $botman->listen();
    }
}