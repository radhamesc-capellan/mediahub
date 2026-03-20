@extends('layouts.app')

@section('title', 'Crear Medio - MediaHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Crear Nuevo Medio
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medios.store') }}" enctype="multipart/form-data" id="createForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" name="titulo" value="{{ old('titulo') }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría *</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                    id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tipo de Medio *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="tipo-url" value="url" checked>
                                <label class="btn btn-outline-primary" for="tipo-url">
                                    <i class="bi bi-link-45deg"></i> URL
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo" id="tipo-upload" value="upload">
                                <label class="btn btn-outline-primary" for="tipo-upload">
                                    <i class="bi bi-upload"></i> Subir
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo" id="tipo-embed" value="embed">
                                <label class="btn btn-outline-primary" for="tipo-embed">
                                    <i class="bi bi-youtube"></i> Video
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo" id="tipo-360" value="panorama">
                                <label class="btn btn-outline-primary" for="tipo-360">
                                    <i class="bi bi-sphere"></i> 360°
                                </label>
                            </div>
                        </div>
                        
                        <div id="panel-url" class="mb-3 tipo-panel">
                            <label for="archivo" class="form-label">URL del Archivo *</label>
                            <input type="url" class="form-control" id="archivo" name="archivo" 
                                   placeholder="https://ejemplo.com/video.mp4">
                            <small class="text-muted">URL directa a un archivo (mp4, mp3, jpg, png, etc.)</small>
                        </div>
                        
                        <div id="panel-upload" class="mb-3 tipo-panel d-none">
                            <label for="archivo_file" class="form-label">Seleccionar Archivo *</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="archivo_file" name="archivo_file" 
                                       accept="video/*,audio/*,image/*">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('archivo_file').click()">
                                    <i class="bi bi-upload"></i> Subir
                                </button>
                            </div>
                            <small class="text-muted">Video, audio o imagen (máx. 100MB)</small>
                            <div id="file-preview" class="mt-2 d-none">
                                <i class="bi bi-file-earmark"></i> <span id="file-name"></span>
                            </div>
                        </div>
                        
                        <div id="panel-embed" class="mb-3 tipo-panel d-none">
                            <label for="archivo_embed" class="form-label">URL de YouTube/Vimeo *</label>
                            <input type="url" class="form-control" id="archivo_embed" name="archivo_embed" 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <small class="text-muted">Pega la URL del video de YouTube o Vimeo</small>
                        </div>
                        
                        <div id="panel-panorama" class="mb-3 tipo-panel d-none">
                            <label for="archivo_panorama" class="form-label">URL de Imagen 360° *</label>
                            <input type="url" class="form-control" id="archivo_panorama" name="archivo_panorama" 
                                   placeholder="https://ejemplo.com/imagen-360.jpg">
                            <small class="text-muted">
                                URL de imagen panorámica equirectangular. 
                                <a href="https://commons.wikimedia.org/wiki/Category:Equirectangular_panoramas" target="_blank">Ver ejemplos</a>
                            </small>
                            
                            <div id="preview-360" class="mt-3 text-center d-none">
                                <div class="ratio ratio-16x9 bg-dark rounded" style="max-width: 400px; margin: 0 auto; overflow: hidden;">
                                    <a-scene embedded vr-mode-ui="enabled: false">
                                        <a-sky id="preview-sky" src="" rotation="0 -90 0"></a-sky>
                                        <a-entity camera look-controls="pointerLockEnabled: false" position="0 1.6 0"></a-entity>
                                    </a-scene>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-check-circle text-success"></i> Previsualización lista
                                </small>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('medios.index') }}" class="btn btn-outline-secondary me-md-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="bi bi-check-circle"></i> Crear Medio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://aframe.io/releases/1.6.0/aframe.min.js"></script>
<script>
window.onload = function() {
    var radios = document.querySelectorAll('input[name="tipo"]');
    
    function showPanel(tipo) {
        var panels = document.querySelectorAll('.tipo-panel');
        panels.forEach(function(p) { p.classList.add('d-none'); });
        var target = document.getElementById('panel-' + tipo);
        if (target) target.classList.remove('d-none');
    }
    
    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('change', function() {
            showPanel(this.value);
        });
    }
    
    var fileInput = document.getElementById('archivo_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                document.getElementById('file-name').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                document.getElementById('file-preview').classList.remove('d-none');
            }
        });
    }
    
    var panoramaInput = document.getElementById('archivo_panorama');
    if (panoramaInput) {
        panoramaInput.addEventListener('input', function() {
            var url = this.value;
            var preview = document.getElementById('preview-360');
            var sky = document.getElementById('preview-sky');
            if (url && /\.(jpg|jpeg|png|webp)$/i.test(url)) {
                sky.setAttribute('src', url);
                preview.classList.remove('d-none');
            } else {
                preview.classList.add('d-none');
            }
        });
    }
    
    console.log('Script inicializado');
};
</script>
@endpush
