<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validación y lógica de autenticación
        $credentials = $request->only('correo', 'contrasena');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->route('home');
        }

        return back()->withErrors(['correo' => 'Credenciales incorrectas']);
    }

    public function register(Request $request)
    {
        // Lógica de registro de usuario: validar datos, encriptar contraseña y guardar en la base de datos.
    }
}
