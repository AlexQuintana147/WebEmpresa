<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDoctorRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->rol_id != 3) {
            // Si el usuario no está autenticado o no es un doctor (rol_id = 3)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado. La sesión ha expirado o no tienes permisos suficientes.',
                    'session_expired' => true
                ], 401);
            }
            
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección. Área exclusiva para médicos.');
        }

        return $next($request);
    }
}