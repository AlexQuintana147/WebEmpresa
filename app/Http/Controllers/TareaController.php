<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class TareaController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        $tareas = Auth::user()->tareas;
        return view('actividades', compact('tareas'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('calendario');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
        ]);
        
        // Validar que las horas estén dentro del rango permitido (8:00 - 18:00)
        $horaInicio = $request->hora_inicio;
        $horaFin = $request->hora_fin;
        
        if (strtotime($horaInicio) < strtotime('08:00') || strtotime($horaFin) > strtotime('18:00')) {
            return redirect()->back()->with('error', 'El horario de citas debe estar entre las 8:00 y las 18:00 horas')->withInput();
        }
        
        // Verificar si hay conflictos de horario
        $diaSemana = $request->dia_semana;
        $tareasExistentes = Auth::user()->tareas
            ->where('dia_semana', $diaSemana)
            ->filter(function($tarea) use ($horaInicio, $horaFin) {
                // Verificar si hay solapamiento de horarios
                return (
                    // Nueva cita comienza durante una existente
                    (strtotime($horaInicio) >= strtotime($tarea->hora_inicio) && 
                     strtotime($horaInicio) < strtotime($tarea->hora_fin)) ||
                    // Nueva cita termina durante una existente
                    (strtotime($horaFin) > strtotime($tarea->hora_inicio) && 
                     strtotime($horaFin) <= strtotime($tarea->hora_fin)) ||
                    // Nueva cita abarca completamente una existente
                    (strtotime($horaInicio) <= strtotime($tarea->hora_inicio) && 
                     strtotime($horaFin) >= strtotime($tarea->hora_fin))
                );
            });
        
        if ($tareasExistentes->count() > 0) {
            return redirect()->back()->with('error', 'Ya existe una cita programada en ese horario')->withInput();
        }

        $tarea = new Tarea($request->all());
        $tarea->usuario_id = Auth::id();
        $tarea->save();

        return redirect()->route('calendario')->with('success', 'Tarea creada correctamente');
    }

    /**
     * Display the specified task.
     */
    public function show(Tarea $tarea)
    {
        // Verificar que la tarea pertenece al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('tarea.show', compact('tarea'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Tarea $tarea)
    {
        // Verificar que la tarea pertenece al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('tarea.edit', compact('tarea'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Tarea $tarea)
    {
        // Verificar que la tarea pertenece al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
        ]);
        
        // Validar que las horas estén dentro del rango permitido (8:00 - 18:00)
        $horaInicio = $request->hora_inicio;
        $horaFin = $request->hora_fin;
        
        if (strtotime($horaInicio) < strtotime('08:00') || strtotime($horaFin) > strtotime('18:00')) {
            return redirect()->back()->with('error', 'El horario de citas debe estar entre las 8:00 y las 18:00 horas')->withInput();
        }
        
        // Verificar si hay conflictos de horario con otras tareas (excluyendo la tarea actual)
        $diaSemana = $request->dia_semana;
        $tareasExistentes = Auth::user()->tareas
            ->where('dia_semana', $diaSemana)
            ->where('id', '!=', $tarea->id) // Excluir la tarea que se está editando
            ->filter(function($t) use ($horaInicio, $horaFin) {
                // Verificar si hay solapamiento de horarios
                return (
                    // Nueva cita comienza durante una existente
                    (strtotime($horaInicio) >= strtotime($t->hora_inicio) && 
                     strtotime($horaInicio) < strtotime($t->hora_fin)) ||
                    // Nueva cita termina durante una existente
                    (strtotime($horaFin) > strtotime($t->hora_inicio) && 
                     strtotime($horaFin) <= strtotime($t->hora_fin)) ||
                    // Nueva cita abarca completamente una existente
                    (strtotime($horaInicio) <= strtotime($t->hora_inicio) && 
                     strtotime($horaFin) >= strtotime($t->hora_fin))
                );
            });
        
        if ($tareasExistentes->count() > 0) {
            return redirect()->back()->with('error', 'Ya existe una cita programada en ese horario')->withInput();
        }

        $tarea->update($request->all());

        return redirect()->route('calendario')->with('success', 'Tarea actualizada correctamente');
    }
    
    /**
     * Get tasks in JSON format for the calendar view.
     */
    public function getTareasJson()
    {
        $tareas = Auth::user()->tareas;
        return response()->json($tareas);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Tarea $tarea)
    {
        // Verificar que la tarea pertenece al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $tarea->delete();

        return redirect()->route('calendario')->with('success', 'Tarea eliminada correctamente');
    }
}