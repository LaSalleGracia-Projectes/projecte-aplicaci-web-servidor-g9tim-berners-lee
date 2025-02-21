<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Comentarios;
use App\Models\User;

class LikesComentarios extends Model
{
    /** @use HasFactory<\Database\Factories\LikesComentariosFactory> */
    use HasFactory;

    protected $table = 'likes_comentarios';

    protected $fillable = [
        'user_id',
        'id_comentario',
        'tipo',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comentario()
    {
        return $this->belongsTo(Comentarios::class, 'id_comentario');
    }
}
