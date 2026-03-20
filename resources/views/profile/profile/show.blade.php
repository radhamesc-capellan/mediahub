@extends('layouts.app')

@section('title', 'Mi Perfil - MediaHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <img src="{{ $user->getAvatarUrl() }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    
                    <span class="badge bg-{{ $user->isAdmin() ? 'danger' : ($user->isEditor() ? 'primary' : 'secondary') }}">
                        {{ $user->getRoleLabel() }}
                    </span>
                    
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Editar Perfil
                        </a>
                        <a href="{{ route('profile.activity') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-clock-history"></i> Mi Actividad
                        </a>
                    </div>
                </div>
                
                <div class="card-footer bg-white">
                    <div class="row text-center">
                        <div class="col-4">
                            <strong>{{ $user->medios_count ?? $user->medios->count() }}</strong>
                            <br><small class="text-muted">Medios</small>
                        </div>
                        <div class="col-4">
                            <strong>{{ $user->comentarios_count ?? $user->comentarios->count() }}</strong>
                            <br><small class="text-muted">Comentarios</small>
                        </div>
                        <div class="col-4">
                            <strong>{{ $user->created_at->diffForHumans() }}</strong>
                            <br><small class="text-muted">Miembro desde</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($activities->count() > 0)
            <div class="card mt-4 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Actividad Reciente</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($activities as $activity)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-{{ $activity->action === 'created' ? 'plus-circle text-success' : ($activity->action === 'deleted' ? 'trash text-danger' : 'pencil text-primary') }}"></i>
                                    <span class="ms-2">{{ $activity->getActionLabel() }}</span>
                                </div>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
