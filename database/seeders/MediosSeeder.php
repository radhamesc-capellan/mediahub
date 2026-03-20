<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medios')->insert([
            [
                'titulo' => 'Primer artículo',
                'descripcion' => 'Descripción del primer artículo',
                'archivo' => 'archivo1.pdf',
                'tipo' => 'upload', // <-- ¡Aquí está el campo que faltaba!
                'categoria_id' => 1,
                'user_id' => 1,     // Agregado porque tu migración lo permite
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Segundo artículo',
                'descripcion' => 'Descripción del segundo artículo',
                'archivo' => 'archivo2.pdf',
                'tipo' => 'upload', // <-- ¡Aquí está el campo que faltaba!
                'categoria_id' => 2,
                'user_id' => 1,     // Agregado
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}