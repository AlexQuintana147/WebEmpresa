<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class NotaController extends Controller
{
    /**
     * Display a listing of the user's notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // If user is authenticated, get their notes
        if (Auth::check()) {
            $notas = Nota::where('usuario_id', Auth::id())->get();
            return view('notas', ['notas' => $notas]);
        }
        
        // If not authenticated, just return the view (which will show example notes)
        return view('notas');
    }

    /**
     * Store a newly created note in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'categoria' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'isPinned' => 'boolean',
        ]);

        $nota = new Nota([
            'usuario_id' => Auth::id(),
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'categoria' => $request->categoria,
            'color' => $request->color,
            'isPinned' => $request->isPinned ?? false,
            'isArchived' => false,
        ]);

        $nota->save();

        // Check if the request expects a JSON response
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Nota creada correctamente']);
        }
        
        return redirect()->route('notas.index')->with('success', 'Nota creada correctamente');
    }
    
    /**
     * Update the specified note in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Nota $nota)
    {
        // Check if the note belongs to the authenticated user
        if ($nota->usuario_id !== Auth::id()) {
            return redirect()->route('notas.index')->with('error', 'No tienes permiso para editar esta nota');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'categoria' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'isPinned' => 'boolean',
            'isArchived' => 'boolean',
        ]);

        $nota->update([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'categoria' => $request->categoria,
            'color' => $request->color,
            'isPinned' => $request->isPinned ?? false,
            'isArchived' => $request->isArchived ?? false,
        ]);

        return redirect()->route('notas.index')->with('success', 'Nota actualizada correctamente');
    }

    /**
     * Remove the specified note from storage.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nota $nota)
    {
        // Check if the note belongs to the authenticated user
        if ($nota->usuario_id !== Auth::id()) {
            return redirect()->route('notas.index')->with('error', 'No tienes permiso para eliminar esta nota');
        }

        $nota->delete();

        return redirect()->route('notas.index')->with('success', 'Nota eliminada correctamente');
    }

    /**
     * Toggle the pinned status of a note.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function togglePin(Nota $nota)
    {
        // Check if the note belongs to the authenticated user
        if ($nota->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $nota->update(['isPinned' => !$nota->isPinned]);

        return response()->json(['success' => true, 'isPinned' => $nota->isPinned]);
    }

    /**
     * Toggle the archived status of a note.
     *
     * @param  \App\Models\Nota  $nota
     * @return \Illuminate\Http\Response
     */
    public function toggleArchive(Nota $nota)
    {
        // Check if the note belongs to the authenticated user
        if ($nota->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $nota->update(['isArchived' => !$nota->isArchived]);

        return response()->json(['success' => true, 'isArchived' => $nota->isArchived]);
    }
}

