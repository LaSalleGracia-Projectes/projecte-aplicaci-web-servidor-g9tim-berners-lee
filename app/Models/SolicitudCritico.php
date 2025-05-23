<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudCritico extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_critico';
    
    protected $fillable = [
        'user_id',
        'nombre',
        'apellido',
        'edad',
        'motivo',
        'estado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

