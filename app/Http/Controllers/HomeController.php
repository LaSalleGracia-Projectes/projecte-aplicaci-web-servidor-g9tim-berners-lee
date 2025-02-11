<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use App\Models\Usuarios;

class HomeController extends Controller
{
    public function index()
    {
        // Ejemplo: obtener banners (puedes definirlos manualmente o a partir de otro modelo)
        $banners = []; // o, por ejemplo, Banner::all();

        // Obtener contenidos en tendencia (por ejemplo, los últimos agregados o con más valoraciones)
        $trendingMovies = PeliculasSeries::where('tipo', 'pelicula')->orderBy('fecha_creacion', 'desc')->take(5)->get();

        // Obtener críticos destacados
        $criticos = Usuario::where('rol', 'critico')->get();

        // Opcional: Obtener favoritos del usuario autenticado
        $favoritos = []; // Lógica para obtener favoritos según la sesión del usuario

        // Opcional: Cine Randomizer (puede venir de otra consulta o controlador)
        $randomMovie = PeliculasSeries::inRandomOrder()->first();

        return view('home', compact('banners', 'trendingMovies', 'criticos', 'favoritos', 'randomMovie'));
    }
}
