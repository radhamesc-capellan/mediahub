<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',
        'ip_address',
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Creó un elemento',
            'updated' => 'Actualizó un elemento',
            'deleted' => 'Eliminó un elemento',
            'profile_updated' => 'Actualizó su perfil',
            'login' => 'Inició sesión',
            'logout' => 'Cerró sesión',
            default => ucfirst($this->action),
        };
    }

    public function model()
    {
        return $this->morphTo();
    }

    public static function log(string $action, ?string $modelType = null, ?int $modelId = null, ?array $details = null): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'details' => $details,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function forModel(Model $model, string $action, ?array $details = null): self
    {
        return self::log(
            $action,
            get_class($model),
            $model->getKey(),
            $details
        );
    }
}
