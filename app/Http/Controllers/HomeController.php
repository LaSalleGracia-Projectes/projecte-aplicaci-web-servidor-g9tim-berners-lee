<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {

        $banners = [];

        // Obtener contenidos en tendencia (por ejemplo, los últimos agregados o con más valoraciones)
        $trendingMovies = PeliculasSeries::where('tipo', 'pelicula')->orderBy('año_estreno', 'desc')->take(5)->get();

        // Obtener críticos destacados
        $criticos = User::where('rol', 'critico')->get();

        // Opcional: Obtener favoritos del usuario autenticado
        $favoritos = []; // Lógica para obtener favoritos según la sesión del usuario

        // Opcional: Cine Randomizer (puede venir de otra consulta o controlador)
        $randomMovie = PeliculasSeries::inRandomOrder()->first();

        return view('home', compact('banners', 'trendingMovies', 'criticos', 'favoritos', 'randomMovie'));
    }
}
