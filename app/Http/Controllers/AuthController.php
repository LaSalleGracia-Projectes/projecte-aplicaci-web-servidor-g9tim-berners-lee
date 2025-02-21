<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    // ✅ REGISTRO
    public function register(Request $request)
    {
        // Validar los campos necesarios
        $request->validate([
            'name' => 'required|string|max:255', // Nombre de usuario
            'email' => 'required|string|email|max:255|unique:users', // Correo
            'password' => 'required|string|min:6|confirmed', // Contraseña
        ]);

        // Crear el usuario
        $user = User::create([
            'name' => $request->name,  // Nombre de usuario
            'email' => $request->email,  // Correo
            'password' => Hash::make($request->password),  // Contraseña
        ]);

        // Crear el token para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        // Responder con un mensaje y el token
        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    // ✅ LOGOUT
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Cierre de sesión exitoso'
        ]);
    }
}
