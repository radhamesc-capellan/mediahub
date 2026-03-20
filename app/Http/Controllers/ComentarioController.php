<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Medio;
use App\Notifications\NuevoComentarioNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioController extends Controller
{
    public function store(Request $request, $medio_id)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $medio = Medio::findOrFail($medio_id);
        
        $comentario = Comentario::create([
            'contenido' => $request->contenido,
            'medio_id' => $medio_id,
            'user_id' => Auth::id(),
        ]);

        $comentario->load('user');

        if ($medio->user_id !== Auth::id()) {
            $medio->user->notify(new NuevoComentarioNotification($comentario));
        }

        if ($request->expectsJson()) {
            return response()->json($comentario, 201);
        }

        return back()->with('success', 'Comentario publicado exitosamente.');
    }

    public function index($medio_id)
    {
        $comentarios = Comentario::where('medio_id', $medio_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($comentarios);
    }

    public function destroy(Request $request, $id)
    {
        $comentario = Comentario::findOrFail($id);
        
        if ($comentario->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            return back()->with('error', 'No tienes permiso para eliminar este comentario.');
        }
        
        $comentario->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', 'Comentario eliminado exitosamente.');
    }
}
