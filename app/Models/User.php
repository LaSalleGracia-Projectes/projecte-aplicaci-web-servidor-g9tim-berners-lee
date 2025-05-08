<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'foto_perfil',
        'foto_perfil_mime',
        'biografia',
        'rol',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFotoPerfilBase64Attribute()
    {
        if ($this->attributes['foto_perfil'] && $this->foto_perfil_mime) {
            return 'data:' . $this->foto_perfil_mime . ';base64,' . base64_encode($this->attributes['foto_perfil']);
        }
        return null;
    }

    /**
     * Obtiene la URL del avatar del usuario usando UI Avatars
     * Genera un avatar con las iniciales del nombre del usuario
     */
    public function getAvatarUrlAttribute()
    {
        // Generar avatar con UI Avatars usando las iniciales del nombre
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=random&color=fff&size=128&rounded=true&bold=true&format=png";
    }

    /**
     * Guarda el avatar generado por UI Avatars
     */
    protected function guardarAvatarGenerado($avatarUrl)
    {
        try {
            // Obtener el contenido de la imagen
            $imageContent = file_get_contents($avatarUrl);

            if ($imageContent) {
                // Generar un nombre único para el archivo
                $fileName = 'perfiles/avatar_' . $this->id . '_' . time() . '.png';

                // Guardar la imagen en el storage
                Storage::disk('public')->put($fileName, $imageContent);

                // Actualizar el modelo
                $this->foto_perfil = $fileName;
                $this->foto_perfil_mime = 'image/png';
                $this->save();
            }
        } catch (\Exception $e) {
            \Log::error('Error al guardar avatar generado: ' . $e->getMessage());
        }
    }

    // ✅ Relación con Valoraciones
    public function valoraciones()
    {
        return $this->hasMany(Valoraciones::class, 'user_id');
    }

    // ✅ Relación con Comentarios
    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'user_id');
    }

    // ✅ Relación con Respuestas a Comentarios
    public function respuestasComentarios()
    {
        return $this->hasMany(RespuestasComentarios::class, 'user_id');
    }

    // ✅ Relación con Listas
    public function listas()
    {
        return $this->hasMany(Listas::class, 'user_id');
    }

    // ✅ Relación con Notificaciones
    public function notificaciones()
    {
        return $this->hasMany(Notificaciones::class, 'user_id');
    }

    // ✅ Relación con Recomendaciones
    public function recomendaciones()
    {
        return $this->hasMany(Recomendaciones::class, 'user_id');
    }

    // ✅ Relación con Seguimientos
    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'user_id');
    }

    // ✅ Relación con Solicitudes de Crítico
    public function solicitudesCritico()
    {
        return $this->hasMany(SolicitudCritico::class, 'user_id');
    }
}
