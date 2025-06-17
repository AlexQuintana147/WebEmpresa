<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;

class ReniecController extends Controller
{
    /**
     * Consulta la API de RENIEC para obtener datos de un DNI
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consultarDni(Request $request)
    {
        // Validar el DNI
        $request->validate([
            'dni' => 'required|string|size:8'
        ]);

        try {
            // Realizar la consulta a la API de RENIEC
            $response = Http::withHeaders([
                'Authorization' => 'apis-token-15854.VQIB4X1xNlKCLtdPrZoZQXd951llc6jm'
            ])->get('https://api.apis.net.pe/v2/reniec/dni', [
                'numero' => $request->dni
            ]);

            // Verificar si la respuesta fue exitosa
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener información del DNI'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar la API de RENIEC',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}