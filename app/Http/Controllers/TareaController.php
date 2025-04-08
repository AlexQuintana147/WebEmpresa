<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    /**
     * Muestra la vista del calendario con las tareas del usuario autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tareas = Tarea::where('usuario_id', Auth::id())->get();
        return view('calendario', compact('tareas'));
    }

    /**
     * Almacena una nueva tarea en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'color' => 'required|string|max:255',
            'icono' => 'required|string|max:255',
        ]);

        $tarea = new Tarea();
        $tarea->usuario_id = Auth::id();
        $tarea->titulo = $request->titulo;
        $tarea->descripcion = $request->descripcion;
        $tarea->dia_semana = $request->dia_semana;
        $tarea->hora_inicio = $request->hora_inicio;
        $tarea->hora_fin = $request->hora_fin;
        $tarea->color = $request->color;
        $tarea->icono = $request->icono;
        $tarea->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'tarea' => $tarea
        ]);
    }

    /**
     * Muestra una tarea específica.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json(['tarea' => $tarea]);
    }

    /**
     * Muestra el formulario para editar una tarea.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json(['tarea' => $tarea]);
    }

    /**
     * Actualiza una tarea específica en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'color' => 'required|string|max:255',
            'icono' => 'required|string|max:255',
        ]);

        $tarea->titulo = $request->titulo;
        $tarea->descripcion = $request->descripcion;
        $tarea->dia_semana = $request->dia_semana;
        $tarea->hora_inicio = $request->hora_inicio;
        $tarea->hora_fin = $request->hora_fin;
        $tarea->color = $request->color;
        $tarea->icono = $request->icono;
        $tarea->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada correctamente',
            'tarea' => $tarea
        ]);
    }

    /**
     * Elimina una tarea específica de la base de datos.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $tarea->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada correctamente'
        ]);
    }

    /**
     * Obtiene todas las tareas del usuario autenticado en formato JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTareasJson()
    {
        $tareas = Tarea::where('usuario_id', Auth::id())->get();
        return response()->json(['tareas' => $tareas]);
    }
}