<?php

namespace App\Http\Controllers;

use App\Models\LikesComentarios;
use App\Models\Comentarios;
use App\Models\Notificaciones;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikesComentariosController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'id_comentario' => 'required|exists:comentarios,id',
            'tipo' => 'required|in:like,dislike',
        ]);

        $existingLike = LikesComentarios::where('user_id', $request->user_id)
            ->where('id_comentario', $request->id_comentario)
            ->first();

        $comentario = Comentarios::with('usuario')->findOrFail($request->id_comentario);
        $usuario_comentario = $comentario->usuario;
        $usuario_like = User::findOrFail($request->user_id);

        if ($existingLike && $existingLike->tipo === $request->tipo) {
            $existingLike->delete();
            
            $comentario = $this->getComentarioWithLikes($request->id_comentario);
            return response()->json([
                'message' => $request->tipo.' eliminado',
                'comentario' => $comentario
            ]);
        }
        
        else if ($existingLike) {
            $existingLike->tipo = $request->tipo;
            $existingLike->save();
            
            $comentario = $this->getComentarioWithLikes($request->id_comentario);
            return response()->json([
                'message' => $request->tipo.' actualizado',
                'comentario' => $comentario
            ]);
        }
        
        else {
            $likeComentario = LikesComentarios::create([
                'user_id' => $request->user_id,
                'id_comentario' => $request->id_comentario,
                'tipo' => $request->tipo,
            ]);
            
            if ($usuario_comentario->id != $request->user_id) {
                $tipoAccion = $request->tipo === 'like' ? 'le ha gustado' : 'no le ha gustado';
                $mensaje = "{$usuario_like->name} {$tipoAccion} tu comentario";
                
                Notificaciones::create([
                    'user_id' => $usuario_comentario->id,
                    'mensaje' => $mensaje,
                    'tipo' => 'nuevo_like',
                    'leido' => false,
                ]);
            }
            
            $comentario = $this->getComentarioWithLikes($request->id_comentario);
            return response()->json([
                'message' => $request->tipo.' creado',
                'comentario' => $comentario
            ], 201);
        }
    }

    /**
     * Get the likes status of a comment for a specific user
     */
    public function getLikeStatus($comentarioId, $userId)
    {
        $like = LikesComentarios::where('id_comentario', $comentarioId)
            ->where('user_id', $userId)
            ->first();
            
        if (!$like) {
            return response()->json(['status' => 'none']);
        }
        
        return response()->json(['status' => $like->tipo]);
    }
    
    /**
     * Get comment with like/dislike counts
     */
    private function getComentarioWithLikes($comentarioId)
    {
        $comentario = Comentarios::with('usuario')->findOrFail($comentarioId);
        
        $likes = LikesComentarios::where('id_comentario', $comentarioId)
            ->where('tipo', 'like')
            ->count();
            
        $dislikes = LikesComentarios::where('id_comentario', $comentarioId)
            ->where('tipo', 'dislike')
            ->count();
            
        $comentario->likes_count = $likes;
        $comentario->dislikes_count = $dislikes;
        
        return $comentario;
    }
    
    /**
     * Get likes and dislikes count for a comment
     */
    public function getLikesCount($comentarioId)
    {
        $likes = LikesComentarios::where('id_comentario', $comentarioId)
            ->where('tipo', 'like')
            ->count();
            
        $dislikes = LikesComentarios::where('id_comentario', $comentarioId)
            ->where('tipo', 'dislike')
            ->count();
            
        return response()->json([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }
}