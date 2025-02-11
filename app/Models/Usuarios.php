<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Valoraciones;
use App\Models\Comentarios;
use App\Models\Listas;
use App\Models\Notificaciones;
use App\Models\Recomendaciones;
use App\Models\Seguimiento;


class Usuarios extends Model
{
    /** @use HasFactory<\Database\Factories\UsuariosFactory> */
    use HasFactory;

    protected $table = 'Usuarios';

    protected $fillable = [
        'nombre_usuario',
        'correo',
        'contrasena',
        'foto_perfil',
        'biografia',
        'rol',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function valoraciones()
    {
        return $this->hasMany(Valoraciones::class, 'id_usuario');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'id_usuario');
    }

    public function listas()
    {
        return $this->hasMany(Listas::class, 'id_usuario');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificaciones::class, 'id_usuario');
    }

    public function recomendaciones()
    {
        return $this->hasMany(Recomendaciones::class, 'id_usuario');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'id_usuario');
    }
}
