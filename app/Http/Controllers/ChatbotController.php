<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ChatbotController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    protected $model = 'deepseek/deepseek-chat:free';
    protected $catalogPath;

    public function __construct()
    {
        // Obtener la API key desde la variable de entorno
        $this->apiKey = env('OPENROUTER_API_KEY');
        $this->catalogPath = public_path('CorpusChatBot.txt');
    }

    public function chat(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            if (empty($userMessage)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El mensaje no puede estar vacío.'
                ], 400);
            }

            if (!file_exists($this->catalogPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: No se encontró el archivo de catálogo.'
                ], 500);
            }

            $catalogContent = file_get_contents($this->catalogPath);

            $systemMessage = "Eres un chatbot con el nombre de 'Dr. Asistente Virtual de la Clínica Ricardo Palma' hecho para una clínica, no contestes cosas o respondas cosas fuera de tus parámetros, todo tiene que ver con medicina humana. Además siempre deja en claro que siempre lo mejor no es automedicarse, si no ir con un especialista por si la enfermedad es muy grave. El número de emergencia es el 106. Y la información que tienes es esta:\n" . $catalogContent;

            // Obtener la URL de la aplicación desde la configuración
            $appUrl = config('app.url');
            
            // Si la URL es localhost, usar un dominio más específico para el HTTP-Referer
            $refererUrl = $appUrl;
            if ($refererUrl === 'http://localhost') {
                $refererUrl = 'https://clinica-ricardo-palma.com';
            }
            
            // Generar un identificador único para el usuario basado en la sesión o IP
            $userId = md5(session()->getId() . request()->ip());
            
            Log::info('Enviando solicitud a OpenRouter (Chatbot)', [
                'referer' => $refererUrl,
                'model' => $this->model,
                'user_id_hash' => $userId
            ]);
            
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => $refererUrl,
                'Content-Type' => 'application/json',
                'X-Title' => 'Clinica Ricardo Palma Chatbot'
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $userMessage]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'user' => $userId // Identificador estable para el usuario final
            ]);

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = isset($errorData['error']['message']) 
                    ? 'Error del servidor: ' . $errorData['error']['message']
                    : 'Error al procesar la solicitud. Por favor, inténtelo de nuevo.';
                
                \Illuminate\Support\Facades\Log::error('Error en la API de OpenRouter:', [
                    'status' => $response->status(),
                    'error' => $errorData,
                    'request' => $userMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'status_code' => $response->status()
                ], $response->status());
            }

            $result = $response->json();
            if (!isset($result['choices'][0]['message']['content'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de respuesta inválido del API.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => $result['choices'][0]['message']['content']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }
}