<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;


class TareaController extends Controller
{
    public function index()
    {
        // Si el usuario está autenticado, obtener sus tareas
        if (Auth::check()) {
            $usuario_id = Auth::id();
            $tareas = Tarea::where('usuario_id', $usuario_id)->get();
            return view('tareas', compact('tareas'));
        }
        
        // Si no está autenticado, mostrar la vista sin datos
        return view('tareas');
    }

    /**
     * Obtener todas las tareas del usuario autenticado en formato JSON
     */
    public function getTareasJson()
    {
        $usuario_id = Auth::id();
        $tareas = Tarea::where('usuario_id', $usuario_id)->get();
        
        return response()->json($tareas, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Almacenar una nueva tarea
     */
    public function store(Request $request)
    {
        // Forzar que todas las respuestas sean JSON
        $request->headers->set('Accept', 'application/json');
        
        // Validar manualmente para poder capturar errores y devolverlos como JSON
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'color' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422, ['Content-Type' => 'application/json']);
        }

        try {
            $tarea = new Tarea();
            $tarea->usuario_id = Auth::id();
            $tarea->titulo = $request->titulo;
            $tarea->descripcion = $request->descripcion;
            $tarea->dia_semana = $request->dia_semana;
            $tarea->hora_inicio = $request->hora_inicio;
            $tarea->hora_fin = $request->hora_fin;
            $tarea->color = $request->color ?? '#4A90E2';
            $tarea->icono = $request->icono ?? 'fa-user-doctor';
            $tarea->save();

            // Asegurar que siempre se devuelva JSON para peticiones AJAX
            return response()->json([
                'success' => true,
                'message' => 'Horario creado correctamente',
                'tarea' => $tarea
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el horario: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Mostrar una tarea específica
     */
    public function show(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para ver este horario'
            ], 403, ['Content-Type' => 'application/json']);
        }

        return response()->json([
            'success' => true,
            'tarea' => $tarea
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Mostrar el formulario para editar una tarea
     */
    public function edit(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para editar este horario'
            ], 403, ['Content-Type' => 'application/json']);
        }

        return response()->json([
            'success' => true,
            'tarea' => $tarea
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Actualizar una tarea específica
     */
    public function update(Request $request, Tarea $tarea)
    {
        // Forzar que todas las respuestas sean JSON
        $request->headers->set('Accept', 'application/json');
        
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para actualizar este horario'
            ], 403, ['Content-Type' => 'application/json']);
        }

        try {
            // Validar manualmente para poder capturar errores y devolverlos como JSON
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'dia_semana' => 'required|integer|min:1|max:7',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'color' => 'nullable|string|max:255',
                'icono' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422, ['Content-Type' => 'application/json']);
            }
            
            $tarea->titulo = $request->titulo;
            $tarea->descripcion = $request->descripcion;
            $tarea->dia_semana = $request->dia_semana;
            $tarea->hora_inicio = $request->hora_inicio;
            $tarea->hora_fin = $request->hora_fin;
            $tarea->color = $request->color ?? '#4A90E2';
            $tarea->icono = $request->icono ?? 'fa-user-doctor';
            $tarea->save();

            return response()->json([
                'success' => true,
                'message' => 'Horario actualizado correctamente',
                'tarea' => $tarea
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el horario: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Eliminar una tarea específica
     */
    public function destroy(Tarea $tarea)
    {
        // Verificar que la tarea pertenezca al usuario autenticado
        if ($tarea->usuario_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para eliminar este horario'
            ], 403, ['Content-Type' => 'application/json']);
        }

        try {
            $tarea->delete();

            return response()->json([
                'success' => true,
                'message' => 'Horario eliminado correctamente'
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el horario: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }
}