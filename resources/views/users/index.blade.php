@extends('layouts.app')

@section('title', 'Gestionar Usuarios - MediaHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-people"></i> Gestionar Usuarios</h1>
        <span class="badge bg-primary fs-6">{{ $users->total() }} usuarios</span>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th class="text-center">Medios</th>
                            <th class="text-center">Comentarios</th>
                            <th>Registrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->getAvatarUrl() }}" 
                                         alt="{{ $user->name }}"
                                         class="rounded-circle me-2"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-secondary ms-1">Tú</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'editor' ? 'primary' : 'secondary') }}">
                                    {{ $user->getRoleLabel() }}
                                </span>
                            </td>
                            <td class="text-center">{{ $user->medios_count }}</td>
                            <td class="text-center">{{ $user->comentarios_count }}</td>
                            <td>
                                <small class="text-muted-custom">{{ $user->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-center">
                                @if($user->id !== auth()->id())
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.edit', $user->id) }}" 
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-people display-4 text-muted"></i>
                                <p class="text-muted mt-2">No hay usuarios registrados.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Roles del Sistema</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <span class="badge bg-danger mb-2">Administrador</span>
                        <ul class="mb-0 small">
                            <li>Gestionar usuarios</li>
                            <li>CRUD completo de medios</li>
                            <li>CRUD de categorías</li>
                            <li>Moderar comentarios</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <span class="badge bg-primary mb-2">Editor</span>
                        <ul class="mb-0 small">
                            <li>Crear y editar medios</li>
                            <li>Gestionar categorías</li>
                            <li>Ver dashboard</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded">
                        <span class="badge bg-secondary mb-2">Espectador</span>
                        <ul class="mb-0 small">
                            <li>Ver medios</li>
                            <li>Comentar</li>
                            <li>Ver perfil propio</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
