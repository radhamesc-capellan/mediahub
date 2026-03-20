<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['titulo', 'descripcion', 'archivo', 'tipo', 'categoria_id', 'user_id'];

    protected $casts = [
        'tipo' => 'string',
    ];

    public function getEmbedUrlAttribute()
    {
        if ($this->tipo === 'embed') {
            $url = $this->archivo;
            
            if (str_contains($url, 'youtube.com/watch')) {
                preg_match('/[?&]v=([^&]+)/', $url, $matches);
                return 'https://www.youtube.com/embed/' . ($matches[1] ?? '');
            }
            
            if (str_contains($url, 'youtu.be/')) {
                preg_match('/youtu\.be\/([^\?]+)/', $url, $matches);
                return 'https://www.youtube.com/embed/' . ($matches[1] ?? '');
            }
            
            if (str_contains($url, 'vimeo.com/')) {
                preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
                return 'https://player.vimeo.com/video/' . ($matches[1] ?? '');
            }
            
            return $url;
        }
        
        return null;
    }

    public function isVideo(): bool
    {
        return $this->tipo === 'embed' || 
               in_array(pathinfo($this->archivo, PATHINFO_EXTENSION), ['mp4', 'webm', 'avi', 'mov']);
    }

    public function isAudio(): bool
    {
        return in_array(pathinfo($this->archivo, PATHINFO_EXTENSION), ['mp3', 'wav', 'ogg', 'm4a']);
    }

    public function isImage(): bool
    {
        return in_array(pathinfo($this->archivo, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    public function is360(): bool
    {
        return $this->tipo === 'panorama' || str_contains($this->archivo, '_360');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritos()
    {
        return $this->hasMany(Favorite::class, 'medio_id');
    }

    public function usuariosFavoritos()
    {
        return $this->belongsToMany(User::class, 'favoritos')->withTimestamps();
    }

    public function getFavoritosCountAttribute(): int
    {
        return $this->favoritos()->count();
    }
}
