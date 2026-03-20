@extends('layouts.app')

@section('title', 'Categorías - MediaHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-folder"></i> Categorías
        </h1>
        @auth
        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Categoría
        </a>
        @endauth
    </div>

    @if($categorias->count() > 0)
    <div class="row">
        @foreach($categorias as $categoria)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm card-media">
                <div class="card-body text-center">
                    <i class="bi bi-folder2-open display-4 text-primary mb-3"></i>
                    <h5 class="card-title">{{ $categoria->nombre }}</h5>
                    <p class="text-muted-custom">
                        {{ $categoria->medios_count }} medio{{ $categoria->medios_count != 1 ? 's' : '' }}
                    </p>
                </div>
                <div class="card-footer border-0">
                    <a href="{{ route('categorias.show', $categoria->id) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="bi bi-eye"></i> Ver Medios
                    </a>
                    @auth
                    <div class="d-flex gap-2">
                        <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <form action="{{ route('categorias.destroy', $categoria->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                    onclick="return confirm('¿Eliminar esta categoría?')"
                                    {{ $categoria->medios_count > 0 ? 'disabled title=Primero elimina los medios asociados' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $categorias->links() }}
    </div>
    @else
    <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No hay categorías disponibles.
        @auth
        <br><a href="{{ route('categorias.create') }}" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle"></i> Crear Primera Categoría
        </a>
        @endauth
    </div>
    @endif
</div>
@endsection
