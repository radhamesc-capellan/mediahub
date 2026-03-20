<?php

namespace App\Http\Controllers;

use App\Models\Medio;
use App\Models\Categoria;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedioController extends Controller
{
    public function index()
    {
        $medios = Medio::with(['categoria', 'user'])->withCount('favoritos')->latest()->paginate(12);
        return view('medios.index', compact('medios'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('medios.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:url,upload,embed,panorama',
            'archivo' => 'nullable|string|max:500',
            'archivo_file' => 'nullable|file|max:102400',
            'archivo_embed' => 'nullable|string|max:500',
            'archivo_panorama' => 'nullable|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $archivo = $request->archivo;
        $tipo = $request->tipo;

        if ($request->tipo === 'upload' && $request->hasFile('archivo_file')) {
            $file = $request->file('archivo_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->store('medios', 'public');
            $archivo = '/storage/' . $path;
            
            if (in_array($extension, ['mp4', 'webm', 'avi', 'mov', 'mkv'])) {
                $tipo = 'video';
            } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'flac'])) {
                $tipo = 'audio';
            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $tipo = 'image';
            }
        } elseif ($request->tipo === 'embed' && $request->archivo_embed) {
            $archivo = $request->archivo_embed;
        } elseif ($request->tipo === 'panorama' && $request->archivo_panorama) {
            $archivo = $request->archivo_panorama;
        }

        if (empty($archivo)) {
            return back()->with('error', 'Debes proporcionar un archivo o URL.');
        }

        $medio = Medio::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $tipo,
            'archivo' => $archivo,
            'categoria_id' => $request->categoria_id,
            'user_id' => Auth::id(),
        ]);

        ActivityLog::forModel($medio, 'created');

        return redirect('/medios')->with('success', 'Medio creado exitosamente.');
    }

    public function show($id)
    {
        $medio = Medio::with(['categoria', 'user', 'comentarios.user', 'favoritos'])->withCount('favoritos')->findOrFail($id);
        return view('medios.show', compact('medio'));
    }

    public function edit($id)
    {
        $medio = Medio::findOrFail($id);
        
        if ($medio->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect('/medios')->with('error', 'No tienes permiso para editar este medio.');
        }
        
        $categorias = Categoria::all();
        return view('medios.edit', compact('medio', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $medio = Medio::findOrFail($id);
        
        if ($medio->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect('/medios')->with('error', 'No tienes permiso para editar este medio.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:url,upload,embed',
            'archivo' => 'required|string|max:500',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $archivo = $request->archivo;

        if ($request->tipo === 'upload' && $request->hasFile('archivo_file')) {
            if ($medio->archivo && str_starts_with($medio->archivo, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $medio->archivo));
            }
            $file = $request->file('archivo_file');
            $path = $file->store('medios', 'public');
            $archivo = '/storage/' . $path;
        }

        $medio->update([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo' => $request->tipo,
            'archivo' => $archivo,
            'categoria_id' => $request->categoria_id,
        ]);

        ActivityLog::forModel($medio, 'updated');

        return redirect('/medios/' . $medio->id)->with('success', 'Medio actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $medio = Medio::findOrFail($id);
        
        if ($medio->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect('/medios')->with('error', 'No tienes permiso para eliminar este medio.');
        }

        if ($medio->tipo === 'upload' && $medio->archivo && str_starts_with($medio->archivo, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $medio->archivo));
        }

        ActivityLog::forModel($medio, 'deleted');

        $medio->delete();

        return redirect('/medios')->with('success', 'Medio eliminado exitosamente.');
    }
}
