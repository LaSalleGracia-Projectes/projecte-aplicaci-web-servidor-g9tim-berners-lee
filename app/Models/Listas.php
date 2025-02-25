<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContenidoListas;
use App\Models\User;

class Listas extends Model
{
    /** @use HasFactory<\Database\Factories\ListasFactory> */
    use HasFactory;

    protected $table = 'listas';

    protected $fillable = [
        'user_id',
        'nombre_lista',
        'fecha_creacion',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contenidosListas()
    {
        return $this->hasMany(ContenidoListas::class, 'id_lista');
    }
}
