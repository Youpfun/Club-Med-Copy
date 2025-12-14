#!/bin/bash
# Script de vérification de la configuration Stripe

echo "=== Vérification de la configuration Stripe ==="
echo ""

# Vérifier les variables d'environnement
echo "1. Vérification des variables d'environnement:"
if grep -q "STRIPE_PUBLIC_KEY" .env; then
    echo "   ✓ STRIPE_PUBLIC_KEY trouvé"
else
    echo "   ✗ STRIPE_PUBLIC_KEY manquant"
fi

if grep -q "STRIPE_SECRET_KEY" .env; then
    echo "   ✓ STRIPE_SECRET_KEY trouvé"
else
    echo "   ✗ STRIPE_SECRET_KEY manquant"
fi

if grep -q "STRIPE_WEBHOOK_SECRET" .env; then
    echo "   ✓ STRIPE_WEBHOOK_SECRET trouvé"
else
    echo "   ✗ STRIPE_WEBHOOK_SECRET manquant"
fi

echo ""

# Vérifier les fichiers de configuration
echo "2. Vérification des fichiers:"
if [ -f "config/stripe.php" ]; then
    echo "   ✓ config/stripe.php existe"
else
    echo "   ✗ config/stripe.php manquant"
fi

if [ -f "app/Http/Controllers/StripeController.php" ]; then
    echo "   ✓ StripeController.php existe"
else
    echo "   ✗ StripeController.php manquant"
fi

# Vérifier les migrations
echo ""
echo "3. Vérification des migrations:"
php artisan migrate:status | grep -i paiement

# Vérifier les packages
echo ""
echo "4. Vérification des packages Composer:"
if grep -q "stripe/stripe-php" composer.lock; then
    echo "   ✓ stripe/stripe-php installé"
    grep "stripe/stripe-php" composer.lock | grep -o '"version": "[^"]*"' | head -1
else
    echo "   ✗ stripe/stripe-php non installé"
fi

echo ""
echo "=== Configuration Stripe vérifiée ==="
