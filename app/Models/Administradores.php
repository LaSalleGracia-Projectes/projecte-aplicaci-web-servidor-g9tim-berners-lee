<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administradores extends Model
{
    /** @use HasFactory<\Database\Factories\AdministradoresFactory> */
    use HasFactory;

    protected $table = 'Administradores';

    protected $fillable = [
        'nombre_admin',
        'correo',
        'contrasena',
        'rol',
        'fecha_creacion',
    ];

    public $timestamps = false;
}
