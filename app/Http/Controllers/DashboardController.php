<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medio;
use App\Models\Categoria;
use App\Models\Comentario;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'total_medios' => Medio::count(),
            'total_categorias' => Categoria::count(),
            'total_comentarios' => Comentario::count(),
            'mis_medios' => $user ? Medio::where('user_id', $user->id)->count() : 0,
        ];
        
        $recent_medios = Medio::with(['categoria', 'user', 'comentarios'])
            ->latest()
            ->take(6)
            ->get();
        
        $mis_medios = Medio::where('user_id', $user ? $user->id : 0)
            ->with(['categoria', 'comentarios'])
            ->latest()
            ->take(6)
            ->get();
        
        return view('dashboard', compact('stats', 'recent_medios', 'mis_medios'));
    }
}
