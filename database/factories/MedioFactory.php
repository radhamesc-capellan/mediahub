<?php

namespace Database\Factories;

use App\Models\Medio;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedioFactory extends Factory
{
    protected $model = Medio::class;

    public function definition(): array
    {
        return [
            'titulo' => fake()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'archivo' => fake()->url() . '/video.mp4',
            'categoria_id' => Categoria::factory(),
            'user_id' => User::factory(),
        ];
    }
}
