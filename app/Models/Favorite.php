<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $table = 'favoritos';
    
    protected $fillable = ['user_id', 'medio_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medio(): BelongsTo
    {
        return $this->belongsTo(Medio::class, 'medio_id');
    }
}
