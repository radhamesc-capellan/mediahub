<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Medio;
use App\Models\Comentario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_medios(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create();
        
        $medio = Medio::factory()->create([
            'user_id' => $user->id,
            'categoria_id' => $categoria->id,
        ]);

        $this->assertCount(1, $user->medios);
        $this->assertTrue($user->medios->contains($medio));
    }

    public function test_user_can_have_comentarios(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);
        
        $comentario = Comentario::factory()->create([
            'user_id' => $user->id,
            'medio_id' => $medio->id,
        ]);

        $this->assertCount(1, $user->comentarios);
        $this->assertTrue($user->comentarios->contains($comentario));
    }

    public function test_categoria_can_have_medios(): void
    {
        $categoria = Categoria::factory()->create();
        $medios = Medio::factory()->count(3)->create(['categoria_id' => $categoria->id]);

        $this->assertCount(3, $categoria->medios);
        $this->assertTrue($categoria->medios->contains($medios->first()));
    }

    public function test_medio_belongs_to_categoria(): void
    {
        $categoria = Categoria::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);

        $this->assertInstanceOf(Categoria::class, $medio->categoria);
        $this->assertEquals($categoria->id, $medio->categoria->id);
    }

    public function test_medio_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $medio = Medio::factory()->create([
            'user_id' => $user->id,
            'categoria_id' => $categoria->id,
        ]);

        $this->assertInstanceOf(User::class, $medio->user);
        $this->assertEquals($user->id, $medio->user->id);
    }

    public function test_medio_can_have_comentarios(): void
    {
        $categoria = Categoria::factory()->create();
        $user = User::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);
        
        $comentarios = Comentario::factory()->count(2)->create([
            'medio_id' => $medio->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(2, $medio->comentarios);
    }

    public function test_comentario_belongs_to_medio(): void
    {
        $categoria = Categoria::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);
        $user = User::factory()->create();
        
        $comentario = Comentario::factory()->create([
            'medio_id' => $medio->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Medio::class, $comentario->medio);
        $this->assertEquals($medio->id, $comentario->medio->id);
    }

    public function test_comentario_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);
        
        $comentario = Comentario::factory()->create([
            'user_id' => $user->id,
            'medio_id' => $medio->id,
        ]);

        $this->assertInstanceOf(User::class, $comentario->user);
        $this->assertEquals($user->id, $comentario->user->id);
    }

    public function test_user_fillable_attributes(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_categoria_fillable_attributes(): void
    {
        $categoria = Categoria::factory()->create(['nombre' => 'Noticias']);

        $this->assertEquals('Noticias', $categoria->nombre);
    }

    public function test_medio_fillable_attributes(): void
    {
        $categoria = Categoria::factory()->create();
        $user = User::factory()->create();
        
        $medio = Medio::factory()->create([
            'titulo' => 'Video de prueba',
            'descripcion' => 'Descripción del video',
            'archivo' => 'https://ejemplo.com/video.mp4',
            'categoria_id' => $categoria->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Video de prueba', $medio->titulo);
        $this->assertEquals('Descripción del video', $medio->descripcion);
        $this->assertEquals('https://ejemplo.com/video.mp4', $medio->archivo);
    }

    public function test_comentario_fillable_attributes(): void
    {
        $categoria = Categoria::factory()->create();
        $user = User::factory()->create();
        $medio = Medio::factory()->create(['categoria_id' => $categoria->id]);
        
        $comentario = Comentario::factory()->create([
            'contenido' => 'Este es un comentario de prueba',
            'medio_id' => $medio->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Este es un comentario de prueba', $comentario->contenido);
    }
}
