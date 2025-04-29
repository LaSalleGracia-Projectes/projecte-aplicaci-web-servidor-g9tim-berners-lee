<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notificaciones extends Model
{
    /** @use HasFactory<\Database\Factories\NotificacionesFactory> */
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'mensaje',
        'tipo',
        'leido'
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
