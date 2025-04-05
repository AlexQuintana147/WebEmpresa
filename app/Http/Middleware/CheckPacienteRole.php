<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPacienteRole
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
        if (!Auth::check() || Auth::user()->rol_id != 2) {
            // Si el usuario no está autenticado o no es un paciente (rol_id = 2)
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección. Área exclusiva para pacientes.');
        }

        return $next($request);
    }
}