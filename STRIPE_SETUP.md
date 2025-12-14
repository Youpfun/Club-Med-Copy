# Configuration Stripe - Club Med

## Installation complète de Stripe

### Packages installés
- `stripe/stripe-php` v19.0.0 - SDK PHP officiel de Stripe

### Fichiers configurés

#### 1. **Configuration (.env)**
Les variables d'environnement Stripe ont été ajoutées:
```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

#### 2. **Configuration (config/stripe.php)**
Fichier de configuration centralisé pour Stripe avec accès aux variables d'environnement.

#### 3. **Base de données (migrations)**
Migration ajoutée pour supporter les colonnes Stripe dans la table `paiement`:
- `stripe_session_id` - ID de la session Stripe
- `stripe_payment_intent` - ID du paiement Stripe
- `statut` - Statut du paiement (En attente, Complété, Échoué, Remboursé)

#### 4. **Routes de paiement (routes/web.php)**
Routes authentifiées pour le flux de paiement:
- `GET /reservation/{id}/payment` - Page de paiement
- `POST /reservation/{id}/checkout` - Initier le paiement Stripe
- `GET /reservation/{id}/payment-success` - Confirmation du paiement
- `GET /reservation/{id}/payment-cancel` - Annulation du paiement

#### 5. **Contrôleur (app/Http/Controllers/StripeController.php)**
Méthodes principales:
- `showPaymentPage()` - Affiche la page de paiement avec résumé de réservation
- `checkout()` - Crée une session Stripe et redirige l'utilisateur
- `success()` - Traite la confirmation du paiement et met à jour le statut
- `cancel()` - Gère l'annulation du paiement

#### 6. **Vues Blade**

**resources/views/payment/checkout.blade.php**
- Page de paiement sécurisée
- Résumé de la réservation
- Bouton de paiement Stripe

**resources/views/payment/success.blade.php**
- Page de confirmation après paiement réussi
- Détails de la réservation
- Liens vers les réservations et la navigation

#### 7. **Intégration avec le panier**
Le bouton "Payer maintenant" du panier (resources/views/panier/detail.blade.php) 
a été mis à jour pour rediriger vers `route('payment.page', $reservation->numreservation)`

---

## Configuration Stripe (À faire)

### 1. Créer un compte Stripe
- Aller sur https://dashboard.stripe.com
- S'inscrire ou se connecter
- Aller à **API Keys** dans Settings

### 2. Récupérer les clés d'API

#### Mode test (recommandé pour le développement)
Dans https://dashboard.stripe.com/apikeys (basculez en mode test):
```
Publishable key (Public): pk_test_...
Secret key (Secrète): sk_test_...
```

#### Mode production (après tests)
Basculez à droite sur "Live" pour obtenir:
```
Publishable key: pk_live_...
Secret key: sk_live_...
```

### 3. Mettre à jour .env
```env
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

### 4. Webhook Stripe (optionnel mais recommandé)
Pour gérer les événements Stripe en temps réel:

1. Aller à https://dashboard.stripe.com/webhooks
2. Créer un nouveau webhook endpoint:
   - URL: `https://votresite.com/stripe/webhook`
   - Événements à sélectionner:
     - `checkout.session.completed`
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`

3. Copier le Signing Secret et l'ajouter à `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
   ```

---

## Flux de paiement

### 1. Utilisateur complète sa réservation
- L'utilisateur ajoute une réservation au panier (statut: `en_attente`)

### 2. Utilisateur clique "Payer maintenant"
- Redirection vers `/reservation/{id}/payment`
- Page affiche le résumé de la réservation

### 3. Utilisateur clique "Procéder au paiement"
- POST vers `/reservation/{id}/checkout`
- Création d'une session Stripe Checkout
- Redirection vers le formulaire de paiement sécurisé Stripe

### 4. Utilisateur rentre ses coordonnées
- Sur la page Stripe sécurisée
- Paiement par carte bancaire

### 5. Confirmation du paiement
- **Succès**: Redirection vers `/reservation/{id}/payment-success`
  - Enregistrement du paiement en base
  - Mise à jour du statut: `Confirmée`
  - Affichage de la page de succès
- **Annulation**: Redirection vers `/reservation/{id}/payment-cancel`
  - Réservation reste en attente

---

## Statuts de réservation

- `en_attente` - Réservation en attente de paiement (dans le panier)
- `Confirmée` - Paiement complété avec succès
- `En attente` - Réservation validée en attente de confirmations
- `Terminée` - Séjour terminé

---

## Tests en mode développement

### Cartes de test Stripe
https://stripe.com/docs/testing

**Paiement réussi:**
```
Numéro: 4242 4242 4242 4242
Expiration: 12/25
CVC: 123
```

**Paiement décliné:**
```
Numéro: 4000 0000 0000 0002
Expiration: 12/25
CVC: 123
```

---

## Notes de sécurité

✅ Les clés publiques et secrètes sont stockées en variables d'environnement
✅ Les vérifications d'autorisation sont en place (l'utilisateur ne peut payer que ses propres réservations)
✅ Les paiements sont gérés par Stripe (PCI compliant)
✅ Les données sensibles ne sont jamais loggées

---

## Prochaines étapes

1. ✅ Configuration initiale complète
2. 📋 Récupérer les clés Stripe et mettre à jour `.env`
3. 📋 Configurer le webhook Stripe (pour les notifications en temps réel)
4. 📋 Tester avec les cartes de test
5. 📋 Configurer les emails de confirmation
6. 📋 Passer en mode production avec les vraies clés

---

## Support et documentation

- Documentation officielle Stripe: https://stripe.com/docs
- PHP SDK Stripe: https://github.com/stripe/stripe-php
- Laravel + Stripe: https://stripe.com/docs/payments/checkout/accept-a-payment
