{{-- Bandeau de consentement cookies RGPD --}}
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[100] transform translate-y-full transition-transform duration-500 ease-out">
    <div class="bg-white border-t border-gray-200 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 py-6">
            {{-- Version compacte --}}
            <div id="cookie-banner-compact" class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-clubmed-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="font-bold text-gray-900">Nous respectons votre vie privée</h3>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Nous utilisons des cookies pour améliorer votre expérience, analyser le trafic et personnaliser le contenu. 
                        <button onclick="toggleCookieDetails()" class="text-clubmed-blue hover:underline font-medium">En savoir plus</button>
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button onclick="rejectAllCookies()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                        Refuser
                    </button>
                    <button onclick="toggleCookieDetails()" class="px-5 py-2.5 text-sm font-medium text-clubmed-blue border border-clubmed-blue hover:bg-clubmed-blue hover:text-white rounded-full transition-colors">
                        Personnaliser
                    </button>
                    <button onclick="acceptAllCookies()" class="px-5 py-2.5 text-sm font-medium text-white bg-clubmed-blue hover:bg-clubmed-blue-dark rounded-full transition-colors shadow-md">
                        Tout accepter
                    </button>
                </div>
            </div>

            {{-- Version détaillée --}}
            <div id="cookie-banner-details" class="hidden mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-bold text-gray-900 mb-4">Gérer vos préférences de cookies</h4>
                
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    {{-- Cookies essentiels --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">Cookies essentiels</span>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">Toujours actifs</span>
                        </div>
                        <p class="text-sm text-gray-600">Nécessaires au fonctionnement du site. Ils permettent la navigation et l'accès aux fonctionnalités de base.</p>
                    </div>

                    {{-- Cookies fonctionnels --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">Cookies fonctionnels</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="cookie-functional" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:ring-2 peer-focus:ring-clubmed-gold rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-clubmed-gold"></div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600">Améliorent les fonctionnalités comme les préférences linguistiques et la mémorisation de vos choix.</p>
                    </div>

                    {{-- Cookies analytics --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">Cookies analytiques</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="cookie-analytics" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-300 peer-focus:ring-2 peer-focus:ring-clubmed-gold rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-clubmed-gold"></div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600">Nous aident à comprendre comment les visiteurs utilisent le site pour l'améliorer.</p>
                    </div>

                    {{-- Cookies marketing --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-gray-900">Cookies marketing</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="cookie-marketing" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:ring-2 peer-focus:ring-clubmed-gold rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-clubmed-gold"></div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600">Utilisés pour vous proposer des publicités pertinentes en fonction de vos centres d'intérêt.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button onclick="toggleCookieDetails()" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                        Annuler
                    </button>
                    <button onclick="saveCustomCookies()" class="px-6 py-2.5 text-sm font-medium text-white bg-clubmed-blue hover:bg-clubmed-blue-dark rounded-full transition-colors shadow-md">
                        Enregistrer mes choix
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bouton flottant pour réouvrir les paramètres cookies --}}
<button id="cookie-settings-btn" onclick="showCookieBanner()" class="fixed bottom-4 left-4 z-50 w-12 h-12 bg-white rounded-full shadow-lg border border-gray-200 items-center justify-center hover:shadow-xl transition-shadow hidden" title="Paramètres des cookies">
    <svg class="w-6 h-6 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
</button>

<script>
    // Vérifier le consentement au chargement
    document.addEventListener('DOMContentLoaded', function() {
        checkCookieConsent();
    });

    function checkCookieConsent() {
        fetch('/api/cookies/consent', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.hasConsent) {
                showCookieBanner();
            } else {
                document.getElementById('cookie-settings-btn').classList.remove('hidden');
                document.getElementById('cookie-settings-btn').classList.add('flex');
                // Appliquer les préférences
                applyCookiePreferences(data.preferences);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la vérification du consentement:', error);
            showCookieBanner();
        });
    }

    function showCookieBanner() {
        const banner = document.getElementById('cookie-banner');
        const settingsBtn = document.getElementById('cookie-settings-btn');
        
        banner.classList.remove('translate-y-full');
        settingsBtn.classList.add('hidden');
        settingsBtn.classList.remove('flex');
    }

    function hideCookieBanner() {
        const banner = document.getElementById('cookie-banner');
        const settingsBtn = document.getElementById('cookie-settings-btn');
        
        banner.classList.add('translate-y-full');
        settingsBtn.classList.remove('hidden');
        settingsBtn.classList.add('flex');
    }

    function toggleCookieDetails() {
        const compact = document.getElementById('cookie-banner-compact');
        const details = document.getElementById('cookie-banner-details');
        
        details.classList.toggle('hidden');
    }

    function acceptAllCookies() {
        fetch('/api/cookies/accept-all', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideCookieBanner();
                applyCookiePreferences(data.preferences);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function rejectAllCookies() {
        fetch('/api/cookies/reject-all', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideCookieBanner();
                applyCookiePreferences(data.preferences);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function saveCustomCookies() {
        const preferences = {
            essential: true,
            functional: document.getElementById('cookie-functional').checked,
            analytics: document.getElementById('cookie-analytics').checked,
            marketing: document.getElementById('cookie-marketing').checked
        };

        fetch('/api/cookies/consent', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(preferences)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                hideCookieBanner();
                applyCookiePreferences(data.preferences);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }

    function applyCookiePreferences(preferences) {
        // Ici vous pouvez activer/désactiver les scripts tiers selon les préférences
        if (preferences.analytics) {
            // Activer Google Analytics, etc.
            console.log('Analytics cookies activés');
        }
        if (preferences.marketing) {
            // Activer les cookies marketing
            console.log('Marketing cookies activés');
        }
        if (preferences.functional) {
            // Activer les cookies fonctionnels
            console.log('Functional cookies activés');
        }
    }
</script>
