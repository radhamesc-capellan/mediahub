<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medio;
use App\Http\Resources\MedioResource;
use Illuminate\Http\Request;

class MedioApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Medio::with(['categoria', 'user', 'comentarios']);

        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $medios = $query->latest()->paginate($request->get('per_page', 15));

        return MedioResource::collection($medios);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:url,upload,embed',
            'archivo' => 'required|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $medio = Medio::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'archivo' => $request->archivo,
            'categoria_id' => $request->categoria_id,
            'user_id' => $request->user()->id,
        ]);

        return (new MedioResource($medio->load(['categoria', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $medio = Medio::with(['categoria', 'user', 'comentarios.user'])->findOrFail($id);
        return new MedioResource($medio);
    }

    public function update(Request $request, $id)
    {
        $medio = Medio::findOrFail($id);

        if ($medio->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:url,upload,embed',
            'archivo' => 'required|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $medio->update($request->only(['titulo', 'descripcion', 'tipo', 'archivo', 'categoria_id']));

        return (new MedioResource($medio->load(['categoria', 'user'])))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request, $id)
    {
        $medio = Medio::findOrFail($id);

        if ($medio->user_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $medio->delete();

        return response()->json(null, 204);
    }
}
