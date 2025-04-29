<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PeliculasSeries;
use App\Models\LikesComentarios;

class Comentarios extends Model
{
    /** @use HasFactory<\Database\Factories\ComentariosFactory> */
    use HasFactory;

    protected $table = 'comentarios';

    protected $fillable = [
        'user_id',
        'id_pelicula',
        'comentario',
        'es_spoiler',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pelicula()
    {
        return $this->belongsTo(PeliculasSeries::class, 'id_pelicula');
    }

    public function likesComentarios()
    {
        return $this->hasMany(LikesComentarios::class, 'id_comentario');
    }
}
