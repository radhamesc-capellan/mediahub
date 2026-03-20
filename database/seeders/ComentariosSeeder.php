<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComentariosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('comentarios')->insert([
            [
                'user_id' => 1,
                'medio_id' => 1,
                'contenido' => 'Este es un comentario de prueba.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}