<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Si es una solicitud AJAX o espera JSON, devolver respuesta JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. La sesión ha expirado.',
                    'session_expired' => true
                ], 401);
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}