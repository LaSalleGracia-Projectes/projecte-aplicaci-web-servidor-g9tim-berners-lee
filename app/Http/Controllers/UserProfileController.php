<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario (versión demo)
     */
    public function show()
    {
        // Creamos un usuario de ejemplo para mostrar sin autenticación
        $user = new User();
        $user->name = "Usuario Ejemplo";
        $user->email = "usuario@ejemplo.com";
        $user->biografia = "Esta es una biografía de ejemplo para mostrar cómo se vería el perfil de un usuario.";
        $user->foto_perfil = null; // Sin foto de perfil
        $user->rol = "usuario";
        $user->created_at = now()->subMonths(6); // Miembro desde hace 6 meses

        return view('profile.show', compact('user'));
    }

    /**
     * Mostrar el formulario para editar el perfil (versión demo)
     */
    public function edit()
    {
        // Mismo usuario de ejemplo
        $user = new User();
        $user->name = "Usuario Ejemplo";
        $user->email = "usuario@ejemplo.com";
        $user->biografia = "Esta es una biografía de ejemplo para mostrar cómo se vería el perfil de un usuario.";
        $user->foto_perfil = null;

        return view('profile.edit', compact('user'));
    }

    /**
     * Simulación de actualización del perfil (versión demo)
     */
    public function update(Request $request)
    {
        // Solo simulamos la validación
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:100',
            'biografia' => 'nullable|string|max:500',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Redirigimos como si se hubiera actualizado
        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente (demostración)');
    }

    /**
     * Mostrar formulario para cambiar contraseña (versión demo)
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Simulación de cambio de contraseña (versión demo)
     */
    public function changePassword(Request $request)
    {
        // Solo simulamos la validación
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Redirigimos como si se hubiera actualizado
        return redirect()->route('profile.show')->with('success', 'Contraseña actualizada correctamente (demostración)');
    }
}
