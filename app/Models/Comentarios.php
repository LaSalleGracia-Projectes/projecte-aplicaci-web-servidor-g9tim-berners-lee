<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Usuarios;
use App\Models\PeliculasSeries;
use App\Models\LikesComentarios;

class Comentarios extends Model
{
    /** @use HasFactory<\Database\Factories\ComentariosFactory> */
    use HasFactory;

    protected $table = 'Comentarios';

    protected $fillable = [
        'id_usuario',
        'id_pelicula',
        'comentario',
        'es_spoiler',
        'destacado',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario');
    }

    public function peliculaSerie()
    {
        return $this->belongsTo(PeliculasSeries::class, 'id_pelicula');
    }

    public function likesComentarios()
    {
        return $this->hasMany(LikesComentarios::class, 'id_comentario');
    }
}
