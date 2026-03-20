@extends('layouts.app')

@section('title', 'Dashboard - MediaHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-speedometer2"></i> Dashboard
        </h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Volver al Inicio
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Medios</h6>
                            <h2 class="mb-0" data-counter="{{ $stats['total_medios'] }}">{{ $stats['total_medios'] }}</h2>
                        </div>
                        <i class="bi bi-collection-play display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Categorías</h6>
                            <h2 class="mb-0" data-counter="{{ $stats['total_categorias'] }}">{{ $stats['total_categorias'] }}</h2>
                        </div>
                        <i class="bi bi-folder display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Comentarios</h6>
                            <h2 class="mb-0" data-counter="{{ $stats['total_comentarios'] }}">{{ $stats['total_comentarios'] }}</h2>
                        </div>
                        <i class="bi bi-chat-left-text display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Mis Medios</h6>
                            <h2 class="mb-0" data-counter="{{ $stats['mis_medios'] }}">{{ $stats['mis_medios'] }}</h2>
                        </div>
                        <i class="bi bi-person-video display-4 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-collection-play"></i> Últimos Medios
                    </h5>
                </div>
                <div class="card-body">
                    @if($recent_medios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoría</th>
                                    <th>Autor</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_medios as $medio)
                                <tr>
                                    <td>
                                        <a href="{{ route('medios.show', $medio->id) }}">
                                            {{ Str::limit($medio->titulo, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $medio->categoria->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $medio->user->name ?? 'N/A' }}</td>
                                    <td>{{ $medio->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('medios.show', $medio->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">No hay medios registrados.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-video"></i> Mis Medios
                    </h5>
                </div>
                <div class="card-body">
                    @if($mis_medios->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($mis_medios as $medio)
                        <a href="{{ route('medios.show', $medio->id) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ Str::limit($medio->titulo, 25) }}
                            <span class="badge bg-primary rounded-pill">
                                <i class="bi bi-chat"></i> {{ optional($medio->comentarios)->count() ?? 0 }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">No has creado medios aún.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-lightning"></i> Acciones Rápidas
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('medios.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Crear Nuevo Medio
                        </a>
                        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-folder"></i> Gestionar Categorías
                        </a>
                        <a href="{{ route('medios.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-collection-play"></i> Ver Todos los Medios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
