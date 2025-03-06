<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'correo' => 'required|string|email|max:255|unique:usuarios',
            'contrasena' => [
                'required',
                'string',
                'min:6',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ]
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios',
            'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres',
            'contrasena.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'imagen' => null,
            'rol_id' => 2
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'correo' => 'string|email|max:255|unique:usuarios,correo,' . $user->id,
            'imagen' => 'nullable|string',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'new_password_confirmation' => 'required_with:new_password|same:new_password'
        ], [
            'nombre.regex' => 'El nombre solo puede contener letras y espacios',
            'new_password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'new_password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial',
            'new_password_confirmation.same' => 'Las contraseñas no coinciden'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            if ($request->has('new_password')) {
                if (!Hash::check($request->current_password, $user->contrasena)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La contraseña actual es incorrecta'
                    ], 422);
                }
                $user->contrasena = Hash::make($request->new_password);
            }

            $user->nombre = $request->nombre;
            if ($request->has('imagen')) {
                $user->imagen = $request->imagen;
            }
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}