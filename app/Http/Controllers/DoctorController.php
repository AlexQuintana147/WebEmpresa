<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class DoctorController extends Controller
{
    /**
     * Muestra la vista de pacientes para doctores
     */
    public function index()
    {
        // Verificar si el doctor ya tiene sus datos registrados
        $doctor = Doctor::where('usuario_id', Auth::id())->first();
        $pacientes = [];
        
        // Si el doctor está registrado, obtener sus pacientes mediante la tabla de citas
        if ($doctor) {
            // Obtener los IDs únicos de pacientes que tienen citas con este doctor
            // Modificado para incluir solo citas pendientes o en espera
            $pacienteIds = \App\Models\Cita::where('doctor_id', $doctor->id)
                ->whereIn('estado', ['pendiente', 'En Espera'])
                ->pluck('paciente_id')
                ->unique();

            // Obtener los pacientes y sus citas con este doctor
            $pacientes = \App\Models\Paciente::whereIn('id', $pacienteIds)
                ->with(['citas' => function($query) use ($doctor) {
                    $query->where('doctor_id', $doctor->id);
                }])
                ->get();

            return view('pacientes', compact('doctor', 'pacientes'));
        }
        
        // Si el doctor no está registrado, mostrar formulario para ingresar DNI
        return view('pacientes', compact('doctor', 'pacientes'));
    }
    
    /**
     * Verifica si un DNI existe en el sistema
     */
    public function verificarDni(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El DNI debe tener 8 dígitos',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $doctor = Doctor::where('dni', $request->dni)->first();
        
        if ($doctor) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'doctor' => $doctor
            ]);
        }
        
        return response()->json([
            'success' => true,
            'exists' => false
        ]);
    }
    
    /**
     * Guarda el DNI del doctor después de verificarlo con RENIEC
     */
    public function guardarDni(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8',
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verificar si ya existe un doctor con este DNI
        $existingDoctor = Doctor::where('dni', $request->dni)->first();
        if ($existingDoctor && $existingDoctor->usuario_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un doctor registrado con este DNI'
            ], 422);
        }
        
        // Crear o actualizar el doctor
        $doctor = Doctor::updateOrCreate(
            ['usuario_id' => Auth::id()],
            [
                'dni' => $request->dni,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'correo' => $request->correo ?? null,
                'telefono' => $request->telefono ?? null,
                'especialidad' => $request->especialidad ?? null
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'DNI registrado correctamente',
            'doctor' => $doctor
        ]);
    }
    
    /**
     * Registra un nuevo doctor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|unique:doctores,dni',
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:15',
            'especialidad' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $doctor = Doctor::create([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'especialidad' => $request->especialidad,
            'usuario_id' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Doctor registrado correctamente',
            'doctor' => $doctor
        ]);
    }
    
    /**
     * Obtiene los pacientes asociados al doctor autenticado
     */
    public function getPacientes()
    {
        // Buscar el doctor por usuario_id
        $doctor = \App\Models\Doctor::where('usuario_id', Auth::id())->first();

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del doctor autenticado'
            ], 404);
        }

        // Buscar pacientes asociados a este doctor
        $pacientes = \App\Models\Paciente::where('doctor_id', $doctor->id)->get();

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes
        ]);
    }
    
    /**
     * Asocia un paciente al doctor autenticado
     */
    public function asociarPaciente(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $doctor = Doctor::where('usuario_id', Auth::id())->first();
        
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del doctor'
            ], 404);
        }
        
        $paciente = Paciente::find($request->paciente_id);
        $paciente->doctor_id = $doctor->id;
        $paciente->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Paciente asociado correctamente',
            'paciente' => $paciente
        ]);
    }
    
    /**
     * Asigna el doctor actual al paciente especificado
     */
    public function asignarDoctor($pacienteId)
    {
        try {
            // Obtener el doctor autenticado
            $doctor = Doctor::where('usuario_id', Auth::id())->first();
            
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró información del doctor'
                ], 404);
            }
            
            // Buscar el paciente
            $paciente = Paciente::findOrFail($pacienteId);
            
            // Verificar si el paciente ya tiene un doctor asignado
            if ($paciente->doctor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene un doctor asignado'
                ], 400);
            }
            
            // Asignar el doctor al paciente
            $paciente->doctor_id = $doctor->id;
            $paciente->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Se ha asignado el doctor al paciente correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar doctor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Muestra la vista de atención directa con los pacientes asignados al doctor
     */
    public function atencionDirecta()
    {
        // Obtener el doctor autenticado
        $doctor = Doctor::where('usuario_id', Auth::id())->first();
        
        if (!$doctor) {
            return redirect()->route('pacientes.index')
                ->with('error', 'No se encontró información del doctor');
        }
        
        // Obtener los pacientes asignados a este doctor
        $pacientes = Paciente::where('doctor_id', $doctor->id)->get();
        
        return view('atenciondirecta', compact('doctor', 'pacientes'));
    }
    
    /**
     * Devuelve el horario semanal del doctor (sus tareas)
     */
    public function horario($doctor_id)
    {
        // Obtener el usuario_id del doctor
        $doctor = \App\Models\Doctor::findOrFail($doctor_id);
        $usuario_id = $doctor->usuario_id;
        // Obtener tareas (horarios) de la semana, ordenadas por día y hora
        $tareas = \App\Models\Tarea::where('usuario_id', $usuario_id)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get(['dia_semana', 'hora_inicio', 'hora_fin', 'titulo']);
        return response()->json([
            'success' => true,
            'horario' => $tareas
        ]);
    }
}