<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            abort(404);
        }
        return view('profile.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('profile.edit', compact('user'));
    }

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
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $path = $request->file('foto_perfil')->store('perfiles', 'public');
            $user->foto_perfil = $path;
        }
        $user->save();
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

    public function obtenerFotoPerfil($id)
    {
        $user = User::findOrFail($id);

        if (!$user->foto_perfil) {
            return response()->file(public_path('images/default-profile.png'));
        }

        return response()->file(storage_path('app/public/' . $user->foto_perfil));
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
