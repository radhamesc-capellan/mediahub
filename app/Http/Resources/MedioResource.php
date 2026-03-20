<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'archivo' => $this->archivo,
            'embed_url' => $this->embed_url,
            'es_video' => $this->isVideo(),
            'es_audio' => $this->isAudio(),
            'es_imagen' => $this->isImage(),
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'usuario' => [
                'id' => $this->user->id ?? null,
                'nombre' => $this->user->name ?? null,
            ],
            'comentarios_count' => $this->when($this->comentarios_count !== null, $this->comentarios_count),
            'comentarios' => ComentarioResource::collection($this->whenLoaded('comentarios')),
            'creado_en' => $this->created_at?->toIso8601String(),
            'actualizado_en' => $this->updated_at?->toIso8601String(),
        ];
    }
}
