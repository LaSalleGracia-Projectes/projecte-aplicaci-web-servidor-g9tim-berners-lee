<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;

class RandomController extends Controller
{
    public function generate(Request $request)
    {
        // Aquí puedes aplicar filtros según los parámetros recibidos
        $query = PeliculasSeries::query();

        if ($request->filled('tipoContenido')) {
            $query->where('tipo', $request->tipoContenido);
        }
        // Otros filtros: género, duración, año, plataforma, etc.

        $randomMovie = $query->inRandomOrder()->first();

        // Redirigir de nuevo a la home pasando el resultado
        return redirect()->route('home')->with('randomMovie', $randomMovie);
    }
}
