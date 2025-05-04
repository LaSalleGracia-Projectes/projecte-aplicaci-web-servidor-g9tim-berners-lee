<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Models\PeliculasSeries;
use App\Models\Valoraciones;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administrador con estadísticas en tiempo real
     */
    public function dashboard()
    {
        // Obtener estadísticas básicas
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $totalMovies = PeliculasSeries::count();
        $totalReviews = Valoraciones::count();

        // Obtener distribución de roles
        $roleCounts = User::select('rol', DB::raw('count(*) as total'))
                           ->groupBy('rol')
                           ->get()
                           ->pluck('total', 'rol')
                           ->toArray();

        $rolePercentages = [];
        foreach ($roleCounts as $role => $count) {
            $percentage = round(($count / $totalUsers) * 100, 1);
            $rolePercentages[$role] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        // Asegurar que todos los roles están representados
        $allRoles = ['admin', 'premium', 'critico', 'usuario'];
        foreach ($allRoles as $role) {
            if (!isset($rolePercentages[$role])) {
                $rolePercentages[$role] = [
                    'count' => 0,
                    'percentage' => 0
                ];
            }
        }

        // Tendencia de nuevos usuarios (30 días)
        $userTrend = User::where('created_at', '>=', Carbon::now()->subDays(30))
                        ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                        ->groupBy('date')
                        ->get();

        // Usuarios recientes
        $recentUsers = User::orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        // Valoraciones recientes - Solo mostramos información básica sin columnas problemáticas
        $recentReviews = Valoraciones::join('users', 'valoraciones.user_id', '=', 'users.id')
                                 ->select(
                                    'valoraciones.id',
                                    'users.name as user_name',
                                    'valoraciones.valoracion',
                                    'valoraciones.created_at'
                                 )
                                 ->orderBy('valoraciones.created_at', 'desc')
                                 ->limit(5)
                                 ->get();

        // Estadísticas para el dashboard
        $stats = [
            'total_users' => $totalUsers,
            'new_users_today' => $newUsersToday,
            'total_movies' => $totalMovies,
            'total_reviews' => $totalReviews,
            'visits_today' => mt_rand(50, 200), // Valor de ejemplo (deberías usar datos reales)
            'role_percentages' => $rolePercentages,
            'user_trend' => $userTrend
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentReviews'));
    }

    /**
     * Muestra la vista de gestión de usuarios
     */
    public function users(Request $request)
    {
        // Iniciar la consulta
        $query = User::query();

        // Aplicar filtros si están presentes
        // Filtro de búsqueda por nombre o email
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->where('rol', $request->role);
        }

        // Filtro por estado (activo/pendiente)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else if ($request->status === 'inactive') {
                $query->whereNull('email_verified_at');
            }
        }

        // Filtro por fecha de registro
        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } else if ($request->date === 'week') {
                $query->where('created_at', '>=', Carbon::now()->subDays(7));
            } else if ($request->date === 'month') {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }
        }

        // Ordenar por fecha de creación descendente
        $query->orderBy('created_at', 'desc');

        // Paginar resultados
        $users = $query->paginate(10);

        // Calcular estadísticas
        $stats = [
            'total' => User::count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'new_today' => User::whereDate('created_at', today())->count()
        ];

        return view('admin.users', [
            'users' => $users,
            'stats' => $stats
        ]);
    }

    /**
     * Muestra la lista de películas
     */
    public function movies()
    {
        $movies = DB::table('peliculas_series')
            ->orderBy('titulo')
            ->paginate(10);
        return view('admin.movies', compact('movies'));
    }

    /**
     * Muestra la lista de valoraciones
     */
    public function reviews()
    {
        try {
            // Consulta base para las valoraciones
            $query = DB::table('valoraciones')
                ->join('users', 'valoraciones.user_id', '=', 'users.id')
                ->select(
                    'valoraciones.id',
                    'users.name as user_name',
                    'users.profile_photo as user_profile_photo',
                    'valoraciones.valoracion',
                    'valoraciones.comentario',
                    'valoraciones.created_at'
                );

            // Aplicar filtros según parámetros de la URL
            if (request()->has('user') && !empty(request('user'))) {
                $query->where('users.name', 'LIKE', '%' . request('user') . '%');
            }

            if (request()->has('movie') && !empty(request('movie'))) {
                // Ya no podemos filtrar por película directamente
                // Podemos quitar este filtro o implementar otra solución
            }

            if (request()->has('rating') && !empty(request('rating'))) {
                $query->where('valoraciones.valoracion', request('rating'));
            }

            if (request()->has('date')) {
                $date = request('date');
                if ($date === 'today') {
                    $query->whereDate('valoraciones.created_at', Carbon::today());
                } elseif ($date === 'week') {
                    $query->where('valoraciones.created_at', '>=', Carbon::now()->subDays(7));
                } elseif ($date === 'month') {
                    $query->where('valoraciones.created_at', '>=', Carbon::now()->subDays(30));
                }
            }

            // Ordenar y paginar directamente desde la consulta
            $reviews = $query->orderBy('valoraciones.created_at', 'desc')->paginate(10);

            // Estadísticas para el panel
            $stats = [
                'total' => DB::table('valoraciones')->count(),
                'likes' => DB::table('valoraciones')->where('valoracion', 'like')->count(),
                'dislikes' => DB::table('valoraciones')->where('valoracion', 'dislike')->count(),
                'hoy' => DB::table('valoraciones')->whereDate('created_at', Carbon::today())->count(),
            ];
        } catch (\Exception $e) {
            // En caso de error, crear una paginación vacía correctamente
            $reviews = new \Illuminate\Pagination\LengthAwarePaginator(
                [], // datos vacíos
                0,  // contar total
                10, // por página
                1   // página actual
            );

            $stats = [
                'total' => 0,
                'likes' => 0,
                'dislikes' => 0,
                'hoy' => 0,
            ];
        }

        return view('admin.reviews', compact('reviews', 'stats'));
    }

    /**
     * Muestra el perfil del administrador
     */
    public function profile()
    {
        $admin = Auth::user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Actualiza la información del perfil del administrador
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$admin->id,
            'biografia' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->biografia = $request->biografia;
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Actualiza la contraseña del administrador
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
            ], 422);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    /**
     * Obtiene datos de registro de usuarios por día para un período específico
     * @param int $days Número de días a considerar
     * @return array Datos de registro de usuarios por día
     */
    private function getUserRegistrationData($days = 30)
    {
        $data = [];

        // Fecha de inicio (hace $days días)
        $startDate = Carbon::now()->subDays($days);

        // Obtener registros agrupados por día
        $registrations = User::where('created_at', '>=', $startDate)
                                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                ->groupBy('date')
                                ->get()
                                ->pluck('count', 'date')
                                ->toArray();

        // Generar array con todos los días y sus conteos
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $data[$date] = isset($registrations[$date]) ? $registrations[$date] : 0;
        }

        // Ordenar por fecha (de más antigua a más reciente)
        ksort($data);

        return $data;
    }

    /**
     * Obtiene estadísticas actualizadas para la API
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            // Estadísticas de usuarios
            $totalUsers = User::count();
            $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
            $verifiedUsers = User::whereNotNull('email_verified_at')->count();
            $unverifiedUsers = User::whereNull('email_verified_at')->count();

            // Estadísticas de películas/series
            $totalMovies = DB::table('peliculas_series')->count();

            // Estadísticas de valoraciones
            $totalReviews = DB::table('valoraciones')->count();

            // Estadísticas por rol
            $userRoles = User::select('rol', DB::raw('count(*) as total'))
                           ->groupBy('rol')
                           ->get()
                           ->pluck('total', 'rol')
                           ->toArray();

            // Empaquetar estadísticas
            $stats = [
                'total_users' => $totalUsers,
                'new_users_today' => $newUsersToday,
                'verified_users' => $verifiedUsers,
                'unverified_users' => $unverifiedUsers,
                'total_movies' => $totalMovies,
                'total_reviews' => $totalReviews,
                'user_roles' => $userRoles
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un usuario de la base de datos
     * @param int $id ID del usuario a eliminar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function deleteUser($id)
    {
        try {
            \Log::info('Iniciando eliminación de usuario:', ['user_id' => $id]);

            $user = User::findOrFail($id);

            // Verificar que no sea el último admin
            if ($user->rol === 'admin' && User::where('rol', 'admin')->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al último administrador'
                ], 400);
            }

            // Eliminar registros relacionados de forma segura
            try {
                if (method_exists($user, 'comentarios') && $user->comentarios()->exists()) {
                    $user->comentarios()->delete();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al eliminar comentarios del usuario:', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            try {
                if (method_exists($user, 'valoraciones') && $user->valoraciones()->exists()) {
                    $user->valoraciones()->delete();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al eliminar valoraciones del usuario:', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            try {
                if (method_exists($user, 'listas') && $user->listas()->exists()) {
                    $user->listas()->delete();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al eliminar listas del usuario:', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            try {
                if (method_exists($user, 'notificaciones') && $user->notificaciones()->exists()) {
                    $user->notificaciones()->delete();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al eliminar notificaciones del usuario:', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            try {
                if (method_exists($user, 'seguimientos') && $user->seguimientos()->exists()) {
                    $user->seguimientos()->delete();
                }
            } catch (\Exception $e) {
                \Log::warning('Error al eliminar seguimientos del usuario:', [
                    'user_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }

            // Eliminar el usuario
            $user->delete();

            \Log::info('Usuario eliminado exitosamente:', ['user_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario:', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza la información de un usuario
     * @param \Illuminate\Http\Request $request Datos de la solicitud
     * @param int $id ID del usuario a actualizar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validar datos
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'rol' => 'required|in:usuario,critico,premium,admin',
                'status' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar datos básicos
            $user->name = $request->name;
            $user->email = $request->email;
            $user->rol = $request->rol;

            // Actualizar estado (activación/desactivación)
            if ($request->status) {
                $user->email_verified_at = $user->email_verified_at ?? now();
            } else {
                $user->email_verified_at = null;
            }

            // Actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'status' => $user->email_verified_at !== null,
                    'created_at' => $user->created_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una valoración de la base de datos
     * @param int $id ID de la valoración a eliminar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function deleteReview($id)
    {
        try {
            // Verificar si la valoración existe
            $review = DB::table('valoraciones')->where('id', $id)->first();

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'La valoración no existe'
                ], 404);
            }

            // Eliminar la valoración
            DB::table('valoraciones')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => "Valoración eliminada correctamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la valoración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una película de la base de datos
     * @param int $id ID de la película a eliminar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function deleteMovie($id)
    {
        try {
            // Verificar si la película existe
            $movie = DB::table('peliculas_series')->where('id', $id)->first();

            if (!$movie) {
                return response()->json([
                    'success' => false,
                    'message' => 'La película no existe'
                ], 404);
            }

            // Eliminar imagen de poster si existe
            if ($movie->poster) {
                Storage::delete('public/' . $movie->poster);
            }

            // Eliminar valoraciones relacionadas
            DB::table('valoraciones')->where('id_pelicula', $id)->delete();

            // Eliminar de listas de usuarios si existe
            DB::table('contenido_listas')->where('id_pelicula', $id)->delete();

            // Eliminar la película
            DB::table('peliculas_series')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => "Película eliminada correctamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la película: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos
     * @param \Illuminate\Http\Request $request Datos del usuario a crear
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function createUser(Request $request)
    {
        try {
            // Validar datos
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:6',
                'rol' => 'required|in:usuario,critico,premium,admin',
                'status' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Crear el usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->rol = $request->rol;

            // Establecer fecha de verificación si el usuario está activo
            if ($request->status) {
                $user->email_verified_at = now();
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'status' => $user->email_verified_at !== null,
                    'created_at' => $user->created_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hace admin a un usuario
     * @param Request $request
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeAdmin(Request $request, $id)
    {
        try {
            \Log::info('Iniciando conversión a admin:', ['user_id' => $id]);

            $user = User::findOrFail($id);

            // Verificar que el usuario no sea ya admin
            if ($user->rol === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya es administrador'
                ], 400);
            }

            // Actualizar el rol a admin usando save() en lugar de update()
            $user->rol = 'admin';
            $user->save();

            \Log::info('Usuario convertido a admin exitosamente:', [
                'user_id' => $id,
                'rol_anterior' => $user->getOriginal('rol'),
                'rol_nuevo' => $user->rol
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario convertido en administrador exitosamente',
                'user' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al hacer admin al usuario:', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al convertir usuario en administrador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la vista de gestión de comentarios
     */
    public function comments()
    {
        try {
            // Usar el modelo Eloquent en lugar de Query Builder
            $rawCount = \App\Models\Comentarios::count();
            \Log::info('Total de comentarios en la BD (Eloquent): ' . $rawCount);

            // Usar el modelo con relaciones cargadas
            $comments = \App\Models\Comentarios::with(['usuario'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Estadísticas para el panel
            $stats = [
                'total' => $rawCount,
                'destacados' => \App\Models\Comentarios::where('destacado', true)->count(),
                'spoilers' => \App\Models\Comentarios::where('es_spoiler', true)->count(),
                'hoy' => \App\Models\Comentarios::whereDate('created_at', Carbon::today())->count(),
            ];

            \Log::info('Comentarios cargados (Eloquent): ' . $comments->count());
        } catch (\Exception $e) {
            // En caso de error, preparar datos vacíos y registrar el error
            \Log::error('Error al cargar comentarios: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());

            $comments = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1);
            $stats = [
                'total' => 0,
                'destacados' => 0,
                'spoilers' => 0,
                'hoy' => 0,
            ];
        }

        return view('admin.comments', compact('comments', 'stats'));
    }

    /**
     * Elimina un comentario de la base de datos
     * @param int $id ID del comentario a eliminar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function deleteComment($id)
    {
        try {
            $comment = DB::table('comentarios')->where('id', $id)->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comentario no encontrado'
                ], 404);
            }

            DB::table('comentarios')->where('id', $id)->delete();

            // También eliminar los likes asociados
            DB::table('likes_comentarios')->where('id_comentario', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Destaca un comentario
     * @param int $id ID del comentario a destacar
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function highlightComment($id)
    {
        try {
            $comment = DB::table('comentarios')->where('id', $id)->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comentario no encontrado'
                ], 404);
            }

            DB::table('comentarios')
                ->where('id', $id)
                ->update(['destacado' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Comentario destacado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al destacar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quita el destacado de un comentario
     * @param int $id ID del comentario a quitar el destacado
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado
     */
    public function unhighlightComment($id)
    {
        try {
            $comment = DB::table('comentarios')->where('id', $id)->first();

            if (!$comment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comentario no encontrado'
                ], 404);
            }

            DB::table('comentarios')
                ->where('id', $id)
                ->update(['destacado' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Destacado removido correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al quitar el destacado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene los datos de un usuario específico
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'status' => $user->email_verified_at !== null,
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'last_login_at' => $user->last_login_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del usuario: ' . $e->getMessage()
            ], 500);
        }
    }
}
