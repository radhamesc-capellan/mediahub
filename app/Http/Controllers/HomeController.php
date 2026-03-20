<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medio;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index()
    {
        $medios = Medio::with(['categoria', 'user'])->latest()->take(12)->get();
        $categorias = Categoria::withCount('medios')->get();
        
        return view('home', compact('medios', 'categorias'));
    }
}
