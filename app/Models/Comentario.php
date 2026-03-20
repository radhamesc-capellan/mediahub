<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['contenido', 'user_id', 'medio_id'];

    public function medio()
    {
        return $this->belongsTo(Medio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
