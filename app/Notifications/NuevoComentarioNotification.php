<?php

namespace App\Notifications;

use App\Models\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NuevoComentarioNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comentario $comentario
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comentario_id' => $this->comentario->id,
            'medio_id' => $this->comentario->medio_id,
            'medio_titulo' => $this->comentario->medio->titulo ?? 'Medio',
            'user_id' => $this->comentario->user_id,
            'user_nombre' => $this->comentario->user->name ?? 'Usuario',
            'contenido' => Str::limit($this->comentario->contenido, 50),
            'tipo' => 'comentario',
            'mensaje' => $this->comentario->user->name . ' commented on ' . ($this->comentario->medio->titulo ?? 'a media'),
        ];
    }
}
