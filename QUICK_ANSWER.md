# ⚡ Réponse Rapide : Quels événements Stripe sélectionner ?

## 🎯 Les 3 événements à cocher

Pour mettre à jour automatiquement le statut des réservations après paiement, vous devez sélectionner **exactement 3 événements** dans Stripe Dashboard :

### ✅ 1. checkout.session.completed
- **Section :** Checkout (ou "Paiement Checkout")
- **Quand :** La session de paiement Checkout est terminée avec succès
- **Action :** Crée le paiement et change le statut de la réservation à "Confirmée"

### ✅ 2. payment_intent.succeeded  
- **Section :** PaymentIntent (ou "Intention de paiement")
- **Quand :** Le paiement est effectivement capturé par Stripe
- **Action :** Double vérification que le paiement est bien passé

### ✅ 3. payment_intent.payment_failed
- **Section :** PaymentIntent (ou "Intention de paiement")
- **Quand :** Une tentative de paiement échoue
- **Action :** Enregistre l'erreur dans les logs

---

## ❌ À NE PAS sélectionner

### ❌ invoice_payment.paid
- **Section :** Paiement de la facture
- **Pourquoi ne pas le cocher :** Cet événement concerne les **factures** et **abonnements récurrents**, pas les paiements ponctuels via Checkout

---

## 🎬 Prochaines étapes

1. ✅ Cochez les 3 événements ci-dessus
2. ✅ Créez le webhook
3. ✅ Copiez le secret du webhook (commence par `whsec_...`)
4. ✅ Ajoutez-le dans votre `.env` :
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_votre_secret_ici
   ```
5. ✅ Redémarrez votre serveur Laravel

---

## 📚 Documentation complète

Pour plus de détails :
- Guide visuel : [STRIPE_EVENTS_VISUAL_GUIDE.md](STRIPE_EVENTS_VISUAL_GUIDE.md)
- Configuration complète : [WEBHOOK_CONFIGURATION.md](WEBHOOK_CONFIGURATION.md)
- Résumé des modifications : [WEBHOOK_IMPLEMENTATION_SUMMARY.md](WEBHOOK_IMPLEMENTATION_SUMMARY.md)

---

**✨ C'est tout ! Avec ces 3 événements, vos réservations seront automatiquement confirmées après paiement.**
