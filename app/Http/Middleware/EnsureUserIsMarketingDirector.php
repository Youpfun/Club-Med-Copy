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
        if (!Auth::check()) {
            return redirect('/login');
        }

        $role = strtolower(Auth::user()->role);
        if (strpos($role, 'marketing') === false) {
            abort(403, "Accès refusé. Vous devez être membre du service Marketing.");
        }

        return $next($request);
    }
}