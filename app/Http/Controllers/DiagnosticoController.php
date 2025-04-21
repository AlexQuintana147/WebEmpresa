<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class DiagnosticoController extends Controller
{
    public function diagnosticoIA(Request $request)
    {
        $descripcion = $request->input('descripcion');
        if (!$descripcion) {
            return response()->json(['success' => false, 'error' => 'No se recibió descripción'], 400);
        }
        $python = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3';
        $script = base_path('DiagnosticoIA/diagnostico_ia.py');
        $cmd = "$python " . escapeshellarg($script) . " " . escapeshellarg($descripcion);
        Log::info('Comando ejecutado: ' . $cmd);
        $respuesta = shell_exec($cmd);
        Log::info('Respuesta script: ' . $respuesta);
        if (!$respuesta || strpos($respuesta, 'ERROR:') === 0) {
            return response()->json(['success' => false, 'error' => $respuesta ?: 'Sin respuesta del script'], 500);
        }
        return response()->json(['success' => true, 'respuesta' => trim($respuesta)]);
    }
}
