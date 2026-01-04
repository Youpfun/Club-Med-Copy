<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsMarketingDirector
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect('/login');
        }

        // On récupère le rôle en minuscule pour comparer facilement
        $role = strtolower(Auth::user()->role);

        // La condition : le rôle doit contenir le mot "marketing"
        // Cela autorise "Directeur Marketing", "Membre du service Marketing", etc.
        if (strpos($role, 'marketing') === false) {
            abort(403, "Accès refusé. Vous devez être membre du service Marketing.");
        }

        return $next($request);
    }
}