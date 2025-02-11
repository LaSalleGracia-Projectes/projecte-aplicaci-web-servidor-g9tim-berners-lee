<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;

class PeliculasController extends Controller
{
    public function index()
    {
        $peliculas = PeliculasSeries::where('tipo', 'pelicula')->get();
        return view('peliculas', compact('peliculas'));
    }
}
