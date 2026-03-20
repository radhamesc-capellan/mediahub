@extends('layouts.app')

@section('title', 'Editar Medio - MediaHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Editar Medio
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('medios.update', $medio->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título *</label>
                            <input type="text" class="form-control @error('titulo') is-invalid @enderror" 
                                   id="titulo" name="titulo" value="{{ old('titulo', $medio->titulo) }}" required>
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría *</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" 
                                    id="categoria_id" name="categoria_id" required>
                                @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $medio->categoria_id == $categoria->id ? 'selected' : '' }}>
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
                                      id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $medio->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tipo de Medio *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="tipo-url" value="url" {{ old('tipo', $medio->tipo) === 'url' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="tipo-url">
                                    <i class="bi bi-link-45deg"></i> URL Externa
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo" id="tipo-upload" value="upload" {{ old('tipo', $medio->tipo) === 'upload' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="tipo-upload">
                                    <i class="bi bi-upload"></i> Subir Archivo
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipo" id="tipo-embed" value="embed" {{ old('tipo', $medio->tipo) === 'embed' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="tipo-embed">
                                    <i class="bi bi-youtube"></i> YouTube/Vimeo
                                </label>
                            </div>
                        </div>
                        
                        <div id="panel-url" class="mb-3 tipo-panel {{ old('tipo', $medio->tipo) !== 'url' ? 'd-none' : '' }}">
                            <label for="archivo_url" class="form-label">URL del Archivo *</label>
                            <input type="url" class="form-control @error('archivo') is-invalid @enderror" 
                                   id="archivo_url" name="archivo" value="{{ old('archivo', $medio->archivo) }}">
                            <small class="text-muted">URL directa a un archivo (mp4, mp3, jpg, png, etc.)</small>
                            @error('archivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="panel-upload" class="mb-3 tipo-panel {{ old('tipo', $medio->tipo) !== 'upload' ? 'd-none' : '' }}">
                            <label for="archivo_file" class="form-label">Subir Nuevo Archivo *</label>
                            <input type="file" class="form-control @error('archivo_file') is-invalid @enderror" 
                                   id="archivo_file" name="archivo_file" accept="video/*,audio/*,image/*">
                            <small class="text-muted">Deja vacío para mantener el archivo actual</small>
                            @error('archivo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($medio->tipo === 'upload' && $medio->archivo)
                            <div class="mt-2">
                                <small class="text-muted">Archivo actual: {{ basename($medio->archivo) }}</small>
                            </div>
                            @endif
                        </div>
                        
                        <div id="panel-embed" class="mb-3 tipo-panel {{ old('tipo', $medio->tipo) !== 'embed' ? 'd-none' : '' }}">
                            <label for="archivo_embed" class="form-label">URL de YouTube/Vimeo *</label>
                            <input type="url" class="form-control @error('archivo') is-invalid @enderror" 
                                   id="archivo_embed" name="archivo" value="{{ old('archivo', $medio->archivo) }}">
                            <small class="text-muted">Pega la URL del video de YouTube o Vimeo</small>
                            @error('archivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('medios.show', $medio->id) }}" class="btn btn-outline-secondary me-md-2">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Actualizar
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
<script>
document.querySelectorAll('input[name="tipo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.tipo-panel').forEach(panel => panel.classList.add('d-none'));
        document.getElementById('panel-' + this.value).classList.remove('d-none');
    });
});
</script>
@endpush
