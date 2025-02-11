<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeliculasSeries;

class SeriesController extends Controller
{
    public function index()
    {
        $series = PeliculasSeries::where('tipo', 'serie')->get();
        return view('series', compact('series'));
    }
}
