<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckGestionRole
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
        if (!Auth::check() || Auth::user()->rol_id != 4) {
            // Si el usuario no está autenticado o no es de gestión (rol_id = 4)
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección. Área exclusiva para personal de gestión.');
        }

        return $next($request);
    }
}