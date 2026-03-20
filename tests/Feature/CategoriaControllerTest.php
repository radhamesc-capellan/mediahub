<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Medio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_categorias_index_requires_authentication(): void
    {
        $response = $this->get('/categorias');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_see_categorias(): void
    {
        Categoria::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/categorias');
        $response->assertStatus(200);
        $response->assertSee('Categorías');
    }

    public function test_user_can_create_categoria(): void
    {
        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => 'Nueva Categoria',
        ]);

        $response->assertRedirect('/categorias');
        $this->assertDatabaseHas('categorias', ['nombre' => 'Nueva Categoria']);
    }

    public function test_categoria_requires_unique_nombre(): void
    {
        Categoria::factory()->create(['nombre' => 'Duplicada']);

        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => 'Duplicada',
        ]);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_categoria_requires_nombre(): void
    {
        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => '',
        ]);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_user_can_view_categoria(): void
    {
        $categoria = Categoria::factory()->create(['nombre' => 'Ver Categoria']);

        $response = $this->actingAs($this->user)->get("/categorias/{$categoria->id}");
        $response->assertStatus(200);
        $response->assertSee('Ver Categoria');
    }

    public function test_user_can_update_categoria(): void
    {
        $categoria = Categoria::factory()->create(['nombre' => 'Original']);

        $response = $this->actingAs($this->user)->put("/categorias/{$categoria->id}", [
            'nombre' => 'Actualizado',
        ]);

        $response->assertRedirect("/categorias/{$categoria->id}");
        $this->assertDatabaseHas('categorias', ['nombre' => 'Actualizado']);
    }

    public function test_user_can_delete_empty_categoria(): void
    {
        $categoria = Categoria::factory()->create();

        $response = $this->actingAs($this->user)->delete("/categorias/{$categoria->id}");

        $response->assertRedirect('/categorias');
        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_cannot_delete_categoria_with_medios(): void
    {
        $categoria = Categoria::factory()->create();
        Medio::factory()->create(['categoria_id' => $categoria->id]);

        $response = $this->actingAs($this->user)->delete("/categorias/{$categoria->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }
}
