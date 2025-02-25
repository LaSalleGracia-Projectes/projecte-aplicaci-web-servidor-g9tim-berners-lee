<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;

class TendenciasController extends Controller
{
    public function index()
    {
        // Ejemplo: obtener los contenidos más recientes como "tendencia"
        $tendencias = PeliculasSeries::orderBy('fecha_creacion', 'desc')->take(10)->get();
        return view('tendencias', compact('tendencias'));
    }
}
