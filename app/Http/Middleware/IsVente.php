<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsVente
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (strpos(strtolower(Auth::user()->role ?? ''), 'vente') === false) {
            abort(403, 'Accès réservé au service vente');
        }

        return $next($request);
    }
}
