<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

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
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/',
                function ($attribute, $value, $fail) {
                    // Verificar si existe otro usuario con el mismo nombre (ignorando mayúsculas/minúsculas)
                    $existingUser = User::whereRaw('LOWER(nombre) = ?', [strtolower($value)])->first();
                    
                    if ($existingUser) {
                        $fail('El nombre de usuario ya existe');
                    }
                },
            ],
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
        $user = User::findOrFail(Auth::id());
        
        // Determine what type of update is being performed
        $isPasswordUpdate = $request->has('new_password');
        $isNameUpdate = $request->has('nombre');
        $isImageUpdate = $request->has('imagen');
        
        // Define validation rules based on update type
        $rules = [];
        $messages = [];
        
        // Only validate name if it's being updated
        if ($isNameUpdate) {
            $rules['nombre'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                function ($attribute, $value, $fail) use ($user) {
                    // Verificar si existe otro usuario con el mismo nombre (ignorando mayúsculas/minúsculas)
                    $existingUser = User::where('id', '!=', $user->id)
                                        ->whereRaw('LOWER(nombre) = ?', [strtolower($value)])
                                        ->first();
                    
                    if ($existingUser) {
                        $fail('El nombre de usuario ya existe');
                    }
                },
            ];
            $messages['nombre.regex'] = 'El nombre solo puede contener letras y espacios';
        }
        
        // Email validation if provided
        if ($request->has('correo')) {
            $rules['correo'] = 'string|email|max:255|unique:usuarios,correo,' . $user->id;
        }
        
        // Image validation if provided
        if ($isImageUpdate) {
            $rules['imagen'] = 'nullable|string';
        }
        
        // Password validation if provided
        if ($isPasswordUpdate) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:6|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/';
            $rules['new_password_confirmation'] = 'required|same:new_password';
            
            $messages['new_password.min'] = 'La contraseña debe tener al menos 6 caracteres';
            $messages['new_password.regex'] = 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial';
            $messages['new_password_confirmation.same'] = 'Las contraseñas no coinciden';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            if ($isPasswordUpdate) {
                if (!Hash::check($request->current_password, $user->contrasena)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La contraseña actual es incorrecta'
                    ], 422);
                }
                $user->contrasena = Hash::make($request->new_password);
            }

            if ($isNameUpdate) {
                $user->nombre = $request->nombre;
            }
            
            if ($isImageUpdate && $request->imagen) {
                // Asegurarse de que la imagen es una cadena base64 válida
                if (preg_match('/^data:image\/\w+;base64,/', $request->imagen)) {
                    // Verificar que la cadena base64 es válida
                    $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $request->imagen);
                    $decodedImage = base64_decode($base64Image, true);
                    
                    if ($decodedImage === false) {
                        return response()->json([
                            'success' => false,
                            'message' => 'La imagen no es una cadena base64 válida'
                        ], 422);
                    }
                    
                    $user->imagen = $request->imagen;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Formato de imagen no válido. Debe ser una cadena base64 con formato data:image/tipo;base64,'
                    ], 422);
                }
            }
            
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente',
                'user' => $user
            ]);}
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
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