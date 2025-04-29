<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\LikesComentarios;

class Comentarios extends Model
{
    use HasFactory;
    
    protected $table = 'comentarios';
    
    protected $fillable = [
        'user_id',
        'tmdb_id',
        'tipo',
        'comentario',
        'es_spoiler',
        'destacado',
    ];
    
    protected $appends = ['likes_count', 'dislikes_count'];
    
    public $timestamps = true;
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function likesComentarios()
    {
        return $this->hasMany(LikesComentarios::class, 'id_comentario');
    }
    
    public function getLikesCountAttribute()
    {
        return $this->likesComentarios()->where('tipo', 'like')->count();
    }
    
    public function getDislikesCountAttribute()
    {
        return $this->likesComentarios()->where('tipo', 'dislike')->count();
    }
}