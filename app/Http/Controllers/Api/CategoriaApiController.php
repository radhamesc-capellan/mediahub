<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use Illuminate\Http\Request;

class CategoriaApiController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::withCount('medios')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return CategoriaResource::collection($categorias);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        $categoria = Categoria::create($request->only('nombre'));

        return (new CategoriaResource($categoria))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $categoria = Categoria::with(['medios' => function($query) {
            $query->with(['user'])->latest()->paginate(12);
        }])->findOrFail($id);

        return new CategoriaResource($categoria);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $id,
        ]);

        $categoria->update($request->only('nombre'));

        return (new CategoriaResource($categoria))
            ->response()
            ->setStatusCode(200);
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->medios()->count() > 0) {
            return response()->json(['error' => 'No se puede eliminar una categoría con medios asociados'], 422);
        }

        $categoria->delete();

        return response()->json(null, 204);
    }
}
