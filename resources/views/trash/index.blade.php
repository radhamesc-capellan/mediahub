@extends('layouts.app')

@section('title', 'Papelera de Reciclaje')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-trash3"></i> Papelera de Reciclaje</h1>
        @if($medios->total() > 0 || $categorias->total() > 0 || $users->total() > 0)
            <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('¿Estás seguro de vaciar toda la papelera? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash3-fill"></i> Vaciar Papelera
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ !request()->has('categorias') && !request()->has('users') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#medios">
                <i class="bi bi-play-circle"></i> Medios <span class="badge bg-secondary">{{ $medios->total() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ request()->has('categorias') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#categorias">
                <i class="bi bi-folder"></i> Categorías <span class="badge bg-secondary">{{ $categorias->total() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ request()->has('users') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#users">
                <i class="bi bi-people"></i> Usuarios <span class="badge bg-secondary">{{ $users->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade {{ !request()->has('categorias') && !request()->has('users') ? 'show active' : '' }}" id="medios">
            @if($medios->isEmpty())
                <div class="alert alert-info">No hay medios eliminados.</div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Usuario</th>
                                    <th>Eliminado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($medios as $medio)
                                    <tr>
                                        <td>{{ $medio->titulo }}</td>
                                        <td>{{ $medio->categoria->nombre ?? 'N/A' }}</td>
                                        <td>{{ $medio->user->name ?? 'N/A' }}</td>
                                        <td>{{ $medio->deleted_at->diffForHumans() }}</td>
                                        <td>
                                            <form action="{{ route('trash.restoreMedio', $medio->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('trash.forceDeleteMedio', $medio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar permanentemente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar permanentemente">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $medios->links() }}
                    </div>
                </div>
            @endif
        </div>

        <div class="tab-pane fade {{ request()->has('categorias') ? 'show active' : '' }}" id="categorias">
            @if($categorias->isEmpty())
                <div class="alert alert-info">No hay categorías eliminadas.</div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Medios Asociados</th>
                                    <th>Eliminado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->nombre }}</td>
                                        <td>{{ $categoria->medios_count }}</td>
                                        <td>{{ $categoria->deleted_at->diffForHumans() }}</td>
                                        <td>
                                            <form action="{{ route('trash.restoreCategoria', $categoria->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('trash.forceDeleteCategoria', $categoria->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar permanentemente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar permanentemente">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $categorias->links() }}
                    </div>
                </div>
            @endif
        </div>

        <div class="tab-pane fade {{ request()->has('users') ? 'show active' : '' }}" id="users">
            @if($users->isEmpty())
                <div class="alert alert-info">No hay usuarios eliminados.</div>
            @else
                <div class="card">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Medios</th>
                                    <th>Comentarios</th>
                                    <th>Eliminado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->medios_count }}</td>
                                        <td>{{ $user->comentarios_count }}</td>
                                        <td>{{ $user->deleted_at->diffForHumans() }}</td>
                                        <td>
                                            <form action="{{ route('trash.restoreUser', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Restaurar">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('trash.forceDeleteUser', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar permanentemente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar permanentemente">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
