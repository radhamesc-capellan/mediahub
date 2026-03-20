@extends('layouts.app')

@section('title', $categoria->nombre . ' - MediaHub')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
            <li class="breadcrumb-item active">{{ $categoria->nombre }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-folder"></i> {{ $categoria->nombre }}
            <span class="badge bg-secondary ms-2">{{ $categoria->medios->count() }} medios</span>
        </h1>
        @auth
        <div>
            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
        @endauth
    </div>

    @if($categoria->medios->count() > 0)
    <div class="row">
        @foreach($categoria->medios as $medio)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm card-media">
                <div class="card-body">
                    <h5 class="card-title">{{ $medio->titulo }}</h5>
                    <p class="card-text text-muted-custom">{{ Str::limit($medio->descripcion, 80) }}</p>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center small">
                        <span>
                            <i class="bi bi-person"></i> {{ $medio->user->name ?? 'Desconocido' }}
                        </span>
                        <span>
                            {{ $medio->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <div class="card-footer border-0">
                    <a href="{{ route('medios.show', $medio->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-eye"></i> Ver Detalles
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No hay medios en esta categoría.
        @auth
        <br><a href="{{ route('medios.create') }}" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle"></i> Crear un Medio
        </a>
        @endauth
    </div>
    @endif
</div>
@endsection
