<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comentarios;

class RespuestasComentarios extends Model
{
    use HasFactory;
    
    protected $table = 'respuestas_comentarios';
    
    protected $fillable = [
        'comentario_id',
        'user_id',
        'respuesta',
        'es_spoiler',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function comentario()
    {
        return $this->belongsTo(Comentarios::class, 'comentario_id');
    }
}