<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    // ✅ REGISTRO
    public function register(Request $request)
    {
        try {
            // Validar los campos necesarios
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'usuario', // Asignar rol por defecto
            ]);

            // Enviar el correo de bienvenida
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            } catch (\Exception $e) {
                Log::error('Error enviando email: ' . $e->getMessage());
                // Continuamos con el proceso aunque falle el email
            }

            // Para solicitudes AJAX, devolver respuesta JSON
            if ($request->expectsJson()) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'Usuario registrado correctamente',
                    'token' => $token,
                    'user' => $user
                ], 201);
            }

            // Iniciar sesión automáticamente
            auth()->login($user);

            // Para solicitudes de formulario, redirigir
            return redirect('/')->with('success', '¡Registro completado con éxito!');

        } catch (\Exception $e) {
            Log::error('Error en el registro: ' . $e->getMessage());

            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error en el registro',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'error' => 'Ha ocurrido un error durante el registro. Por favor, inténtalo de nuevo.'
            ])->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    // ✅ LOGIN
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $credentials = $request->only('email', 'password');

            // Intento de autenticación
            if (auth()->attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();

                // Para solicitudes AJAX, devolver respuesta JSON
                if ($request->expectsJson()) {
                    $user = auth()->user();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json([
                        'message' => 'Inicio de sesión exitoso',
                        'token' => $token,
                        'user' => $user
                    ], 200);
                }

                // Para solicitudes de formulario, redirigir
                return redirect()->intended('/');
            }

            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales no son correctas.'],
                ]);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->withInput($request->except('password'));

        } catch (\Exception $e) {
            // Para solicitudes AJAX, devolver error JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error en el inicio de sesión',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Para solicitudes de formulario, redirigir con errores
            return back()->withErrors([
                'email' => 'Ha ocurrido un error. Por favor, inténtalo de nuevo.',
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        // Si hay un usuario autenticado, eliminar sus tokens
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        // Cerrar sesión web si está disponible
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Para solicitudes AJAX, devolver respuesta JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cierre de sesión exitoso'
            ]);
        }

        // Para solicitudes web, redirigir
        return redirect('/');
    }

    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Muestra el formulario de registro
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
}

