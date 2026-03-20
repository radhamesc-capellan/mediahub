<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('medios')->paginate(12);
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        $categoria = Categoria::create($request->all());

        ActivityLog::forModel($categoria, 'created');

        return redirect('/categorias')->with('success', 'Categoría creada exitosamente.');
    }

    public function show($id)
    {
        $categoria = Categoria::with(['medios' => function($query) {
            $query->with(['user'])->latest()->paginate(12);
        }])->findOrFail($id);
        
        return view('categorias.show', compact('categoria'));
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $id,
        ]);

        $categoria->update($request->all());

        ActivityLog::forModel($categoria, 'updated');

        return redirect('/categorias/' . $categoria->id)->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->medios()->count() > 0) {
            return redirect('/categorias')->with('error', 'No se puede eliminar una categoría con medios asociados.');
        }

        ActivityLog::forModel($categoria, 'deleted');

        $categoria->delete();

        return redirect('/categorias')->with('success', 'Categoría eliminada exitosamente.');
    }
}
