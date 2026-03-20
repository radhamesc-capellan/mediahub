<?php

namespace App\Http\Controllers;

use App\Models\Medio;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $medios = Medio::onlyTrashed()->with(['categoria', 'user'])->paginate(15, ['*'], 'medios');
        $categorias = Categoria::onlyTrashed()->withCount('medios')->paginate(15, ['*'], 'categorias');
        $users = User::onlyTrashed()->withCount(['medios', 'comentarios'])->paginate(15, ['*'], 'users');

        return view('trash.index', compact('medios', 'categorias', 'users'));
    }

    public function restoreMedio($id)
    {
        $medio = Medio::onlyTrashed()->findOrFail($id);
        $medio->restore();

        return redirect()->back()->with('success', 'Medio restaurado correctamente.');
    }

    public function forceDeleteMedio($id)
    {
        $medio = Medio::onlyTrashed()->findOrFail($id);
        
        if ($medio->archivo && file_exists(public_path('storage/medios/' . $medio->archivo))) {
            unlink(public_path('storage/medios/' . $medio->archivo));
        }
        
        $medio->forceDelete();

        return redirect()->back()->with('success', 'Medio eliminado permanentemente.');
    }

    public function restoreCategoria($id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);
        $categoria->restore();

        return redirect()->back()->with('success', 'Categoría restaurada correctamente.');
    }

    public function forceDeleteCategoria($id)
    {
        $categoria = Categoria::onlyTrashed()->findOrFail($id);
        $categoria->forceDelete();

        return redirect()->back()->with('success', 'Categoría eliminada permanentemente.');
    }

    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', 'Usuario restaurado correctamente.');
    }

    public function forceDeleteUser($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        if ($user->avatar && file_exists(public_path('storage/avatars/' . $user->avatar))) {
            unlink(public_path('storage/avatars/' . $user->avatar));
        }
        
        $user->forceDelete();

        return redirect()->back()->with('success', 'Usuario eliminado permanentemente.');
    }

    public function emptyTrash()
    {
        $medios = Medio::onlyTrashed()->get();
        foreach ($medios as $medio) {
            if ($medio->archivo && file_exists(public_path('storage/medios/' . $medio->archivo))) {
                unlink(public_path('storage/medios/' . $medio->archivo));
            }
        }
        Medio::onlyTrashed()->forceDelete();
        Categoria::onlyTrashed()->forceDelete();
        User::onlyTrashed()->forceDelete();

        return redirect()->route('trash.index')->with('success', 'Papelera vaciada completamente.');
    }
}
