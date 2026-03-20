@extends('layouts.app')

@section('title', 'Inicio - MediaHub')

@section('content')
<div class="container">
    <section class="hero-section rounded-3 mb-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3 animate-fadeInUp">
                <i class="bi bi-collection-play"></i> Bienvenido a MediaHub
            </h1>
            <p class="lead mb-4 animate-fadeInUp stagger-1">Tu plataforma multimedia para gestionar y compartir contenido</p>
            @guest
            <div class="animate-fadeInUp stagger-2">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg me-2">
                    <i class="bi bi-person-plus"></i> Regístrate Gratis
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </a>
            </div>
            @else
            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg animate-fadeInUp stagger-2">
                <i class="bi bi-speedometer2"></i> Ir al Dashboard
            </a>
            @endguest
        </div>
    </section>

    <section class="categorias mb-5">
        <h2 class="mb-4 reveal">
            <i class="bi bi-folder"></i> Categorías
        </h2>
        <div class="row">
            @forelse($categorias as $categoria)
            <div class="col-md-4 col-lg-3 mb-3 reveal">
                <a href="{{ route('categorias.show', $categoria->id) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm card-media">
                        <div class="card-body text-center">
                            <i class="bi bi-folder2-open display-4 text-primary mb-2"></i>
                            <h5 class="card-title">{{ $categoria->nombre }}</h5>
                            <p class="text-muted-custom small">{{ $categoria->medios_count }} medios</p>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted text-center">No hay categorías disponibles.</p>
            </div>
            @endforelse
        </div>
    </section>

    <section class="medios-recientes">
        <h2 class="mb-4 reveal">
            <i class="bi bi-clock-history"></i> Medios Recientes
        </h2>
        <div class="row">
            @forelse($medios as $medio)
            <div class="col-md-6 col-lg-4 mb-4 reveal">
                <div class="card h-100 shadow-sm card-media">
                    <div class="card-body">
                        <span class="badge bg-secondary mb-2">{{ $medio->categoria->nombre ?? 'Sin categoría' }}</span>
                        <h5 class="card-title">{{ $medio->titulo }}</h5>
                        <p class="card-text text-muted-custom">{{ Str::limit($medio->descripcion, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted-custom">
                                <i class="bi bi-person"></i> {{ $medio->user->name ?? 'Desconocido' }}
                            </small>
                            <small class="text-muted-custom">
                                {{ $medio->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer border-0">
                        <a href="{{ route('medios.show', $medio->id) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    No hay medios disponibles. ¡Sé el primero en subir uno!
                </div>
                @auth
                <div class="text-center">
                    <a href="{{ route('medios.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Crear Medio
                    </a>
                </div>
                @endauth
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-4 reveal">
            <a href="{{ route('medios.index') }}" class="btn btn-outline-primary">
                Ver Todos los Medios <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>
</div>
@endsection
