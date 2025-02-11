<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comentarios;
use App\Models\Usuarios;

class LikesComentarios extends Model
{
    /** @use HasFactory<\Database\Factories\LikesComentariosFactory> */
    use HasFactory;

    protected $table = 'likes_comentarios';

    protected $fillable = [
        'id_usuario',
        'id_comentario',
        'tipo',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentarios::class, 'id_comentario');
    }
}
