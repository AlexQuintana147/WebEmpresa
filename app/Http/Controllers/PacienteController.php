<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class PacienteController extends Controller
{
    /**
     * Muestra la vista de atención médica
     */
    public function index()
    {
        // Si el usuario está autenticado, verificamos si ya tiene un paciente asociado
        if (Auth::check()) {
            $paciente = Paciente::where('usuario_id', Auth::id())->first();
            return view('atencionmedica', compact('paciente'));
        }
        
        return view('atencionmedica');
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
        
        $paciente = Paciente::where('dni', $request->dni)->first();
        
        if ($paciente) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'paciente' => $paciente
            ]);
        }
        
        return response()->json([
            'success' => true,
            'exists' => false
        ]);
    }
    
    /**
     * Registra un nuevo paciente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|unique:pacientes,dni',
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'correo' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:15',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $paciente = Paciente::create([
            'dni' => $request->dni,
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'usuario_id' => Auth::id() // Si el usuario está autenticado, asociamos el paciente
        ]);
        
        return redirect()->route('atencionmedica.index')
            ->with('success', 'Paciente registrado correctamente');
    }
    
    /**
     * Asocia un paciente existente con el usuario autenticado
     */
    public function asociarUsuario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|string|size:8|exists:pacientes,dni',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $paciente = Paciente::where('dni', $request->dni)->first();
        
        // Si el paciente ya está asociado a otro usuario, mostramos un error
        if ($paciente->usuario_id && $paciente->usuario_id != Auth::id()) {
            return redirect()->back()
                ->with('error', 'Este DNI ya está asociado a otra cuenta');
        }
        
        // Asociamos el paciente al usuario actual
        $paciente->usuario_id = Auth::id();
        $paciente->save();
        
        return redirect()->route('atencionmedica.index')
            ->with('success', 'DNI asociado correctamente a su cuenta');
    }

    /**
     * Muestra la información del doctor asignado al paciente autenticado
     */
    public function verDoctorAsignado()
    {
        // Obtener el paciente autenticado
        $paciente = Paciente::where('usuario_id', Auth::id())->first();
        
        if (!$paciente) {
            return redirect()->route('atencionmedica.index')
                ->with('error', 'No se encontró información del paciente');
        }
        
        // Verificar si el paciente tiene un doctor asignado
        if (!$paciente->doctor_id) {
            return redirect()->route('atencionmedica.index')
                ->with('info', 'Aún no tienes un doctor asignado para atención directa');
        }
        
        // Obtener el doctor asignado con sus datos
        $doctor = $paciente->doctor;
        
        // Pasar solo el paciente actual a la vista (no una colección de pacientes)
        $pacientes = collect([$paciente]);
        
        return view('atenciondirecta', compact('doctor', 'pacientes'));
    }
}