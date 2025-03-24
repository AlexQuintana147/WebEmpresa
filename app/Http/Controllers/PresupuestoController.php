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
    public function index(Request $request)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Pagination parameters
            $paginaCategorias = $request->input('pagina_categorias', 1);
            $paginaTransacciones = $request->input('pagina_transacciones', 1);
            $porPagina = 5;
            
            // Get user's budget categories with pagination
            $totalCategorias = $user->categoriasPresupuesto()->count();
            $categorias = $user->categoriasPresupuesto()
                ->skip(($paginaCategorias - 1) * $porPagina)
                ->take($porPagina)
                ->get();
            $totalPaginasCategorias = ceil($totalCategorias / $porPagina);
            
            // Get user's transactions with pagination
            $totalTransacciones = Transaccion::where('user_id', $user->id)->count();
            $transacciones = Transaccion::where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->skip(($paginaTransacciones - 1) * $porPagina)
                ->take($porPagina)
                ->get();
            $totalPaginasTransacciones = ceil($totalTransacciones / $porPagina);
            
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
                'ahorros',
                'paginaCategorias',
                'paginaTransacciones',
                'totalPaginasCategorias',
                'totalPaginasTransacciones',
                'porPagina'
            ));
        } else {
            // Para usuarios no autenticados, mostrar datos de ejemplo
            $presupuestoTotal = 50000;
            $gastos = 32450;
            $ingresos = 40000;
            $restante = $presupuestoTotal - $gastos;
            $ahorros = 5000;
            
            // Crear categorías de ejemplo
            $categorias = collect([
                (object)[
                    'id' => 1,
                    'nombre' => 'Vivienda',
                    'icono' => 'fa-home',
                    'color' => 'blue',
                    'presupuesto' => 12000,
                    'transacciones' => collect([(object)['tipo' => 'gasto', 'monto' => 10000]])
                ],
                (object)[
                    'id' => 2,
                    'nombre' => 'Alimentación',
                    'icono' => 'fa-utensils',
                    'color' => 'green',
                    'presupuesto' => 8000,
                    'transacciones' => collect([(object)['tipo' => 'gasto', 'monto' => 6500]])
                ],
                (object)[
                    'id' => 3,
                    'nombre' => 'Transporte',
                    'icono' => 'fa-car',
                    'color' => 'purple',
                    'presupuesto' => 5000,
                    'transacciones' => collect([(object)['tipo' => 'gasto', 'monto' => 4200]])
                ]
            ]);
            
            // Crear transacciones de ejemplo
            $transacciones = collect([
                (object)[
                    'id' => 1,
                    'descripcion' => 'Supermercado',
                    'monto' => 150,
                    'tipo' => 'gasto',
                    'fecha' => now()->subDays(3),
                    'categoria_id' => 2,
                    'categoria' => (object)['nombre' => 'Alimentación', 'icono' => 'fa-utensils', 'color' => 'green']
                ],
                (object)[
                    'id' => 2,
                    'descripcion' => 'Ingreso Salario',
                    'monto' => 3000,
                    'tipo' => 'ingreso',
                    'fecha' => now()->subDays(5),
                    'categoria_id' => null,
                    'categoria' => null
                ],
                (object)[
                    'id' => 3,
                    'descripcion' => 'Gasolina',
                    'monto' => 80,
                    'tipo' => 'gasto',
                    'fecha' => now()->subDays(2),
                    'categoria_id' => 3,
                    'categoria' => (object)['nombre' => 'Transporte', 'icono' => 'fa-car', 'color' => 'purple']
                ]
            ]);
            
            return view('presupuesto', compact(
                'categorias', 
                'transacciones', 
                'presupuestoTotal', 
                'gastos', 
                'ingresos', 
                'restante', 
                'ahorros',
                'paginaCategorias',
                'paginaTransacciones',
                'totalPaginasCategorias',
                'totalPaginasTransacciones',
                'porPagina'
            ));
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