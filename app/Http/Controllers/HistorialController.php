<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class HistorialController extends Controller
{
    /**
     * Muestra la vista de historial médico del paciente autenticado
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();
        
        // Buscar el paciente asociado al usuario
        $paciente = Paciente::where('usuario_id', $usuario->id)->first();
        
        // Si no existe un paciente asociado al usuario
        if (!$paciente) {
            return view('historial', ['paciente' => null, 'historiales' => null]);
        }
        
        // Obtener los historiales médicos del paciente
        $historiales = Historial::where('paciente_id', $paciente->id)
                              ->orderBy('fecha', 'desc')
                              ->get();
        
        return view('historial', [
            'paciente' => $paciente,
            'historiales' => $historiales
        ]);
    }
    
    /**
     * Muestra el historial médico de un paciente específico (para doctores)
     */
    public function show($pacienteId)
    {
        // Verificar que el usuario sea un doctor
        if (Auth::user()->rol_id != 3) {
            return redirect()->route('historial.index')
                ->with('error', 'No tiene permisos para ver esta información');
        }
        
        // Buscar el paciente
        $paciente = Paciente::findOrFail($pacienteId);
        
        // Obtener los historiales médicos del paciente
        $historiales = Historial::where('paciente_id', $paciente->id)
                              ->orderBy('fecha', 'desc')
                              ->get();
        
        return view('historial', [
            'paciente' => $paciente,
            'historiales' => $historiales
        ]);
    }
}