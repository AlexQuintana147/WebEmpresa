<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required'
        ]);

        $user = User::where('correo', $credentials['correo'])->first();

        if ($user && Hash::check($credentials['contrasena'], $user->contrasena)) {
            Auth::login($user, $request->remember);
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => [
                    'nombre' => $user->nombre,
                    'correo' => $user->correo
                ]
            ]);
        }

        return response()->json([
            'message' => 'Las credenciales proporcionadas son incorrectas'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}