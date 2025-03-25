<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario (versión demo)
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        return view('profile.show', compact('user'));
    }

    /**
     * Mostrar el formulario para editar el perfil (versión demo)
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('profile.edit', compact('user'));
    }

    /**
     * Simulación de actualización del perfil (versión demo)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'biografia' => 'nullable|string|max:500',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->biografia = $validatedData['biografia'] ?? null;

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                Storage::delete('public/' . $user->foto_perfil);
            }

            $photoPath = $request->file('foto_perfil')->store('profile_photos', 'public');
            $user->foto_perfil = $photoPath;
        }

        $user->save();

        // Asegúrate de pasar el ID del usuario al redirigir
        return redirect()->route('profile.show', ['id' => $user->id])
            ->with('success', 'Perfil actualizado correctamente');
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
