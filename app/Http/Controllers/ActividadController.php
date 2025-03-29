<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class ActividadController extends Controller
{
    /**
     * Display a listing of the activities.
     */
    public function index()
    {
        // Si el usuario está autenticado, obtener sus actividades
        if (Auth::check()) {
            $actividades = Actividad::where('usuario_id', Auth::id())->where('nivel', 'principal')->get();
            return view('lista-de-actividades', compact('actividades'));
        }
        
        // Si no está autenticado, mostrar la vista con datos de ejemplo
        return view('lista-de-actividades');
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'nivel' => 'required|in:principal,secundaria,terciaria',
                'estado' => 'required|in:pendiente,en_progreso,completada',
                'fecha_limite' => 'nullable|date',
                'hora_limite' => 'nullable',
                'color' => 'required|string',
                'icono' => 'required|string',
                'prioridad' => 'required|integer|min:1|max:3',
                'actividad_padre_id' => 'nullable|exists:actividades,id',
            ]);

            $actividad = new Actividad($request->all());
            $actividad->usuario_id = Auth::id();
            $actividad->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Actividad creada correctamente']);
            }

            return redirect()->route('actividades.index')->with('success', 'Actividad creada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al crear la actividad: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified activity.
     */
    public function show(Actividad $actividad)
    {
        // Verificar que la actividad pertenece al usuario autenticado
        if ($actividad->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        return view('actividades.show', compact('actividad'));
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(Request $request, Actividad $actividad)
    {
        // Verificar que la actividad pertenece al usuario autenticado
        if ($actividad->usuario_id !== Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No autorizado para editar esta actividad'], 403);
            }
            abort(403, 'No autorizado');
        }

        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'nivel' => 'required|in:principal,secundaria,terciaria',
                'estado' => 'required|in:pendiente,en_progreso,completada',
                'fecha_limite' => 'nullable|date',
                'hora_limite' => 'nullable',
                'color' => 'required|string',
                'icono' => 'required|string',
                'prioridad' => 'required|integer|min:1|max:3',
                'actividad_padre_id' => 'nullable|exists:actividades,id',
            ]);

            $actividad->update($request->all());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Actividad actualizada correctamente']);
            }

            return redirect()->route('actividades.index')->with('success', 'Actividad actualizada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar la actividad: ' . $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(Actividad $actividad)
    {
        // Verificar que la actividad pertenece al usuario autenticado
        if ($actividad->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $actividad->delete();

        return redirect()->route('actividades.index')->with('success', 'Actividad eliminada correctamente');
    }
    
    /**
     * Change the status of an activity.
     */
    public function cambiarEstado(Request $request, Actividad $actividad)
    {
        // Verificar que la actividad pertenece al usuario autenticado
        if ($actividad->usuario_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }
        
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada',
        ]);
        
        $actividad->estado = $request->estado;
        $actividad->save();
        
        return redirect()->route('actividades.index')->with('success', 'Estado de actividad actualizado correctamente');
    }
}