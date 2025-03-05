<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/lista-de-actividades', function () {
    return view('lista-de-actividades');
});

Route::get('/calendario', function () {
    return view('calendario');
});

Route::get('/inversiones', function () {
    return view('inversiones');
});

Route::get('/opciones', function () {
    return view('opciones');
});

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);