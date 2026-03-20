@extends('layouts.app')

@section('title', 'Mis Favoritos - MediaHub')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-heart-fill text-danger"></i> Mis Favoritos
        </h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-house"></i> Volver al Inicio
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($favoritos->count() > 0)
    <div class="row g-2 g-md-3">
        @foreach($favoritos as $medio)
        <div class="col-6 col-md-6 col-lg-4 mb-3">
            <div class="card h-100 shadow-sm card-media">
                <a href="{{ route('medios.show', $medio->id) }}" class="text-decoration-none">
                    @if($medio->tipo === 'embed' && $medio->embed_url)
                    @php
                        $videoId = '';
                        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $medio->archivo, $m)) $videoId = $m[1];
                        elseif (preg_match('/youtu\.be\/([^\?]+)/', $medio->archivo, $m)) $videoId = $m[1];
                    @endphp
                    @if($videoId)
                    <div class="position-relative" style="height: 120px;">
                        <img src="https://img.youtube.com/vi/{{ $videoId }}/mqdefault.jpg" 
                             class="card-img-top h-100 w-100" 
                             style="object-fit: cover;"
                             alt="{{ $medio->titulo }}"
                             loading="lazy"
                             onerror="this.style.display='none'">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <i class="bi bi-youtube text-white" style="font-size: 2.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);"></i>
                        </div>
                    </div>
                    @endif
                    @elseif($medio->isImage())
                    <div class="position-relative" style="height: 120px; overflow: hidden;">
                        <img src="{{ $medio->archivo }}" 
                             class="card-img-top h-100 w-100" 
                             style="object-fit: cover;" 
                             alt="{{ $medio->titulo }}"
                             loading="lazy">
                    </div>
                    @elseif($medio->isAudio())
                    <div class="d-flex align-items-center justify-content-center" style="height: 120px;">
                        <i class="bi bi-music-note text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center" style="height: 120px; background: var(--bg-tertiary);">
                        <i class="bi bi-play-circle-fill text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    @endif
                </a>
                <div class="card-body p-2 p-md-3">
                    <span class="badge bg-secondary mb-1" style="font-size: 0.65rem;">{{ $medio->categoria->nombre ?? 'Sin categoría' }}</span>
                    <h6 class="card-title mb-1 text-truncate">
                        <a href="{{ route('medios.show', $medio->id) }}" class="text-decoration-none">
                            {{ $medio->titulo }}
                        </a>
                    </h6>
                    <p class="card-text text-muted small mb-2 d-none d-sm-block">{{ Str::limit($medio->descripcion, 60) }}</p>
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span class="text-truncate me-1">
                            <i class="bi bi-person"></i> {{ $medio->user->name ?? 'Usuario' }}
                        </span>
                        <span>
                            <i class="bi bi-heart-fill text-danger"></i> {{ $medio->favoritos_count }}
                        </span>
                    </div>
                </div>
                <div class="card-footer border-0">
                    <form action="{{ route('favoritos.destroy', $medio->id) }}" method="POST" class="d-inline w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-heart-fill"></i> Quitar de Favoritos
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $favoritos->links('pagination::bootstrap-5') }}
    </div>
    @else
    <div class="alert alert-info text-center">
        <i class="bi bi-heart fs-1"></i>
        <p class="mt-2 mb-0">No tienes medios favoritos aún.</p>
        <a href="{{ route('medios.index') }}" class="btn btn-primary mt-3">
            <i class="bi bi-collection"></i> Explorar Medios
        </a>
    </div>
    @endif
</div>
@endsection
