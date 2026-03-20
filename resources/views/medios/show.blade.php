@extends('layouts.app')

@section('title', $medio->titulo . ' - MediaHub')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('medios.index') }}">Medios</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($medio->titulo, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <span class="badge bg-secondary mb-2">{{ $medio->categoria->nombre ?? 'Sin categoría' }}</span>
                    <h1 class="card-title display-6">{{ $medio->titulo }}</h1>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-muted">
                            <i class="bi bi-person"></i> {{ $medio->user->name ?? 'Desconocido' }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-calendar"></i> {{ $medio->created_at->format('d M Y') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-heart-fill text-danger"></i> <span id="favoritos-count">{{ $medio->favoritos_count }}</span>
                        </div>
                        @auth
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm {{ Auth::user()->hasFavorited($medio) ? 'btn-danger' : 'btn-outline-danger' }} btn-like" 
                                    onclick="toggleFavorito({{ $medio->id }})"
                                    id="btn-favorito">
                                <i class="bi bi-heart{{ Auth::user()->hasFavorited($medio) ? '-fill' : '' }}"></i>
                                <span id="btn-favorito-text">{{ Auth::user()->hasFavorited($medio) ? 'Favorito' : 'Me gusta' }}</span>
                            </button>
                            @if($medio->user_id === Auth::id())
                            <a href="{{ route('medios.edit', $medio->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <form action="{{ route('medios.destroy', $medio->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('¿Estás seguro de eliminar este medio?')">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                        @endauth
                    </div>
                    
                    @if($medio->archivo)
                    <div class="mb-4">
                        @if($medio->tipo === 'embed' && $medio->embed_url)
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ $medio->embed_url }}" frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen></iframe>
                        </div>
                        @elseif($medio->tipo === 'video' || $medio->tipo === 'upload')
                        <div class="ratio ratio-16x9 bg-dark rounded">
                            <video controls class="w-100 h-100" style="object-fit: contain;">
                                <source src="{{ $medio->archivo }}" type="video/mp4">
                                Tu navegador no soporta el elemento de video.
                            </video>
                        </div>
                        @elseif($medio->tipo === 'audio')
                        <div class="rounded p-4 text-center" style="background-color: var(--bg-tertiary);">
                            <i class="bi bi-music-note display-1 text-primary"></i>
                            <audio controls class="w-100 mt-3">
                                <source src="{{ $medio->archivo }}">
                                Tu navegador no soporta audio.
                            </audio>
                        </div>
                        @elseif($medio->tipo === 'panorama')
                        <div class="ratio ratio-16x9 bg-dark rounded overflow-hidden" id="vr-viewer">
                            <a-scene embedded vr-mode-ui="enabled: true" cursor="rayOrigin: mouse">
                                <a-sky src="{{ $medio->archivo }}"></a-sky>
                                <a-entity camera look-controls position="0 1.6 0">
                                    <a-cursor color="#0d6efd" fuse="false" raycaster="objects: .clickable"></a-cursor>
                                </a-entity>
                                
                                <a-entity id="vr-controls" position="0 2.8 -4">
                                    <a-box position="-0.6 0 0" width="0.3" height="0.3" depth="0.05" 
                                           color="#dc3545" class="clickable vr-btn"
                                           event-set__mouseenter="material.emissive: #8a1a29"
                                           event-set__mouseleave="material.emissive: #000">
                                        <a-text value="✕" position="0 0 0.03" align="center" color="#fff" width="2"></a-text>
                                    </a-box>
                                    <a-box position="0 0 0" width="0.3" height="0.3" depth="0.05" 
                                           color="#198754" class="clickable vr-btn"
                                           event-set__mouseenter="material.emissive: #0a5230"
                                           event-set__mouseleave="material.emissive: #000">
                                        <a-text value="♥" position="0 0 0.03" align="center" color="#fff" width="2"></a-text>
                                    </a-box>
                                    <a-box position="0.6 0 0" width="0.3" height="0.3" depth="0.05" 
                                           color="#0d6efd" class="clickable vr-btn"
                                           event-set__mouseenter="material.emissive: #0046b3"
                                           event-set__mouseleave="material.emissive: #000">
                                        <a-text value="⟳" position="0 0 0.03" align="center" color="#fff" width="2"></a-text>
                                    </a-box>
                                </a-entity>
                                
                                <a-entity id="vr-info" position="0 2.2 -4">
                                    <a-plane width="2" height="0.4" color="#212529" opacity="0.8"></a-plane>
                                    <a-text value="{{ Str::limit($medio->titulo, 30) }}" position="0 0.08 0.01" align="center" color="#fff" width="5"></a-text>
                                    <a-text value="Clic en ♥ para favorito" position="0 -0.08 0.01" align="center" color="#aaa" width="4"></a-text>
                                </a-entity>
                            </a-scene>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Controles:</strong> Arrastra para rotar | WASD mover | Botones VR para acciones
                        </div>
                        @elseif($medio->tipo === 'image' || $medio->isImage())
                        <div class="text-center">
                            <a href="{{ $medio->archivo }}" data-fancybox="gallery" data-caption="{{ $medio->titulo }}">
                                <img src="{{ $medio->archivo }}" alt="{{ $medio->titulo }}" class="img-fluid rounded lightbox">
                            </a>
                        </div>
                        @else
                        <div class="text-center">
                            <a href="{{ $medio->archivo }}" class="btn btn-primary btn-lg" target="_blank" download>
                                <i class="bi bi-download"></i> Descargar Archivo
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    @if($medio->descripcion)
                    <div class="card-text">
                        <h5>Descripción</h5>
                        <p class="lead">{{ $medio->descripcion }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> Comentarios ({{ $medio->comentarios->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @auth
                    <form method="POST" action="{{ route('comentarios.store', $medio->id) }}" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <label for="contenido" class="form-label">Agregar un comentario</label>
                            <textarea class="form-control @error('contenido') is-invalid @enderror" 
                                      id="contenido" name="contenido" rows="3" required
                                      placeholder="Escribe tu comentario...">{{ old('contenido') }}</textarea>
                            @error('contenido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send"></i> Publicar Comentario
                        </button>
                    </form>
                    @else
                    <div class="alert alert-info mb-4">
                        <a href="{{ route('login') }}">Inicia sesión</a> para poder comentar.
                    </div>
                    @endauth

                    <div class="comentarios">
                        @forelse($medio->comentarios->sortByDesc('created_at') as $comentario)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    <i class="bi bi-person-circle"></i> {{ $comentario->user->name ?? 'Usuario' }}
                                </strong>
                                <small class="text-muted">{{ $comentario->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-2 mt-2">{{ $comentario->contenido }}</p>
                            @auth
                            @if($comentario->user_id === Auth::id())
                            <form action="{{ route('comentarios.destroy', $comentario->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('¿Eliminar este comentario?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                            @endauth
                        </div>
                        @empty
                        <p class="text-muted text-center">No hay comentarios aún. ¡Sé el primero!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i> Categoría
                    </h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('categorias.show', $medio->categoria->id ?? 0) }}" class="text-decoration-none">
                        <span class="badge bg-primary fs-6">
                            {{ $medio->categoria->nombre ?? 'Sin categoría' }}
                        </span>
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-share"></i> Compartir
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted-custom">Copia el enlace para compartir:</p>
                    <input type="text" class="form-control" readonly value="{{ url()->current() }}">
                </div>
            </div>
        </div>
    </div>
</div>

@if($medio->tipo === 'panorama')
<script src="https://aframe.io/releases/1.6.0/aframe.min.js"></script>
<script>
document.querySelectorAll('.vr-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var text = this.querySelector('a-text').getAttribute('value');
        if (text === '♥') {
            toggleFavorito({{ $medio->id }});
        } else if (text === '✕') {
            window.history.back();
        } else if (text === '⟳') {
            location.reload();
        }
    });
});
</script>
@endif

@if($medio->tipo === 'image' || $medio->isImage())
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
@endif
@endsection
