<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
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
        
        // Si el doctor está registrado, obtener sus pacientes
        if ($doctor) {
            $pacientes = Paciente::where('doctor_id', $doctor->id)->get();
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
        $doctor = Doctor::where('usuario_id', Auth::id())->first();
        
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró información del doctor'
            ], 404);
        }
        
        $pacientes = Paciente::where('doctor_id', $doctor->id)->get();
        
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
}