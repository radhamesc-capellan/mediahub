<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Http\Resources\ComentarioResource;
use Illuminate\Http\Request;

class ComentarioApiController extends Controller
{
    public function index(Request $request, $medioId)
    {
        $request->validate([
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $comentarios = Comentario::where('medio_id', $medioId)
            ->with('user')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return ComentarioResource::collection($comentarios);
    }

    public function store(Request $request, $medioId)
    {
        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $comentario = Comentario::create([
            'contenido' => $request->contenido,
            'medio_id' => $medioId,
            'user_id' => $request->user()->id,
        ]);

        return (new ComentarioResource($comentario->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function show($medioId, $id)
    {
        $comentario = Comentario::where('medio_id', $medioId)
            ->with('user')
            ->findOrFail($id);

        return new ComentarioResource($comentario);
    }

    public function update(Request $request, $medioId, $id)
    {
        $comentario = Comentario::where('medio_id', $medioId)->findOrFail($id);

        if ($comentario->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'contenido' => 'required|string|max:1000',
        ]);

        $comentario->update(['contenido' => $request->contenido]);

        return (new ComentarioResource($comentario->load('user')))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request, $medioId, $id)
    {
        $comentario = Comentario::where('medio_id', $medioId)->findOrFail($id);

        if ($comentario->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comentario->delete();

        return response()->json(null, 204);
    }
}
