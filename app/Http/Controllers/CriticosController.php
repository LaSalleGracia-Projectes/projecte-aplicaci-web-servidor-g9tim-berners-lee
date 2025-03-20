<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CriticosController extends Controller
{
    public function index()
    {
        // Get all critics based on the 'rol' field in the users table
        $topCritics = User::where('rol', 'critico')
            ->select('id', 'name', 'foto_perfil', 'biografia', 'rol', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // For each critic, add some calculated fields
        foreach ($topCritics as $critic) {
            // Use the name field from the database
            $critic->nombre_usuario = $critic->name;

            // Add placeholder values for fields not in the database
            $critic->total_resenas = rand(5, 50);  // Random number for demo
            $critic->calificacion = rand(35, 50) / 10;  // Random rating between 3.5-5.0
            $critic->seguidores = rand(10, 500);  // Random number of followers

            // Generate some random specialties
            $genres = ['Drama', 'Comedia', 'Acción', 'Terror', 'Ciencia Ficción', 'Documental', 'Animación'];
            shuffle($genres);
            $critic->especialidad = implode(',', array_slice($genres, 0, rand(1, 3)));

            // Set verified status (for demo)
            $critic->verificado = (rand(0, 1) == 1);
        }

        // Create sample trending reviews
        $trendingReviews = $this->getDummyReviews($topCritics);

        return view('criticos', compact('topCritics', 'trendingReviews'));
    }

    private function getDummyReviews($critics)
    {
        $dummyData = [];
        $movieTitles = [
            'El Secreto de sus Ojos', 'Interestelar', 'Pulp Fiction',
            'El Padrino', 'Matrix', 'El Señor de los Anillos',
            'La La Land', 'Parásitos', 'Joker', 'Avengers: Endgame'
        ];

        foreach ($critics as $index => $critic) {
            if ($index >= 10)break;

            $review = new \stdClass();
            $review->id = $index + 1;
            $review->contenido = "Esta es una reseña de ejemplo.";
            $review->calificacion = rand(30, 50) / 10;
            $review->created_at = now()->subDays(rand(1, 30));
            $review->likes = rand(10, 200);
            $review->vistas = rand(100, 1000);

            // Associate with critic
            $review->usuario = $critic;

            // Movie info
            $pelicula = new \stdClass();
            $pelicula->id = $index + 1;
            $pelicula->titulo = $movieTitles[$index];
            $pelicula->fecha_lanzamiento = now()->subYears(rand(0, 5))->format('Y-m-d');
            $review->pelicula = $pelicula;

            // Add empty comments collection
            $review->comentarios = collect([]);
            for ($j = 1; $j <= rand(0, 5); $j++) {
                $review->comentarios->push(new \stdClass());
            }

            $dummyData[] = $review;
        }

        return collect($dummyData);
    }
}
