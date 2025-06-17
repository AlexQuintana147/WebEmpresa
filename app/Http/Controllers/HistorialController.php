<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Muestra el formulario para crear un nuevo registro de historial médico
     */
    public function create($pacienteId, $citaId = null)
    {
        // Verificar que el usuario sea un doctor
        if (Auth::user()->rol_id != 3) {
            return redirect()->route('pacientes.index')
                ->with('error', 'No tiene permisos para realizar esta acción');
        }
        
        // Buscar el paciente
        $paciente = Paciente::findOrFail($pacienteId);
        
        // Buscar la cita si se proporciona un ID
        $cita = null;
        if ($citaId) {
            $cita = Cita::findOrFail($citaId);
        }
        
        return view('historial.create', [
            'paciente' => $paciente,
            'cita' => $cita
        ]);
    }

    /**
     * Almacena un nuevo registro de historial médico
     */
    public function store(Request $request)
    {
        // Verificar que el usuario sea un doctor
        if (Auth::user()->rol_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para realizar esta acción'
            ], 403);
        }
        
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'cita_id' => 'nullable|exists:citas,id',
            'fecha' => 'required|date',
            'motivo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'diagnostico' => 'required|string',
            'tratamiento' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Crear el nuevo registro de historial
        $historial = Historial::create([
            'paciente_id' => $request->paciente_id,
            'cita_id' => $request->cita_id,
            'fecha' => $request->fecha,
            'motivo' => $request->motivo,
            'descripcion' => $request->descripcion,
            'diagnostico' => $request->diagnostico,
            'tratamiento' => $request->tratamiento
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Historial médico registrado correctamente',
            'historial' => $historial
        ]);
    }
}