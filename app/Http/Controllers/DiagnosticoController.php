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

            // Buscar la línea de respuesta IA y concatenar todas las líneas siguientes
            $respuestaIA = null;
            $encontrado = false;
            $lineas = [];
            foreach (preg_split('/\r?\n/', $response) as $line) {
                if ($encontrado) {
                    $lineas[] = trim($line);
                }
                if (strpos($line, 'Respuesta IA:') === 0) {
                    $lineas[] = trim(substr($line, strlen('Respuesta IA:')));
                    $encontrado = true;
                }
            }
            $respuestaIA = trim(implode("\n", array_filter($lineas)));

            // --- Normalización y limpieza de codificación UTF-8 ---
            $detectedEncoding = mb_detect_encoding($respuestaIA, 'UTF-8, ISO-8859-1, Windows-1252', true);
            if ($detectedEncoding && $detectedEncoding !== 'UTF-8') {
                $respuestaIA = mb_convert_encoding($respuestaIA, 'UTF-8', $detectedEncoding);
            } elseif (!$detectedEncoding) {
                $respuestaIA = mb_convert_encoding($respuestaIA, 'UTF-8', 'Windows-1252');
            }
            $respuestaIA = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $respuestaIA);
            if (!mb_check_encoding($respuestaIA, 'UTF-8')) {
                $respuestaIA = mb_convert_encoding($respuestaIA, 'UTF-8', 'UTF-8');
            }
            // --- FIN normalización ---

            // --- Procesamiento estructurado del diagnóstico IA ---
            // 1. Extrae todas las líneas relevantes
            $lineas = array_filter(array_map('trim', preg_split('/\r?\n/', $respuestaIA)));
            $malestares = [];
            $enfermedades = '';
            $recomendaciones = '';
            $recuerda = '';

            // 2. Buscar todos los textos entre comillas en la primera línea
            if (isset($lineas[0])) {
                preg_match_all('/"([^"]+)"/', $lineas[0], $matches);
                $malestares = $matches[1];
            }
            // 3. Enfermedades asociadas: texto después del último malestar entre comillas en la primera línea
            if (isset($lineas[0]) && !empty($malestares)) {
                $last_quote = strrpos($lineas[0], '"');
                if ($last_quote !== false) {
                    $enfermedades = trim(substr($lineas[0], $last_quote + 1));
                }
            }
            // 4. Recomendaciones (línea 2 si existe)
            if (isset($lineas[1])) {
                $recomendaciones = $lineas[1];
            }
            // 5. Recuerda (línea 3 si existe)
            if (isset($lineas[2])) {
                $recuerda = $lineas[2];
            }

            // 6. Construye el texto final formateado
            $texto_final = "";
            if (!empty($malestares)) {
                $texto_final .= "<b>Malestares:</b> " . implode(', ', $malestares) . "<br>";
            }
            if (!empty($enfermedades)) {
                $texto_final .= "<b>Enfermedades asociadas:</b> " . htmlspecialchars($enfermedades) . "<br>";
            }
            if (!empty($recomendaciones)) {
                $texto_final .= "<b>Recomendaciones:</b> " . htmlspecialchars($recomendaciones) . "<br>";
            }
            if (!empty($recuerda)) {
                $texto_final .= "<b>Recuerda:</b> " . htmlspecialchars($recuerda);
            }
            $respuestaIA = $texto_final;
            // --- FIN procesamiento estructurado ---

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

            return response()->json(['success' => true, 'respuesta' => $respuestaIA], 200, [
                'Content-Type' => 'application/json; charset=UTF-8'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        } else {
            Log::error('DiagnosticoIA: No se pudo iniciar el proceso del script Python');
            return response()->json([
                'success' => false,
                'error' => 'No se pudo iniciar el proceso del script Python'
            ], 500);
        }
    }
}
