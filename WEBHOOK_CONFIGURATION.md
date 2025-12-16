# Configuration des Webhooks Stripe

## 📋 Vue d'ensemble

Les webhooks Stripe permettent de recevoir des notifications en temps réel sur les événements de paiement, assurant ainsi que le statut des réservations est toujours synchronisé avec les paiements effectués.

## ⚡ Événements configurés

Votre application écoute maintenant **3 événements Stripe** :

### 1. `checkout.session.completed` ⭐ (Principal)
- **Quand :** Une session Checkout est complétée avec succès
- **Action :** 
  - Crée un enregistrement de paiement dans la table `paiement`
  - Met à jour le statut de la réservation à `Confirmée`
- **Utilisation :** C'est l'événement principal pour Stripe Checkout

### 2. `payment_intent.succeeded` ⭐ (Confirmation)
- **Quand :** Le paiement est effectivement capturé et réussi
- **Action :** 
  - Confirme que le paiement a bien été traité
  - Met à jour le statut de la réservation à `Confirmée` (si ce n'est pas déjà fait)
- **Utilisation :** Double sécurité pour s'assurer que le paiement est bien passé

### 3. `payment_intent.payment_failed` ⚠️ (Erreurs)
- **Quand :** Une tentative de paiement échoue
- **Action :** 
  - Enregistre l'échec dans les logs
  - Peut être utilisé pour notifier l'utilisateur
- **Utilisation :** Permet de détecter et gérer les problèmes de paiement

## 🔧 Configuration dans Stripe Dashboard

### Étape 1 : Accéder aux webhooks
1. Connectez-vous à [Stripe Dashboard](https://dashboard.stripe.com)
2. Allez dans **Développeurs** > **Webhooks**
3. Cliquez sur **"Ajouter un endpoint"**

### Étape 2 : Configurer l'endpoint

#### En développement (local) :
```
URL de l'endpoint : https://votre-tunnel.ngrok.io/stripe/webhook
```
> 💡 Utilisez [ngrok](https://ngrok.com/) ou [Stripe CLI](https://stripe.com/docs/stripe-cli) pour tester en local

#### En production :
```
URL de l'endpoint : https://votre-domaine.com/stripe/webhook
```

### Étape 3 : Sélectionner les événements

**Cochez exactement ces 3 événements :**

✅ **checkout.session.completed** (dans la section "Checkout")
✅ **payment_intent.succeeded** (dans la section "PaymentIntent")
✅ **payment_intent.payment_failed** (dans la section "PaymentIntent")

> ⚠️ **NE PAS cocher** `invoice_payment.paid` - cet événement est pour les factures/abonnements, pas pour les paiements Checkout ponctuels.

### Étape 4 : Récupérer le secret du webhook
1. Après avoir créé le webhook, cliquez dessus
2. Allez dans l'onglet **"Signing secret"**
3. Révélez et copiez le secret (commence par `whsec_...`)

### Étape 5 : Ajouter le secret à votre .env

Ouvrez votre fichier `.env` et ajoutez :

```env
STRIPE_WEBHOOK_SECRET=whsec_votre_secret_ici
```

## 🧪 Tester les webhooks

### Option 1 : Stripe CLI (Recommandé pour le développement)

```bash
# Installer Stripe CLI
# Voir : https://stripe.com/docs/stripe-cli

# Se connecter
stripe login

# Rediriger les webhooks vers votre serveur local
stripe listen --forward-to localhost:8000/stripe/webhook

# Le CLI affichera le webhook secret - ajoutez-le à votre .env
```

### Option 2 : Ngrok (Alternative)

```bash
# Installer ngrok
# Voir : https://ngrok.com/download

# Démarrer un tunnel vers votre serveur local
ngrok http 8000

# Utilisez l'URL https fournie pour configurer le webhook dans Stripe Dashboard
```

### Option 3 : Tester depuis Stripe Dashboard

1. Dans **Développeurs** > **Webhooks**
2. Cliquez sur votre endpoint
3. Allez dans l'onglet **"Tester"**
4. Sélectionnez un événement et envoyez-le

## 📊 Vérifier que ça fonctionne

### 1. Consulter les logs Laravel

```bash
tail -f storage/logs/laravel.log
```

Vous devriez voir :
```
[INFO] Payment Intent succeeded: {"payment_intent_id":"pi_xxx","amount":100,"currency":"eur"}
[INFO] Webhook: Reservation updated: {"numreservation":"123","statut":"Confirmée"}
```

### 2. Vérifier dans Stripe Dashboard

Dans **Développeurs** > **Webhooks** > Votre endpoint > **Tentatives récentes** :
- ✅ Les événements doivent avoir un statut **200 OK**
- ❌ Si vous voyez des erreurs 400/500, vérifiez vos logs

### 3. Vérifier dans votre base de données

```sql
-- Vérifier qu'une réservation a été mise à jour
SELECT numreservation, statut, prixtotal 
FROM reservation 
WHERE numreservation = 123;

-- Vérifier qu'un paiement a été enregistré
SELECT * FROM paiement 
WHERE numreservation = 123;
```

## 🔒 Sécurité

### Vérification de signature
Le webhook vérifie automatiquement la signature Stripe si `STRIPE_WEBHOOK_SECRET` est défini :

```php
if ($webhookSecret) {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $webhookSecret
    );
}
```

### Protection CSRF
La route webhook est **exclue** de la protection CSRF (normal pour les webhooks) :

```php
// routes/web.php
// Cette route est AVANT les middlewares auth
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
```

## 🐛 Dépannage

### Problème : Webhook reçoit 419 (CSRF Token Mismatch)
**Solution :** Vérifiez que la route est bien **avant** les middlewares dans `web.php`

### Problème : Webhook reçoit 400 (Bad Request)
**Solution :** 
- Vérifiez que le `STRIPE_WEBHOOK_SECRET` est correct dans `.env`
- Vérifiez que la signature n'a pas été modifiée en transit

### Problème : La réservation n'est pas mise à jour
**Solution :**
- Vérifiez les logs : `storage/logs/laravel.log`
- Vérifiez que le `metadata.numreservation` est bien envoyé avec la session Checkout

### Problème : Événement non traité
**Solution :** 
- Vérifiez que vous avez bien coché les 3 événements dans Stripe Dashboard
- Consultez les logs pour voir quel type d'événement est reçu

## 📝 Flux complet de paiement

```mermaid
1. Client clique sur "Payer"
   ↓
2. StripeController crée une session Checkout avec metadata.numreservation
   ↓
3. Client est redirigé vers Stripe Checkout
   ↓
4. Client saisit ses infos bancaires
   ↓
5. Stripe traite le paiement
   ↓
6. ✅ Paiement réussi → Stripe envoie "checkout.session.completed"
   ↓
7. Webhook reçoit l'événement et met à jour la réservation
   ↓
8. Stripe envoie "payment_intent.succeeded" (confirmation)
   ↓
9. Webhook confirme que tout est OK
   ↓
10. Client est redirigé vers la page de succès
```

## 🔗 Liens utiles

- [Documentation Stripe Webhooks](https://stripe.com/docs/webhooks)
- [Stripe CLI](https://stripe.com/docs/stripe-cli)
- [Tester les webhooks](https://stripe.com/docs/webhooks/test)
- [Événements Stripe](https://stripe.com/docs/api/events/types)

## ✅ Checklist de configuration

- [ ] Webhook créé dans Stripe Dashboard
- [ ] 3 événements sélectionnés (checkout.session.completed, payment_intent.succeeded, payment_intent.payment_failed)
- [ ] Secret du webhook ajouté à `.env` (STRIPE_WEBHOOK_SECRET)
- [ ] Application redémarrée après modification du `.env`
- [ ] Test effectué avec Stripe CLI ou Dashboard
- [ ] Logs vérifiés (pas d'erreurs)
- [ ] Réservation bien mise à jour dans la base de données
- [ ] Paiement enregistré dans la table `paiement`

---

**Votre webhook est maintenant configuré et prêt à recevoir les notifications Stripe ! 🎉**
