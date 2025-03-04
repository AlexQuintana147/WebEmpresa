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

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'correo' => 'string|email|max:255|unique:usuarios,correo,' . $id,
            'imagen' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only(['nombre', 'correo', 'imagen']));

        if ($request->has('contrasena')) {
            $user->update([
                'contrasena' => Hash::make($request->contrasena)
            ]);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }
}