# Club Med Copy – Application de réservation

Application Laravel reproduisant l’expérience Club Med : découverte des resorts, parcours de réservation multi-étapes, panier, gestion des utilisateurs et avis. Front propulsé par Blade + Vite/Tailwind, authentification Jetstream/Fortify avec 2FA par OTP.

## Fonctionnalités principales
- Listing des resorts et fiche détaillée (photos, localisation, activités).
- Parcours de réservation en 3 étapes avec choix de la chambre et options de transport.
- Panier utilisateur (réservations en attente), paiement Stripe intégré.
- Webhooks Stripe pour synchronisation automatique des paiements.
- Historique des réservations et dépôt d’avis.
- Authentification Laravel Jetstream/Fortify + 2FA OTP, gestion du profil.

## Prérequis
- PHP ≥ 8.1, Composer
- Node.js + npm
- Base de données MySQL/MariaDB ou SQLite

## Installation
1) Cloner le dépôt puis installer les dépendances :
```bash
composer install
npm install
```
2) Copier l’exemple d’environnement et configurer la base de données :
```bash
cp .env.example .env   # sous Windows : copy .env.example .env
```
Renseigner `DB_*` (MySQL/MariaDB) ou `DB_CONNECTION=sqlite` + chemin du fichier.

3) Générer la clé et créer le lien de stockage :
```bash
php artisan key:generate
php artisan storage:link
```

4) Créer la base :
```bash
php artisan migrate      # ajoute :php artisan db:seed si des seeders existent
```

## Lancer en développement
Terminal 1 :
```bash
php artisan serve
```
Terminal 2 :
```bash
npm run dev
```
## Tests
```bash
php artisan test
```

## Configuration Stripe (Paiements)

### 1. Configuration des clés API
Ajoutez vos clés Stripe dans `.env` :
```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 2. Configuration des webhooks
Pour que les réservations soient automatiquement confirmées après paiement :

1. Créez un webhook dans [Stripe Dashboard](https://dashboard.stripe.com/webhooks)
2. Configurez l'URL : `https://votre-domaine.com/stripe/webhook`
3. Sélectionnez ces événements :
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. Copiez le webhook secret dans `.env`

**📚 Documentation complète :** Voir [WEBHOOK_CONFIGURATION.md](WEBHOOK_CONFIGURATION.md)

**🔍 Vérifier la configuration :**
```bash
bash check-webhook-setup.sh    # Linux/Mac
# ou
sh check-webhook-setup.sh      # Windows Git Bash
```

## Points d'entrée utiles
- Routes principales : `routes/web.php`
- Contrôleurs métier : `app/Http/Controllers/`
- Webhooks Stripe : `app/Http/Controllers/StripeWebhookController.php`
- Vues Blade : `resources/views/`
- Configuration Jetstream/Fortify : `config/jetstream.php`, `config/fortify.php`
- Configuration Stripe : `config/stripe.php`

## Dépannage rapide
- Clé manquante : `php artisan key:generate`
- Assets absents : vérifier `npm run dev`
- Cache incohérent : `php artisan optimize:clear`
- Webhooks Stripe : consulter `storage/logs/laravel.log`
