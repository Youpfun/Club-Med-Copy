#!/bin/bash

# Script de test pour vérifier la configuration des webhooks Stripe

echo "╔═══════════════════════════════════════════════════════════╗"
echo "║   🔍 Vérification de la configuration des webhooks      ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo ""

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Vérifier si le fichier .env existe
if [ ! -f .env ]; then
    echo -e "${RED}✗ Fichier .env introuvable${NC}"
    exit 1
fi

# Vérifier les clés Stripe
echo "1. Vérification des clés Stripe dans .env:"
echo "   ----------------------------------------"

if grep -q "^STRIPE_PUBLIC_KEY=sk_" .env; then
    echo -e "   ${RED}✗ ATTENTION : STRIPE_PUBLIC_KEY contient une clé secrète !${NC}"
    echo -e "     ${YELLOW}La clé publique doit commencer par 'pk_'${NC}"
else
    if grep -q "^STRIPE_PUBLIC_KEY=pk_" .env && [ -n "$(grep '^STRIPE_PUBLIC_KEY=' .env | cut -d'=' -f2)" ]; then
        echo -e "   ${GREEN}✓ STRIPE_PUBLIC_KEY configurée${NC}"
    else
        echo -e "   ${RED}✗ STRIPE_PUBLIC_KEY manquante ou invalide${NC}"
    fi
fi

if grep -q "^STRIPE_SECRET_KEY=sk_" .env && [ -n "$(grep '^STRIPE_SECRET_KEY=' .env | cut -d'=' -f2)" ]; then
    echo -e "   ${GREEN}✓ STRIPE_SECRET_KEY configurée${NC}"
else
    echo -e "   ${RED}✗ STRIPE_SECRET_KEY manquante ou invalide${NC}"
fi

if grep -q "^STRIPE_WEBHOOK_SECRET=whsec_" .env && [ -n "$(grep '^STRIPE_WEBHOOK_SECRET=' .env | cut -d'=' -f2)" ]; then
    echo -e "   ${GREEN}✓ STRIPE_WEBHOOK_SECRET configurée${NC}"
    WEBHOOK_CONFIGURED=true
else
    echo -e "   ${YELLOW}⚠ STRIPE_WEBHOOK_SECRET non configurée${NC}"
    echo -e "     ${YELLOW}Les webhooks fonctionneront sans vérification de signature${NC}"
    WEBHOOK_CONFIGURED=false
fi

echo ""

# Vérifier que le contrôleur webhook existe
echo "2. Vérification du contrôleur webhook:"
echo "   ------------------------------------"

if [ -f app/Http/Controllers/StripeWebhookController.php ]; then
    echo -e "   ${GREEN}✓ StripeWebhookController.php existe${NC}"
    
    # Vérifier les méthodes
    if grep -q "handleCheckoutSessionCompleted" app/Http/Controllers/StripeWebhookController.php; then
        echo -e "   ${GREEN}✓ Méthode handleCheckoutSessionCompleted présente${NC}"
    else
        echo -e "   ${RED}✗ Méthode handleCheckoutSessionCompleted manquante${NC}"
    fi
    
    if grep -q "handlePaymentIntentSucceeded" app/Http/Controllers/StripeWebhookController.php; then
        echo -e "   ${GREEN}✓ Méthode handlePaymentIntentSucceeded présente${NC}"
    else
        echo -e "   ${RED}✗ Méthode handlePaymentIntentSucceeded manquante${NC}"
    fi
    
    if grep -q "handlePaymentIntentFailed" app/Http/Controllers/StripeWebhookController.php; then
        echo -e "   ${GREEN}✓ Méthode handlePaymentIntentFailed présente${NC}"
    else
        echo -e "   ${RED}✗ Méthode handlePaymentIntentFailed manquante${NC}"
    fi
else
    echo -e "   ${RED}✗ StripeWebhookController.php introuvable${NC}"
fi

echo ""

# Vérifier la route webhook
echo "3. Vérification de la route webhook:"
echo "   ---------------------------------"

