# 📋 Aide-mémoire : Événements Stripe à sélectionner

## ✅ ÉVÉNEMENTS À COCHER (3 événements)

Lors de la configuration de votre webhook dans Stripe Dashboard, sélectionnez **EXACTEMENT** ces 3 événements :

### 1. checkout.session.completed
- **Catégorie :** Checkout
- **Description :** Se déclenche quand une session Checkout est complétée
- **Action dans l'app :** Crée le paiement et confirme la réservation

### 2. payment_intent.succeeded
- **Catégorie :** PaymentIntent  
- **Description :** Se déclenche quand le paiement est effectivement capturé
- **Action dans l'app :** Double vérification que le paiement est bien passé

### 3. payment_intent.payment_failed
- **Catégorie :** PaymentIntent
- **Description :** Se déclenche en cas d'échec de paiement
- **Action dans l'app :** Enregistre l'échec dans les logs

---

## ❌ ÉVÉNEMENTS À NE PAS COCHER

### ❌ invoice_payment.paid
- **Pourquoi :** Cet événement concerne les **factures** et **abonnements**, pas les paiements Checkout ponctuels
- **Catégorie :** Paiement de la facture (Invoice)

### ❌ invoice_payment.succeeded
- **Pourquoi :** Même raison, uniquement pour les factures/abonnements

---

## 🔍 Comment les trouver dans Stripe Dashboard

1. Allez dans **Développeurs** > **Webhooks**
2. Cliquez sur **"Ajouter un endpoint"** ou éditez un webhook existant
3. Dans la section "Événements à envoyer", cliquez sur **"Sélectionner les événements"**

### Chercher les bons événements :

**Pour `checkout.session.completed` :**
- Déroulez la section **"Checkout"** (ou **"Paiement Checkout"**)
- Cochez `checkout.session.completed`

**Pour `payment_intent.succeeded` et `payment_intent.payment_failed` :**
- Déroulez la section **"PaymentIntent"** (ou **"Intention de paiement"**)
- Cochez `payment_intent.succeeded`
- Cochez `payment_intent.payment_failed`

---

## 📸 Capture d'écran annotée

```
┌─────────────────────────────────────────────────────────┐
│ Événements à envoyer                                    │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ ▼ Checkout                                    3 événements │
│   ☐ checkout.session.async_payment_failed               │
│   ☐ checkout.session.async_payment_succeeded            │
│   ☑ checkout.session.completed            ← COCHER     │
│   ☐ checkout.session.expired                            │
│                                                          │
│ ▼ PaymentIntent                              17 événements │
│   ☐ payment_intent.amount_capturable_updated            │
│   ☐ payment_intent.canceled                             │
│   ☐ payment_intent.created                              │
│   ☑ payment_intent.payment_failed          ← COCHER     │
│   ☐ payment_intent.processing                           │
│   ☑ payment_intent.succeeded               ← COCHER     │
│                                                          │
│ ▼ Paiement de la facture                    1 événement  │
│   ☐ invoice_payment.paid                   ← NE PAS COCHER │
│   ☐ invoice_payment.succeeded              ← NE PAS COCHER │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ Validation

Après avoir sélectionné les événements, vous devriez voir :

```
Événements sélectionnés : 3

• checkout.session.completed
• payment_intent.succeeded  
• payment_intent.payment_failed
```

---

## 🚀 Étapes suivantes

1. ✅ Cochez les 3 événements ci-dessus
2. ✅ Cliquez sur "Ajouter un endpoint" ou "Enregistrer"
3. ✅ Copiez le webhook secret (whsec_...)
4. ✅ Ajoutez-le dans votre `.env` : `STRIPE_WEBHOOK_SECRET=whsec_...`
5. ✅ Redémarrez votre serveur Laravel
6. ✅ Testez avec `stripe listen` ou un paiement de test

---

**Documentation complète :** [WEBHOOK_CONFIGURATION.md](WEBHOOK_CONFIGURATION.md)
