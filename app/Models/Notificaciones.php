<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuarios;

class Notificaciones extends Model
{
    /** @use HasFactory<\Database\Factories\NotificacionesFactory> */
    use HasFactory;

    protected $table = 'Notificaciones';

    protected $fillable = [
        'id_usuario',
        'mensaje',
        'tipo',
        'leido',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario');
    }
}
