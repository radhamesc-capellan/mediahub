<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Medio;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FavoritoController extends Controller
{
    public function index()
    {
        $favoritos = auth()->user()->mediosFavoritos()
            ->with(['categoria', 'user'])
            ->latest()
            ->paginate(12);

        return view('favoritos.index', compact('favoritos'));
    }

    public function toggle(Medio $medio)
    {
        $user = auth()->user();
        $favorito = $user->favoritos()->where('medio_id', $medio->id)->first();

        if ($favorito) {
            $favorito->delete();
            $liked = false;
        } else {
            $user->favoritos()->create(['medio_id' => $medio->id]);
            $liked = true;
        }

        $count = $medio->favoritos()->count();

        if (request()->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'count' => $count
            ]);
        }

        return back()->with('success', $liked ? 'Medio agregado a favoritos.' : 'Medio removido de favoritos.');
    }

    public function destroy(Medio $medio)
    {
        auth()->user()->favoritos()->where('medio_id', $medio->id)->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('favoritos.index')->with('success', 'Medio removido de favoritos.');
    }
}
