<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Exception;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'correo' => 'required|string|email|max:255|unique:usuarios',
                'contrasena' => 'required|string|min:6|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])/',
                'password_confirmation' => 'required|same:contrasena'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
            ]);

            return response()->json(['message' => 'Usuario registrado exitosamente', 'user' => $user], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error en el servidor', 'error' => $e->getMessage()], 500);
        }
    }
}