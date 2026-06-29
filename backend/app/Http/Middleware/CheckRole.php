<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next , ...$roles): Response
      {
        // 1. Vérifie si l'utilisateur est connecté
        if (!$request->user()) {
            return response()->json(['message' => 'Accès interdit : Non authentifié.'], 401);
        }

        // 2. Vérifie si l'utilisateur a l'un des rôles autorisés
        if (!in_array($request->user()->role, $roles)) {
            return response()->json(['message' => 'Accès interdit : Rôle insuffisant.'], 403);
        }

        return $next($request);
    }
     
}
