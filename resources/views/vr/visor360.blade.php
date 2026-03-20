@extends('layouts.app')

@section('title', 'Visor 360° - MediaHub')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-sphere"></i> Visor 360°</h2>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            @if(isset($medio))
                <div id="vr-container" style="height: 500px; background: #1a1a2e;">
                    <a-scene embedded vr-mode-ui="enabled: true" loading-screen="backgroundColor: #1a1a2e; dotsColor: #0d6efd">
                        @if($medio->archivo)
                            <a-sky src="{{ $medio->archivo }}"></a-sky>
                        @else
                            <a-sky color="#2d3436"></a-sky>
                        @endif
                        <a-text value="{{ $medio->titulo }}" position="0 2 -3" align="center" color="#fff" width="6"></a-text>
                        <a-text value="{{ Str::limit($medio->descripcion, 100) }}" position="0 1 -3" align="center" color="#aaa" width="4"></a-text>
                        <a-entity camera look-controls position="0 1.6 0"></a-entity>
                    </a-scene>
                </div>
                <div class="p-3">
                    <h5>{{ $medio->titulo }}</h5>
                    <p class="text-muted">{{ $medio->descripcion }}</p>
                    <span class="badge bg-primary">{{ $medio->categoria->nombre ?? 'Sin categoría' }}</span>
                </div>
            @else
                <div id="vr-container" style="height: 500px; background: linear-gradient(to bottom, #87CEEB, #2d3436);">
                    <a-scene embedded vr-mode-ui="enabled: true">
                        <a-sky color="#87CEEB"></a-sky>
                        
                        <a-text value="Visor 360°" position="0 2 -3" align="center" color="#fff" width="8"></a-text>
                        <a-text value="Arrastra para rotar la vista" position="0 1 -3" align="center" color="#eee" width="5"></a-text>
                        
                        <a-cylinder position="0 0 -4" radius="1" height="2" color="#228B22" rotation="0 0 0"></a-cylinder>
                        <a-cylinder position="3 0 -5" radius="0.5" height="1.5" color="#8B4513"></a-cylinder>
                        <a-box position="-2 0.5 -5" color="#FF6347"></a-box>
                        <a-sphere position="2 2 -6" radius="0.5" color="#FFD700"></a-sphere>
                        
                        <a-plane position="0 0 -5" rotation="-90 0 0" width="50" height="50" color="#228B22"></a-plane>
                        
                        <a-entity light="type: ambient; color: #FFF"></a-entity>
                        <a-entity light="type: directional; color: #FFF" position="-1 1 1"></a-entity>
                        
                        <a-entity camera look-controls position="0 1.6 0">
                            <a-cursor color="#fff" fuse="false" raycaster="objects: .clickable"></a-cursor>
                        </a-entity>
                    </a-scene>
                </div>
                <div class="p-3">
                    <h5>Vista 3D de demostración</h5>
                    <p class="text-muted">Escena 3D simple. Arrastra para rotar la vista y explora los objetos.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-collection-play"></i> Ver medios disponibles
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Controles:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Mouse:</strong> Arrastra para rotar la vista</li>
                    <li><strong>Scroll:</strong> Zoom in/out</li>
                    <li><strong>VR:</strong> Click en icono para modo realidad virtual</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-warning">
                <i class="bi bi-lightbulb"></i>
                <strong>Nota:</strong>
                <p class="mb-0 mt-2">Para ver imágenes 360° reales, sube imágenes panorámicas equirectangulares en formato JPG o PNG.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://aframe.io/releases/1.6.0/aframe.min.js?v=2"></script>
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.getRegistrations().then(registrations => {
            registrations.forEach(reg => reg.unregister());
        });
    }
</script>
@endsection
