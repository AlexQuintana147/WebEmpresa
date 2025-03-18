<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPresupuesto;
use App\Models\Transaccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\Tarea;

class PresupuestoController extends Controller
{
    /**
     * Display the budget management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get user's budget categories
            $categorias = $user->categoriasPresupuesto;
            
            // Get user's transactions
            $transacciones = Transaccion::where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->take(5)
                ->get();
            
            // Calculate budget summary
            $presupuestoTotal = $categorias->sum('presupuesto');
            $gastos = Transaccion::where('user_id', $user->id)->where('tipo', 'gasto')->sum('monto');
            $ingresos = Transaccion::where('user_id', $user->id)->where('tipo', 'ingreso')->sum('monto');
            $restante = $presupuestoTotal - $gastos;
            $ahorros = $ingresos - $gastos > 0 ? $ingresos - $gastos : 0;
            
            return view('presupuesto', compact(
                'categorias', 
                'transacciones', 
                'presupuestoTotal', 
                'gastos', 
                'ingresos', 
                'restante', 
                'ahorros'
            ));
        } else {
            // For guest users, show demo data
            return view('presupuesto');
        }
    }
    
    /**
     * Store a new budget category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'presupuesto' => 'required|numeric|min:0',
        ]);
        
        CategoriaPresupuesto::create([
            'nombre' => $request->nombre,
            'icono' => $request->icono ? 'fa-' . $request->icono : 'fa-tag',
            'color' => $request->color ?? 'blue',
            'presupuesto' => $request->presupuesto,
            'user_id' => Auth::id(),
        ]);
        
        return redirect()->route('presupuesto.index')->with('success', 'Categoría creada exitosamente');
    }
    
    /**
     * Update an existing budget category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CategoriaPresupuesto  $categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategoria(Request $request, CategoriaPresupuesto $categoria)
    {
        // Check if the category belongs to the authenticated user
        if ($categoria->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'presupuesto' => 'required|numeric|min:0',
        ]);
        
        $categoria->update([
            'nombre' => $request->nombre,
            'icono' => $request->icono ? 'fa-' . $request->icono : 'fa-tag',
            'color' => $request->color ?? 'blue',
            'presupuesto' => $request->presupuesto,
        ]);
        
        return redirect()->route('presupuesto.index')->with('success', 'Categoría actualizada exitosamente');
    }
    
    /**
     * Delete a budget category.
     *
     * @param  \App\Models\CategoriaPresupuesto  $categoria
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyCategoria(CategoriaPresupuesto $categoria)
    {
        // Check if the category belongs to the authenticated user
        if ($categoria->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $categoria->delete();
        
        return redirect()->route('presupuesto.index')->with('success', 'Categoría eliminada exitosamente');
    }
    
    /**
     * Store a new transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTransaccion(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'tipo' => 'required|in:ingreso,gasto',
            'fecha' => 'required|date',
            'categoria_id' => 'nullable|exists:categorias_presupuesto,id',
        ]);
        
        // If a category is selected, verify it belongs to the user
        if ($request->categoria_id) {
            $categoria = CategoriaPresupuesto::find($request->categoria_id);
            if ($categoria && $categoria->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        Transaccion::create([
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'categoria_id' => $request->categoria_id,
            'user_id' => Auth::id(),
        ]);
        
        return redirect()->route('presupuesto.index')->with('success', 'Transacción registrada exitosamente');
    }
    
    /**
     * Update an existing transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaccion  $transaccion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTransaccion(Request $request, Transaccion $transaccion)
    {
        // Check if the transaction belongs to the authenticated user
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'tipo' => 'required|in:ingreso,gasto',
            'fecha' => 'required|date',
            'categoria_id' => 'nullable|exists:categorias_presupuesto,id',
        ]);
        
        // If a category is selected, verify it belongs to the user
        if ($request->categoria_id) {
            $categoria = CategoriaPresupuesto::find($request->categoria_id);
            if ($categoria && $categoria->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }
        }
        
        $transaccion->update([
            'descripcion' => $request->descripcion,
            'monto' => $request->monto,
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'categoria_id' => $request->categoria_id,
        ]);
        
        return redirect()->route('presupuesto.index')->with('success', 'Transacción actualizada exitosamente');
    }
    
    /**
     * Delete a transaction.
     *
     * @param  \App\Models\Transaccion  $transaccion
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTransaccion(Transaccion $transaccion)
    {
        // Check if the transaction belongs to the authenticated user
        if ($transaccion->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $transaccion->delete();
        
        return redirect()->route('presupuesto.index')->with('success', 'Transacción eliminada exitosamente');
    }
}