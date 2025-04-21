<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class DiagnosticoController extends Controller
{
    public function diagnosticoIA(Request $request)
    {
        Log::info('DiagnosticoIA: petición recibida', ['descripcion' => $request->input('descripcion')]);
        $descripcion = $request->input('descripcion');
        if (!$descripcion) {
            Log::error('DiagnosticoIA: No se recibió descripción');
            return response()->json(['success' => false, 'error' => 'No se recibió descripción'], 400);
        }
        $python = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3';
        $script = base_path('DiagnosticoIA/diagnostico_ia.py');
        $cmd = "$python " . escapeshellarg($script) . " " . escapeshellarg($descripcion);
        Log::info("Ejecutando comando: $cmd");

        $descriptorspec = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $response = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnCode = proc_close($process);

            Log::info("DiagnosticoIA.py STDOUT: $response");
            if (!empty($stderr)) {
                Log::warning("DiagnosticoIA.py STDERR: $stderr");
            }

            // Buscar la línea de respuesta IA
            $respuestaIA = null;
            foreach (preg_split('/\r?\n/', $response) as $line) {
                if (strpos($line, 'Respuesta IA:') === 0) {
                    $respuestaIA = trim(substr($line, strlen('Respuesta IA:')));
                    break;
                }
            }

            if ($returnCode !== 0 || !$respuestaIA) {
                Log::error('DiagnosticoIA: Error de ejecución o sin respuesta IA', [
                    'stdout' => $response,
                    'stderr' => $stderr,
                    'return_code' => $returnCode
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'No se pudo obtener respuesta válida de la IA.',
                    'debug' => [
                        'stdout' => $response,
                        'stderr' => $stderr,
                        'return_code' => $returnCode
                    ]
                ], 500);
            }

            return response()->json(['success' => true, 'respuesta' => $respuestaIA]);
        } else {
            Log::error('DiagnosticoIA: No se pudo iniciar el proceso del script Python');
            return response()->json([
                'success' => false,
                'error' => 'No se pudo iniciar el proceso del script Python'
            ], 500);
        }
    }
}
