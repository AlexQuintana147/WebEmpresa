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
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
        ]);

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
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
        ]);

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