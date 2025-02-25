<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notificaciones extends Model
{
    /** @use HasFactory<\Database\Factories\NotificacionesFactory> */
    use HasFactory;

    protected $table = 'Notificaciones';

    protected $fillable = [
        'user_id',
        'mensaje',
        'tipo',
        'leido',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