if grep -q "stripe/webhook" routes/web.php; then
    echo -e "   ${GREEN}✓ Route /stripe/webhook configurée${NC}"
    
    # Vérifier que la route est bien avant les middlewares auth
    WEBHOOK_LINE=$(grep -n "stripe/webhook" routes/web.php | cut -d: -f1)
    AUTH_LINE=$(grep -n "auth:sanctum" routes/web.php | head -1 | cut -d: -f1)
    
    if [ "$WEBHOOK_LINE" -lt "$AUTH_LINE" ]; then
        echo -e "   ${GREEN}✓ Route webhook avant les middlewares auth${NC}"
    else
        echo -e "   ${RED}✗ ATTENTION : Route webhook après les middlewares auth${NC}"
        echo -e "     ${YELLOW}Déplacez la route avant 'Route::middleware([...])' dans web.php${NC}"
    fi
else
    echo -e "   ${RED}✗ Route /stripe/webhook non trouvée${NC}"
fi

echo ""

# Vérifier le fichier de config
echo "4. Vérification du fichier config/stripe.php:"
echo "   ------------------------------------------"

if [ -f config/stripe.php ]; then
    echo -e "   ${GREEN}✓ config/stripe.php existe${NC}"
    
    if grep -q "webhook_secret" config/stripe.php; then
        echo -e "   ${GREEN}✓ Paramètre webhook_secret configuré${NC}"
    else
        echo -e "   ${RED}✗ Paramètre webhook_secret manquant${NC}"
    fi
else
    echo -e "   ${RED}✗ config/stripe.php introuvable${NC}"
fi

echo ""

# Tester la connexion au webhook (si serveur actif)
echo "5. Test de connexion au webhook (optionnel):"
echo "   ------------------------------------------"

if command -v curl &> /dev/null; then
    # Déterminer l'URL de base
    if grep -q "^APP_URL=" .env; then
        APP_URL=$(grep "^APP_URL=" .env | cut -d'=' -f2)
        WEBHOOK_URL="$APP_URL/stripe/webhook"
        
        echo -e "   ${YELLOW}⏳ Test de connexion à $WEBHOOK_URL${NC}"
        
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$WEBHOOK_URL" 2>/dev/null)
        
        if [ "$HTTP_CODE" -eq 400 ] || [ "$HTTP_CODE" -eq 200 ]; then
            echo -e "   ${GREEN}✓ Endpoint webhook accessible (HTTP $HTTP_CODE)${NC}"
            echo -e "     ${YELLOW}Note: 400 est normal sans payload Stripe valide${NC}"
        elif [ "$HTTP_CODE" -eq 000 ]; then
            echo -e "   ${YELLOW}⚠ Serveur non accessible (pas démarré ?)${NC}"
        else
            echo -e "   ${YELLOW}⚠ HTTP $HTTP_CODE reçu${NC}"
        fi
    else
        echo -e "   ${YELLOW}⚠ APP_URL non configurée dans .env${NC}"
    fi
else
    echo -e "   ${YELLOW}⚠ curl non installé - test de connexion ignoré${NC}"
fi

echo ""
echo "════════════════════════════════════════════════════════════"

# Résumé
if [ "$WEBHOOK_CONFIGURED" = true ]; then
    echo -e "${GREEN}✓ Configuration webhook complète${NC}"
    echo ""
    echo "📋 Prochaines étapes :"
    echo "   1. Créez un webhook dans Stripe Dashboard"
    echo "   2. Configurez l'URL : https://votre-domaine.com/stripe/webhook"
    echo "   3. Sélectionnez ces événements :"
    echo "      • checkout.session.completed"
    echo "      • payment_intent.succeeded"
    echo "      • payment_intent.payment_failed"
    echo "   4. Testez avec : stripe listen --forward-to localhost:8000/stripe/webhook"
else
    echo -e "${YELLOW}⚠ Configuration webhook incomplète${NC}"
    echo ""
    echo "📋 Actions requises :"
    echo "   1. Créez un webhook dans Stripe Dashboard"
    echo "   2. Copiez le webhook secret (whsec_...)"
    echo "   3. Ajoutez-le à .env : STRIPE_WEBHOOK_SECRET=whsec_..."
    echo "   4. Redémarrez votre serveur Laravel"
fi

echo ""
echo "📚 Documentation complète : voir WEBHOOK_CONFIGURATION.md"
echo "════════════════════════════════════════════════════════════"
