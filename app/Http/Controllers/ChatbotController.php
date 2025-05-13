<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;


class ChatbotController extends Controller
{
    protected $apiKey;
    protected $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    protected $model = 'deepseek/deepseek-chat:free';
    protected $catalogPath;

    public function __construct()
    {
        $this->apiKey = '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';
        $this->catalogPath = public_path('CorpusChatBot.txt');
    }

    public function chat(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            $catalogContent = file_get_contents($this->catalogPath);

            $systemMessage = "Eres un chatbot con el nombre de 'Dr. Asistente Virtual de la Clínica Ricardo Palma' hecho para una clínica, no contestes cosas o respondas cosas fuera de tus parámetros, todo tiene que ver con medicina humana. Además siempre deja en claro que siempre lo mejor no es automedicarse, si no ir con un especialista por si la enfermedad es muy grave. El número de emergencia es el 106. Y la información que tienes es esta:\n" . $catalogContent;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => config('app.url'),
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemMessage],
                    ['role' => 'user', 'content' => $userMessage]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $result['choices'][0]['message']['content'] ?? 'Lo siento, no pude procesar tu mensaje.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar la solicitud.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}