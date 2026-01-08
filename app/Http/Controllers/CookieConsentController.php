<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    /**
     * Récupérer le statut actuel du consentement cookies
     */
    public function getConsent(Request $request): JsonResponse
    {
        $consent = $request->cookie('cookie_consent');
        
        if (!$consent) {
            return response()->json([
                'hasConsent' => false,
                'preferences' => null
            ]);
        }

        $preferences = json_decode($consent, true);

        return response()->json([
            'hasConsent' => true,
            'preferences' => $preferences
        ]);
    }

    /**
     * Sauvegarder les préférences de cookies
     */
    public function saveConsent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'essential' => 'required|boolean',
            'analytics' => 'required|boolean',
            'marketing' => 'required|boolean',
            'functional' => 'required|boolean',
        ]);

        // Les cookies essentiels sont toujours activés
        $validated['essential'] = true;
        $validated['timestamp'] = now()->toISOString();

        $cookie = Cookie::make(
            'cookie_consent',
            json_encode($validated),
            60 * 24 * 365, // 1 an
            '/',
            null,
            config('session.secure'),
            false, // httpOnly = false pour permettre la lecture JS
            false,
            'Lax'
        );

        return response()->json([
            'success' => true,
            'message' => 'Préférences de cookies enregistrées',
            'preferences' => $validated
        ])->withCookie($cookie);
    }

    /**
     * Accepter tous les cookies
     */
    public function acceptAll(): JsonResponse
    {
        $preferences = [
            'essential' => true,
            'analytics' => true,
            'marketing' => true,
            'functional' => true,
            'timestamp' => now()->toISOString()
        ];

        $cookie = Cookie::make(
            'cookie_consent',
            json_encode($preferences),
            60 * 24 * 365,
            '/',
            null,
            config('session.secure'),
            false,
            false,
            'Lax'
        );

        return response()->json([
            'success' => true,
            'message' => 'Tous les cookies acceptés',
            'preferences' => $preferences
        ])->withCookie($cookie);
    }

    /**
     * Refuser tous les cookies (sauf essentiels)
     */
    public function rejectAll(): JsonResponse
    {
        $preferences = [
            'essential' => true,
            'analytics' => false,
            'marketing' => false,
            'functional' => false,
            'timestamp' => now()->toISOString()
        ];

        $cookie = Cookie::make(
            'cookie_consent',
            json_encode($preferences),
            60 * 24 * 365,
            '/',
            null,
            config('session.secure'),
            false,
            false,
            'Lax'
        );

        return response()->json([
            'success' => true,
            'message' => 'Cookies non-essentiels refusés',
            'preferences' => $preferences
        ])->withCookie($cookie);
    }

    /**
     * Révoquer le consentement
     */
    public function revokeConsent(): JsonResponse
    {
        $cookie = Cookie::forget('cookie_consent');

        return response()->json([
            'success' => true,
            'message' => 'Consentement révoqué'
        ])->withCookie($cookie);
    }
}
