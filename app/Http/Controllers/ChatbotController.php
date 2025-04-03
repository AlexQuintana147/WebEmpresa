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
            // Ejecutar el comando y capturar la salida
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            // Unir la salida en una sola cadena
            $response = implode("\n", $output);
            
            // Sanitizar la respuesta para corregir problemas de codificación UTF-8
            // Primero intentar detectar y convertir la codificación
            $response = mb_convert_encoding($response, 'UTF-8', 'auto');
            
            // Eliminar cualquier carácter que no sea válido en UTF-8
            $response = iconv('UTF-8', 'UTF-8//IGNORE', $response);
            
            // Limpiar caracteres de control y otros caracteres problemáticos
            $response = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $response);
            
            // Reemplazar caracteres con tilde mal codificados por sus equivalentes sin tilde
            $tildes = array(
                '/[áàäâãå]/u' => 'a',
                '/[éèëê]/u' => 'e',
                '/[íìïî]/u' => 'i',
                '/[óòöôõ]/u' => 'o',
                '/[úùüû]/u' => 'u',
                '/[ñ]/u' => 'n',
                '/[ÁÀÄÂÃÅ]/u' => 'A',
                '/[ÉÈËÊ]/u' => 'E',
                '/[ÍÌÏÎ]/u' => 'I',
                '/[ÓÒÖÔÕ]/u' => 'O',
                '/[ÚÙÜÛ]/u' => 'U',
                '/[Ñ]/u' => 'N',
                '/[?]/' => '' // Eliminar signos de interrogación que puedan aparecer por problemas de codificación
            );
            
            // Solo aplicar la conversión si hay caracteres mal codificados
            if (strpos($response, '?') !== false) {
                $response = preg_replace(array_keys($tildes), array_values($tildes), $response);
            }
            
            // Extraer solo la respuesta del chatbot (entre las líneas de guiones)
            if (preg_match('/\-{10,}\n(.+?)\n\-{10,}/s', $response, $matches)) {
                $response = trim($matches[1]);
                
                // Aplicar formato en negrita y cursiva al mensaje de emergencia
                $response = preg_replace('/En caso de emergencia, puedes llamar al 106\./', '<em><strong>En caso de emergencia, puedes llamar al 106.</strong></em>', $response);
            }
            
            // Registrar la respuesta para depuración
            Log::info("Respuesta del script Python:", [
                'return_code' => $returnCode,
                'output' => $response
            ]);
            
            // Registrar información adicional sobre la codificación
            Log::info("Información de codificación:", [
                'encoding_detected' => mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true),
                'is_utf8' => mb_check_encoding($response, 'UTF-8')
            ]);
            
            // Registrar información adicional sobre la codificación
            Log::info("Información de codificación:", [
                'encoding_detected' => mb_detect_encoding($response, 'UTF-8, ISO-8859-1, Windows-1252', true),
                'is_utf8' => mb_check_encoding($response, 'UTF-8')
            ]);
            
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
            
            // Devolver la respuesta como JSON (solo la respuesta del chatbot, sin información de depuración)
            return response()->json([
                'success' => true,
                'response' => $response
            ]);
            
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