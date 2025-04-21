<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Tarea;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    /**
     * Muestra la vista de citas
     */
    public function index()
    {
        $paciente = null;
        $citas = [];
        
        // Obtener todas las especialidades únicas de los doctores
        $especialidades = Doctor::select('especialidad')->distinct()->orderBy('especialidad')->pluck('especialidad');
        
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            $usuario = Auth::user();
            
            // Buscar si el usuario tiene un paciente asociado
            $paciente = Paciente::where('usuario_id', $usuario->id)->first();
            
            if ($paciente) {
                // Obtener citas del paciente
                $citas = Cita::with('doctor')
                    ->where('paciente_id', $paciente->id)
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora_inicio', 'desc')
                    ->get();
            }
        }
        
        return view('citas', compact('paciente', 'citas', 'especialidades'));
    }
    
    /**
     * Verifica el DNI del paciente
     */
    public function verificarDni(Request $request)
    {
        $request->validate([
            'dni' => 'required|string|size:8'
        ]);
        
        $dni = $request->dni;
        $paciente = Paciente::where('dni', $dni)->first();
        
        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un paciente con ese DNI.'
            ]);
        }
        
        // Obtener citas del paciente
        $citas = Cita::with('doctor')
            ->where('paciente_id', $paciente->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'paciente' => $paciente,
            'citas' => $citas
        ]);
    }
    /**
     * Almacena una nueva cita en la base de datos
     */
    /**
     * Obtiene los días disponibles para un doctor específico
     */
    public function getDiasDisponibles($doctor_id)
    {
        try {
            // Obtener las tareas del doctor
            $tareas = Tarea::where('usuario_id', $doctor_id)
                ->orderBy('dia_semana')
                ->get();

            if ($tareas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay horarios disponibles para este doctor'
                ]);
            }

            // Obtener la fecha actual
            $fechaActual = now();
            $diasDisponibles = [];

            // Generar fechas para los próximos 30 días
            for ($i = 0; $i < 30; $i++) {
                $fecha = $fechaActual->copy()->addDays($i);
                $diaSemana = $fecha->dayOfWeek ?: 7; // Convertir 0 (domingo) a 7

                // Verificar si hay tareas para este día de la semana
                $tareasDelDia = $tareas->where('dia_semana', $diaSemana);

                if ($tareasDelDia->isNotEmpty()) {
                    $diasDisponibles[] = [
                        'fecha' => $fecha->format('Y-m-d'),
                        'dia_semana' => $diaSemana,
                        'horarios' => $tareasDelDia->map(function ($tarea) {
                            return [
                                'hora_inicio' => $tarea->hora_inicio,
                                'hora_fin' => $tarea->hora_fin
                            ];
                        })
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'dias' => $diasDisponibles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los días disponibles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctores,id',
            'fecha' => 'required|date|after:today',
            'hora' => 'required',
            'motivo' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $paciente = Paciente::where('usuario_id', Auth::id())->firstOrFail();

            $cita = new Cita();
            $cita->paciente_id = $paciente->id;
            $cita->doctor_id = $request->doctor_id;
            $cita->fecha = $request->fecha;
            $cita->hora_inicio = $request->hora;
            $cita->motivo = $request->motivo;
            $cita->estado = 'pendiente';
            $cita->save();

            DB::commit();

            return redirect()->route('citas.index')
                ->with('success', 'Cita agendada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al agendar la cita. Por favor, inténtelo de nuevo.')
                ->withInput();
        }
    }

    public function getDoctoresPorEspecialidad($especialidad)
    {
        $doctores = Doctor::where('especialidad', $especialidad)
            ->orderBy('nombre')
            ->get();
        
        // Enriquecer la información de cada doctor con datos adicionales
        foreach ($doctores as $doctor) {
            // Obtener el número de tareas (horarios disponibles) para este doctor
            $numTareas = Tarea::where('usuario_id', $doctor->usuario_id)->count();
            $doctor->num_horarios_disponibles = $numTareas;
            
            // Obtener los días de la semana en que atiende
            $diasAtencion = Tarea::where('usuario_id', $doctor->usuario_id)
                ->select('dia_semana')
                ->distinct()
                ->get()
                ->pluck('dia_semana')
                ->toArray();
            
            // Convertir números a nombres de días
            $nombresDias = [];
            $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            foreach ($diasAtencion as $dia) {
                // Ajustar el índice (en la BD: 1=Lunes, ..., 7=Domingo)
                $indice = $dia % 7; // Convertir 7 (domingo en BD) a 0 (domingo en array)
                $nombresDias[] = $diasSemana[$indice];
            }
            
            $doctor->dias_atencion = $nombresDias;
        }
        
        return response()->json([
            'success' => true,
            'doctores' => $doctores
        ]);
    }
    
    /**
     * Obtiene los horarios disponibles para un doctor en una fecha específica
     */
    public function getHorariosDisponibles($doctorId, $fecha)
    {
        try {
            $doctor = Doctor::findOrFail($doctorId);
            
            // Obtener el día de la semana (1: lunes, ..., 7: domingo)
            $diaSemana = date('N', strtotime($fecha));
            
            // Obtener tareas del doctor para ese día de la semana
            $tareas = Tarea::where('usuario_id', $doctor->usuario_id)
                ->where('dia_semana', $diaSemana)
                ->orderBy('hora_inicio')
                ->get();
            
            if ($tareas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El doctor no tiene horarios disponibles para este día.'
                ]);
            }
            
            // Obtener citas ya agendadas para ese doctor en esa fecha
            $citasAgendadas = Cita::where('doctor_id', $doctorId)
                ->where('fecha', $fecha)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->get();
            
            $horariosDisponibles = [];
            
            foreach ($tareas as $tarea) {
                // Verificar si el horario ya está ocupado por una cita
                $ocupado = $citasAgendadas->contains(function ($cita) use ($tarea) {
                    return (
                        ($tarea->hora_inicio >= $cita->hora_inicio && $tarea->hora_inicio < $cita->hora_fin) ||
                        ($tarea->hora_fin > $cita->hora_inicio && $tarea->hora_fin <= $cita->hora_fin) ||
                        ($tarea->hora_inicio <= $cita->hora_inicio && $tarea->hora_fin >= $cita->hora_fin)
                    );
                });
                
                if (!$ocupado) {
                    $horariosDisponibles[] = [
                        'hora_inicio' => $tarea->hora_inicio,
                        'hora_fin' => $tarea->hora_fin
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'horarios' => $horariosDisponibles
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los horarios disponibles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function agendarCita(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'doctor_id' => 'required|exists:doctores,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'motivo_consulta' => 'required|string',
            'descripcion_malestar' => 'nullable|string'
        ]);
        
        // Verificar disponibilidad del horario
        $citasExistentes = Cita::where('doctor_id', $request->doctor_id)
            ->where('fecha', $request->fecha)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->where(function($query) use ($request) {
                $query->whereBetween('hora_inicio', [$request->hora_inicio, $request->hora_fin])
                    ->orWhereBetween('hora_fin', [$request->hora_inicio, $request->hora_fin])
                    ->orWhere(function($q) use ($request) {
                        $q->where('hora_inicio', '<=', $request->hora_inicio)
                            ->where('hora_fin', '>=', $request->hora_fin);
                    });
            })
            ->exists();
        
        if ($citasExistentes) {
            return response()->json([
                'success' => false,
                'message' => 'El horario seleccionado ya no está disponible. Por favor seleccione otro horario.'
            ]);
        }
        
        // Crear nueva cita
        $cita = new Cita();
        $cita->paciente_id = $request->paciente_id;
        $cita->doctor_id = $request->doctor_id;
        $cita->fecha = $request->fecha;
        $cita->hora_inicio = $request->hora_inicio;
        $cita->hora_fin = $request->hora_fin;
        $cita->motivo_consulta = $request->motivo_consulta;
        $cita->descripcion_malestar = $request->descripcion_malestar;
        $cita->estado = 'pendiente';
        $cita->save();
        
        // Cargar relaciones para la respuesta
        $cita->load('doctor', 'paciente');
        
        return response()->json([
            'success' => true,
            'message' => 'Cita agendada correctamente.',
            'cita' => $cita
        ]);
    }
    
    /**
     * Cancela una cita
     */
    public function cancelarCita($id)
    {
        $cita = Cita::findOrFail($id);
        
        // Verificar que la cita esté pendiente
        if ($cita->estado !== 'pendiente') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden cancelar citas pendientes.'
            ]);
        }
        
        // Actualizar estado
        $cita->estado = 'cancelada';
        $cita->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Cita cancelada correctamente.'
        ]);
    }

}
    
