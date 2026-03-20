@extends('layouts.app')

@section('title', 'Mi Actividad - MediaHub')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-clock-history"></i> Mi Actividad</h2>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Perfil
                </a>
            </div>

            @if($activities->count() > 0)
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                    <th>Detalles</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $activity->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->action === 'created' ? 'success' : ($activity->action === 'deleted' ? 'danger' : ($activity->action === 'profile_updated' ? 'info' : 'secondary')) }}">
                                            {{ $activity->getActionLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($activity->model_type)
                                            <small>
                                                {{ class_basename($activity->model_type) }}
                                                @if($activity->model_id)
                                                    #{{ $activity->model_id }}
                                                @endif
                                            </small>
                                        @elseif($activity->details)
                                            <small class="text-muted">
                                                {{ json_encode($activity->details) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $activity->ip_address ?? '-' }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {{ $activities->links() }}
                </div>
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No hay actividad registrada todavía.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
