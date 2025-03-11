<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TareaController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/chat', function () {
    return view('chat');
});

Route::get('/instrucciones', function () {
    return view('instrucciones');
});

Route::get('/presupuesto', function () {
    return view('presupuesto');
});

Route::get('/lista-de-actividades', [TareaController::class, 'index'])->name('actividades.index');

Route::get('/calendario', [TareaController::class, 'create'])->name('calendario');

// Redirect from /tareas to /calendario
Route::redirect('/tareas', '/calendario');

// Rutas para la gestión de tareas
Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');
Route::get('/tareas/{tarea}', [TareaController::class, 'show'])->name('tareas.show');
Route::get('/tareas/{tarea}/edit', [TareaController::class, 'edit'])->name('tareas.edit');
Route::put('/tareas/{tarea}', [TareaController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{tarea}', [TareaController::class, 'destroy'])->name('tareas.destroy');
Route::get('/tareas-json', [TareaController::class, 'getTareasJson'])->name('tareas.json');

Route::get('/inversiones', function () {
    return view('inversiones');
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