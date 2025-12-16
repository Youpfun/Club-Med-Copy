# 🎯 Guide visuel : Trouver les événements Stripe

## 📍 Où suis-je dans l'interface ?

Vous êtes sur la page de **sélection des événements** lors de la création d'un webhook.

---

## 🔍 SECTION 1 : Checkout (ou "Paiement Checkout")

```
╔══════════════════════════════════════════════════════════╗
║  > Checkout                                 9 événements ║
╠══════════════════════════════════════════════════════════╣
║  ☐ checkout.session.async_payment_failed                 ║
║  ☐ checkout.session.async_payment_succeeded              ║
║  ☑ checkout.session.completed           👈 COCHEZ CELUI-CI
║  ☐ checkout.session.expired                              ║
║  ☐ ...                                                    ║
╚══════════════════════════════════════════════════════════╝
```

**✅ Cochez :** `checkout.session.completed`

**Description :** Se produit lorsqu'un paiement de facture est payé avec succès.

---

## 🔍 SECTION 2 : PaymentIntent (ou "Intention de paiement")

```
╔══════════════════════════════════════════════════════════╗
║  > PaymentIntent                           17 événements ║
╠══════════════════════════════════════════════════════════╣
║  ☐ payment_intent.amount_capturable_updated              ║
║  ☐ payment_intent.canceled                               ║
║  ☐ payment_intent.created                                ║
║  ☑ payment_intent.payment_failed         👈 COCHEZ CELUI-CI
║  ☐ payment_intent.partially_funded                       ║
║  ☐ payment_intent.processing                             ║
║  ☐ payment_intent.requires_action                        ║
║  ☑ payment_intent.succeeded              👈 COCHEZ CELUI-CI
║  ☐ ...                                                    ║
╚══════════════════════════════════════════════════════════╝
```

**✅ Cochez :** 
- `payment_intent.succeeded`
- `payment_intent.payment_failed`

---

## ❌ SECTION 3 : Paiement de la facture (NE PAS COCHER)

```
╔══════════════════════════════════════════════════════════╗
║  > Paiement de la facture                  1 événement   ║
╠══════════════════════════════════════════════════════════╣
║  ☐ invoice_payment.paid                  👈 NE PAS COCHER ║
╚══════════════════════════════════════════════════════════╝
```

**❌ NE COCHEZ PAS :** `invoice_payment.paid`

**Raison :** Cet événement est pour les **factures récurrentes** et **abonnements**, pas pour les paiements ponctuels Checkout.

---

## 📊 Résumé : Les 3 événements à cocher

| Événement | Section | Description |
|-----------|---------|-------------|
| ✅ `checkout.session.completed` | Checkout | Paiement Checkout complété |
| ✅ `payment_intent.succeeded` | PaymentIntent | Paiement capturé avec succès |
| ✅ `payment_intent.payment_failed` | PaymentIntent | Paiement échoué |

---

## 🎨 Interface Stripe : Vue d'ensemble

```
┌─────────────────────────────────────────────────────────────┐
│ Créer un endpoint                                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ URL de l'endpoint                                            │
│ ┌────────────────────────────────────────────────────────┐  │
│ │ https://votre-domaine.com/stripe/webhook               │  │
│ └────────────────────────────────────────────────────────┘  │
│                                                              │
│ Description (optionnel)                                      │
│ ┌────────────────────────────────────────────────────────┐  │
│ │ Webhook pour confirmation des réservations Club Med    │  │
│ └────────────────────────────────────────────────────────┘  │
│                                                              │
│ Événements à envoyer                                         │
│                                                              │
│ ( ) Tous les événements                                      │
│ (●) Sélectionner les événements                              │
│                                                              │
│     [Sélectionner les événements]  👈 CLIQUEZ ICI           │
│                                                              │
│     ┌─────────────────────────────────────────────────┐     │
│     │ Événements sélectionnés : 3                      │     │
│     │                                                  │     │
│     │ • checkout.session.completed                     │     │
│     │ • payment_intent.succeeded                       │     │
│     │ • payment_intent.payment_failed                  │     │
│     └─────────────────────────────────────────────────┘     │
│                                                              │
│                        [Annuler]  [Ajouter un endpoint]      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🚀 Étapes suivantes après sélection

1. Cliquez sur **"Ajouter un endpoint"**
2. Stripe créera le webhook et affichera le **secret de signature**
3. Copiez ce secret (commence par `whsec_...`)
4. Ajoutez-le dans votre `.env` :
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```
5. Redémarrez votre serveur Laravel

---

## 💡 Astuce : Comment retrouver une section ?

Si vous ne trouvez pas une section, utilisez la **barre de recherche** :

```
┌─────────────────────────────────────────────────────────────┐
│ Rechercher un événement                                      │
│ ┌────────────────────────────────────────────────────────┐  │
│ │ 🔍 checkout session completed                          │  │
│ └────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

**Recherches utiles :**
- `checkout session completed` → trouve `checkout.session.completed`
- `payment intent succeeded` → trouve `payment_intent.succeeded`
- `payment intent failed` → trouve `payment_intent.payment_failed`

---

## ✅ Validation

Après avoir coché les 3 événements, vous devriez voir ce résumé :

```
┌─────────────────────────────────────────────────────────────┐
│ Événements sélectionnés : 3                                  │
│                                                              │
│ • checkout.session.completed                                 │
│ • payment_intent.payment_failed                              │
│ • payment_intent.succeeded                                   │
└─────────────────────────────────────────────────────────────┘
```

Si vous voyez bien ces **3 événements**, c'est parfait ! ✅

---

## 📞 Besoin d'aide ?

Si vous ne trouvez pas les événements :
1. Vérifiez que vous êtes en mode **Test** (pas Production)
2. Assurez-vous d'avoir cliqué sur **"Sélectionner les événements"**
3. Essayez de chercher avec la barre de recherche
4. Consultez la documentation Stripe : [stripe.com/docs/webhooks](https://stripe.com/docs/webhooks)

---

**🎉 Une fois les 3 événements sélectionnés, cliquez sur "Ajouter un endpoint" !**
