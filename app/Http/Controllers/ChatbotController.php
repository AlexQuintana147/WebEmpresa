<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class ChatbotController extends Controller
{
    /**
     * Procesa la consulta del usuario y ejecuta el script Python.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processQuery(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'question' => 'required|string|max:1000',
        ]);

        // Obtener la pregunta del usuario
        $question = $request->input('question');
        
        // Escapar comillas para evitar problemas en la línea de comandos
        $escapedQuestion = escapeshellarg($question);
        
        // Ruta al script Python (usando rutas absolutas para mayor seguridad)
        $scriptPath = base_path('Script/app.py');
        
        // Comando a ejecutar
        $command = "python \"$scriptPath\" $escapedQuestion";
        
        // Registrar el comando para depuración
        Log::info("Ejecutando comando: $command");
        
        try {
            // Ejecutar el comando usando proc_open para mayor control sobre la salida
            $descriptorspec = [
                0 => ["pipe", "r"],  // stdin
                1 => ["pipe", "w"],  // stdout
                2 => ["pipe", "w"]   // stderr
            ];
            
            // Abrir el proceso
            $process = proc_open($command, $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                // Leer la salida estándar con codificación binaria para evitar transformaciones automáticas
                $response = stream_get_contents($pipes[1]);
                
                // Leer también los errores si hay alguno
                $stderr = stream_get_contents($pipes[2]);
                
                // Cerrar los pipes
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                
                // Obtener el código de retorno
                $returnCode = proc_close($process);
                
                // Registrar errores si existen
                if (!empty($stderr)) {
                    Log::warning("Error en la salida estándar del script Python:", [
                        'stderr' => $stderr
                    ]);
                }
                
                // Guardar la respuesta original para depuración
                $originalResponse = $response;
                
                // Registrar información de la respuesta original
                Log::info("Respuesta original del script Python:", [
                    'encoding_detected' => mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true),
                    'raw_bytes' => bin2hex(substr($response, 0, 50))
                ]);
                
                // Forzar la codificación a UTF-8 desde Windows-1252 (común en sistemas Windows)
                $response = iconv('Windows-1252', 'UTF-8//TRANSLIT//IGNORE', $response);
                
                // Si la conversión falló, intentar con mb_convert_encoding
                if ($response === false) {
                    Log::warning("Conversión con iconv falló, intentando con mb_convert_encoding");
                    $response = mb_convert_encoding($originalResponse, 'UTF-8', 'Windows-1252');
                }
                
                // Limpiar cualquier carácter inválido que pueda haber quedado
                $response = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $response);
                
                // Registrar información después de la conversión
                Log::info("Respuesta después de conversión a UTF-8:", [
                    'encoding_detected' => mb_detect_encoding($response, 'UTF-8', true),
                    'is_valid_utf8' => mb_check_encoding($response, 'UTF-8'),
                    'sample' => substr($response, 0, 100)
                ]);
            } else {
                throw new \Exception("No se pudo iniciar el proceso del script Python");
            }
            
            // Detectar la codificación de la respuesta
            $detectedEncoding = mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true);
            
            // Si no es UTF-8, convertir a UTF-8
            if ($detectedEncoding && $detectedEncoding !== 'UTF-8') {
                $response = mb_convert_encoding($response, 'UTF-8', $detectedEncoding);
                Log::info("Codificación convertida de $detectedEncoding a UTF-8");
            } elseif (!$detectedEncoding) {
                // Si no se pudo detectar la codificación, intentar con Windows-1252 (común en Windows)
                $response = mb_convert_encoding($response, 'UTF-8', 'Windows-1252');
                Log::info("No se pudo detectar la codificación, forzando conversión desde Windows-1252 a UTF-8");
            }
            
            // Asegurar que la respuesta sea válida UTF-8
            if (!mb_check_encoding($response, 'UTF-8')) {
                // Si aún no es UTF-8 válido, limpiar caracteres inválidos
                $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
                Log::info("Se limpiaron caracteres UTF-8 inválidos");
            }
            
            // Mantener el formato exacto de la respuesta, incluyendo las líneas de guiones
            // Solo aplicar formato HTML al mensaje de emergencia sin alterar el resto
            $response = preg_replace('/En caso de emergencia, puedes llamar al 106\./', '<em><strong>En caso de emergencia, puedes llamar al 106.</strong></em>', $response);
            
            // Registrar la respuesta para depuración
            Log::info("Respuesta del script Python (después de conversión):", [
                'return_code' => $returnCode,
                'output_length' => strlen($response),
                'output_sample' => substr($response, 0, 100) . (strlen($response) > 100 ? '...' : ''),
                'encoding_final' => mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true),
                'is_utf8' => mb_check_encoding($response, 'UTF-8'),
                'raw_bytes' => bin2hex(substr($response, 0, 50)) // Mostrar los primeros 50 bytes en hexadecimal
            ]);
            
            // Asegurar que las líneas de guiones se preserven exactamente como están en la consola
            
            // Verificar si hubo un error en la ejecución
            if ($returnCode !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al ejecutar el script Python',
                    'debug_info' => [
                        'return_code' => $returnCode,
                        'raw_output' => $response
                    ]
                ], 500);
            }
            
            // Devolver la respuesta como JSON con la codificación UTF-8 explícita
            // Usar opciones JSON_UNESCAPED_UNICODE y JSON_UNESCAPED_SLASHES para preservar caracteres especiales
            return response()->json([
                'success' => true,
                'response' => $response,
                'encoding_info' => [
                    'detected' => mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true),
                    'is_valid_utf8' => mb_check_encoding($response, 'UTF-8')
                ]
            ], 200, [
                'Content-Type' => 'application/json; charset=UTF-8'
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
            
        } catch (\Exception $e) {
            // Registrar el error
            Log::error("Error al procesar la consulta del chatbot: " . $e->getMessage());
            
            // Devolver respuesta de error
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'debug_info' => [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }
}