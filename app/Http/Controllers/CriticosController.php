<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CriticosController extends Controller
{
    public function index()
    {
        $criticos = User::where('rol', 'critico')->get();
        return view('criticos', compact('criticos'));
    }
}
