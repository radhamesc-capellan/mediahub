<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categorias')->insert([
            ['nombre' => 'Noticias', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tutoriales', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Opinión', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}