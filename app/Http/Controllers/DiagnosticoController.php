<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DiagnosticoController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    protected $model = 'deepseek/deepseek-chat:free';

    public function __construct()
    {
        // Obtener la API key desde la variable de entorno
        $this->apiKey = env('OPENROUTER_API_KEY');
    }

    public function diagnosticoIA(Request $request)
    {
        try {
            $descripcion = $request->input('descripcion');
            if (empty($descripcion)) {
                return response()->json([
                    'success' => false,
                    'error' => 'La descripción del malestar no puede estar vacía.'
                ], 400);
            }

            $systemMessage = "Eres un asistente médico especializado en diagnósticos preliminares. Tu función es analizar los síntomas descritos y proporcionar posibles diagnósticos médicos basados en la información proporcionada. Debes seguir estas reglas estrictamente:\n\n1. Solo responde con información médica basada en evidencia.\n2. Si los síntomas son ambiguos o insuficientes para un diagnóstico, responde con 'No entiendo la consulta'.\n3. Siempre aclara que tu diagnóstico es preliminar y que se requiere una evaluación médica profesional.\n4. No proporciones tratamientos específicos, solo posibles diagnósticos y recomendaciones generales.\n5. Si detectas una posible emergencia médica, indica que se debe buscar atención médica inmediata.\n6. No respondas a consultas que no estén relacionadas con síntomas o condiciones médicas.\n7. Mantén un tono profesional y empático.\n8. Estructura tu respuesta en: Posible diagnóstico, Explicación, y Recomendaciones generales.\n9. Si la consulta no está relacionada con medicina o salud, responde con 'No entiendo la consulta'.";

            // Obtener la URL de la aplicación desde la configuración
            $appUrl = config('app.url');
            
            // Si la URL es localhost, usar un dominio más específico para el HTTP-Referer
            $refererUrl = $appUrl;
            if ($refererUrl === 'http://localhost') {
                $refererUrl = 'https://clinica-ricardo-palma.com';
            }
            
            // Generar un identificador único para el usuario basado en la sesión o IP
            $userId = md5(session()->getId() . request()->ip());
            
            Log::info('Enviando solicitud a OpenRouter', [
                'referer' => $refererUrl,
                'model' => $this->model,
                'user_id_hash' => $userId
            ]);
            
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => $refererUrl,
                'Content-Type' => 'application/json',
                'X-Title' => 'Clinica Ricardo Palma Diagnóstico IA'
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $descripcion]
                ],
                'temperature' => 0.3, // Temperatura más baja para respuestas más precisas
                'max_tokens' => 1000,
                'user' => $userId // Identificador estable para el usuario final
            ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = isset($errorData['error']['message']) 
                    ? 'Error del servidor: ' . $errorData['error']['message']
                    : 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.';
                
                \Illuminate\Support\Facades\Log::error('Error en la API de OpenRouter (Diagnóstico):', [
                    'status' => $response->status(),
                    'error' => $errorData,
                    'request' => $descripcion
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                    'status_code' => $response->status()
                ], $response->status());
            }

            $result = $response->json();
            if (!isset($result['choices'][0]['message']['content'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Formato de respuesta inválido del API.'
                ], 500);
            }

            $respuesta = $result['choices'][0]['message']['content'];
            
            // Formatear la respuesta con Markdown para mejorar la presentación
            $respuesta = $this->formatearRespuesta($respuesta);
            
            // Verificar si la respuesta indica que no entendió la consulta
            if (stripos($respuesta, 'No entiendo la consulta') !== false) {
                return response()->json([
                    'success' => true,
                    'respuesta' => 'No entiendo la consulta. Por favor, proporcione una descripción más detallada de los síntomas médicos.'
                ]);
            }

            return response()->json([
                'success' => true,
                'respuesta' => $respuesta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error del servidor: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Formatea la respuesta del diagnóstico IA con Markdown para mejorar la presentación
     * 
     * @param string $respuesta La respuesta original del modelo IA
     * @return string La respuesta formateada con Markdown
     */
    private function formatearRespuesta($respuesta)
    {
        // Identificar las secciones comunes en la respuesta
        $secciones = [
            'Posible diagnóstico' => '**Posible diagnóstico:**',
            'Posibles diagnósticos' => '**Posibles diagnósticos:**',
            'Explicación' => '**Explicación:**',
            'Recomendaciones' => '**Recomendaciones generales:**',
            'Recomendaciones generales' => '**Recomendaciones generales:**',
            'Emergencia' => '**¡EMERGENCIA!**',
            'Nota' => '**Nota:**'
        ];

        // Reemplazar los títulos de secciones con versiones en negrita
        foreach ($secciones as $original => $formateado) {
            // Buscar el título de sección seguido de dos puntos o no
            $respuesta = preg_replace('/(' . preg_quote($original, '/') . ')(:|)\s*/i', "\n\n$formateado\n", $respuesta);
        }

        // Formatear listas numeradas (líneas que comienzan con números seguidos de punto o paréntesis)
        $respuesta = preg_replace('/^(\d+)[\)\.]\s+/m', "\n$1. ", $respuesta);

        // Formatear listas con viñetas (líneas que comienzan con - o *)
        $respuesta = preg_replace('/^[\-\*]\s+/m', "\n* ", $respuesta);

        // Formatear términos médicos o palabras clave en negrita
        $terminosMedicos = ['cefalea', 'migraña', 'tensional', 'hipertensión', 'arterial', 
                           'neurológico', 'vascular', 'cerebral', 'sinusitis', 'estrés',
                           'deshidratación', 'emergencia'];
        
        foreach ($terminosMedicos as $termino) {
            $respuesta = preg_replace('/\b(' . preg_quote($termino, '/') . ')\b/i', '**$1**', $respuesta);
        }

        // Asegurar que hay espacios adecuados entre párrafos
        $respuesta = preg_replace('/\n{3,}/', "\n\n", $respuesta);
        
        // Asegurar que la respuesta comienza sin espacios en blanco
        $respuesta = trim($respuesta);

        return $respuesta;
    }
}