<?php

namespace Database\Factories;

use App\Models\Comentario;
use App\Models\Medio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComentarioFactory extends Factory
{
    protected $model = Comentario::class;

    public function definition(): array
    {
        return [
            'contenido' => fake()->paragraph(),
            'medio_id' => Medio::factory(),
            'user_id' => User::factory(),
        ];
    }
}
