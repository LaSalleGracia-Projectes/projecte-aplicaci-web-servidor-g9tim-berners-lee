<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;

class CriticosController extends Controller
{
    public function index()
    {
        $criticos = Usuarios::where('rol', 'critico')->get();
        return view('criticos', compact('criticos'));
    }
}
