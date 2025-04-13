<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\CitaController;

Route::get('/', function () {
    return view('sobrenosotros');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::post('/chat/query', [\App\Http\Controllers\ChatbotController::class, 'processQuery'])->name('chat.query');

Route::get('/instrucciones', function () {
    return view('instrucciones');
});

// Rutas para gestión (rol_id = 4)
Route::middleware('gestion')->group(function () {
    Route::get('/presupuesto', [App\Http\Controllers\PresupuestoController::class, 'index'])->name('presupuesto.index');
    
    // Rutas para categorías de presupuesto
    Route::post('/categorias', [App\Http\Controllers\PresupuestoController::class, 'storeCategoria'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [App\Http\Controllers\PresupuestoController::class, 'updateCategoria'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [App\Http\Controllers\PresupuestoController::class, 'destroyCategoria'])->name('categorias.destroy');
    
    // Rutas para transacciones
    Route::post('/transacciones', [App\Http\Controllers\PresupuestoController::class, 'storeTransaccion'])->name('transacciones.store');
    Route::put('/transacciones/{transaccion}', [App\Http\Controllers\PresupuestoController::class, 'updateTransaccion'])->name('transacciones.update');
    Route::delete('/transacciones/{transaccion}', [App\Http\Controllers\PresupuestoController::class, 'destroyTransaccion'])->name('transacciones.destroy');
    
    // Rutas para reportes y recursos (mencionadas en el sidebar)
    Route::get('/reportes', function () {
        return view('reportes');
    })->name('reportes.index');
    
    Route::get('/recursos', function () {
        return view('recursos');
    })->name('recursos.index');
});

// Rutas para doctores (rol_id = 3)
Route::middleware('doctor')->group(function () {
    Route::get('/actividades', [App\Http\Controllers\ActividadController::class, 'index'])->name('actividades.index');
    Route::post('/actividades', [App\Http\Controllers\ActividadController::class, 'store'])->name('actividades.store');
    Route::put('/actividades/{actividad}', [App\Http\Controllers\ActividadController::class, 'update'])->name('actividades.update');
    Route::delete('/actividades/{actividad}', [App\Http\Controllers\ActividadController::class, 'destroy'])->name('actividades.destroy');
    Route::put('/actividades/{actividad}/cambiar-estado', [App\Http\Controllers\ActividadController::class, 'cambiarEstado'])->name('actividades.cambiar-estado');

    Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
    Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
    Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
    Route::get('/tareas/{tarea}/edit', [TareaController::class, 'edit'])->name('tareas.edit');
    Route::put('/tareas/{tarea}', [TareaController::class, 'update'])->name('tareas.update');
    Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');
    Route::get('/tareas-json', [TareaController::class, 'getTareasJson'])->name('tareas.json');

    Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
    Route::post('/notas', [NotaController::class, 'store'])->name('notas.store');
    Route::put('/notas/{nota}', [NotaController::class, 'update'])->name('notas.update');
    Route::delete('/notas/{nota}', [NotaController::class, 'destroy'])->name('notas.destroy');
    Route::post('/notas/{nota}/toggle-pin', [NotaController::class, 'togglePin'])->name('notas.pin');
    Route::post('/notas/{nota}/toggle-archive', [NotaController::class, 'toggleArchive'])->name('notas.archive');

    Route::get('/pacientes', [\App\Http\Controllers\DoctorController::class, 'index'])->name('pacientes.index');
    Route::post('/doctores/verificar-dni', [\App\Http\Controllers\ReniecController::class, 'consultarDni'])->name('doctores.verificar-dni');
    Route::post('/doctores/guardar-dni', [\App\Http\Controllers\DoctorController::class, 'guardarDni'])->name('doctores.guardar-dni');
});


// Rutas para pacientes (rol_id = 2)
Route::middleware('paciente')->group(function () {
    Route::get('/historial', function () {
        return view('historial');
    })->name('historial.index');
    
    Route::get('/atencionmedica', [\App\Http\Controllers\PacienteController::class, 'index'])->name('atencionmedica.index');
    Route::post('/pacientes/verificar-dni', [\App\Http\Controllers\PacienteController::class, 'verificarDni'])->name('pacientes.verificar-dni');
    Route::post('/pacientes', [\App\Http\Controllers\PacienteController::class, 'store'])->name('pacientes.store');
    Route::post('/pacientes/asociar', [\App\Http\Controllers\PacienteController::class, 'asociarUsuario'])->name('pacientes.asociar');
    
    // Ruta para consultar la API de RENIEC desde el backend
    Route::post('/reniec/consultar-dni', [\App\Http\Controllers\ReniecController::class, 'consultarDni'])->name('reniec.consultar-dni');
    
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
    Route::post('/api/citas/agendar', [CitaController::class, 'agendarCita']);
    Route::post('/api/citas/cancelar/{id}', [CitaController::class, 'cancelarCita']);
    Route::get('/api/citas/horarios-disponibles/{doctorId}/{fecha}', [CitaController::class, 'getHorariosDisponibles']);
    Route::get('/api/doctores/por-especialidad/{especialidad}', [CitaController::class, 'getDoctoresPorEspecialidad']);
    Route::post('/api/pacientes/verificar-dni', [CitaController::class, 'verificarDni']);
});

Route::get('/opciones', function () {
    return view('opciones');
})->middleware(\App\Http\Middleware\RedirectIfNotAuthenticated::class);

// Redirect from /actualizar-perfil to /opciones
Route::redirect('/actualizar-perfil', '/opciones');

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// Profile update route
Route::post('/actualizar-perfil', [UserController::class, 'update'])
    ->name('profile.update')
    ->middleware('auth');
