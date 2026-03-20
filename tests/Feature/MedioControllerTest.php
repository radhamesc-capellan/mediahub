<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Medio;
use App\Models\Comentario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedioControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->categoria = Categoria::factory()->create();
    }

    public function test_home_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MediaHub');
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión');
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Crear Cuenta');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'nuevo@test.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_see_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_medios_index_requires_authentication(): void
    {
        $response = $this->get('/medios');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_see_medios(): void
    {
        Medio::factory()->count(3)->create(['categoria_id' => $this->categoria->id]);

        $response = $this->actingAs($this->user)->get('/medios');
        $response->assertStatus(200);
        $response->assertSee('Medios');
    }

    public function test_user_can_create_medio(): void
    {
        $response = $this->actingAs($this->user)->post('/medios', [
            'titulo' => 'Nuevo Video',
            'descripcion' => 'Descripción del video',
            'tipo' => 'url',
            'archivo' => 'https://ejemplo.com/video.mp4',
            'categoria_id' => $this->categoria->id,
        ]);

        $response->assertRedirect('/medios');
        $this->assertDatabaseHas('medios', ['titulo' => 'Nuevo Video']);
    }

    public function test_medio_creation_requires_title(): void
    {
        $response = $this->actingAs($this->user)->post('/medios', [
            'descripcion' => 'Sin título',
            'tipo' => 'url',
            'archivo' => 'https://ejemplo.com/video.mp4',
            'categoria_id' => $this->categoria->id,
        ]);

        $response->assertSessionHasErrors('titulo');
    }

    public function test_user_can_view_medio(): void
    {
        $medio = Medio::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->actingAs($this->user)->get("/medios/{$medio->id}");
        $response->assertStatus(200);
        $response->assertSee($medio->titulo);
    }

    public function test_user_can_update_own_medio(): void
    {
        $medio = Medio::factory()->create([
            'user_id' => $this->user->id,
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->actingAs($this->user)->put("/medios/{$medio->id}", [
            'titulo' => 'Titulo Actualizado',
            'descripcion' => 'Nueva descripción',
            'tipo' => 'url',
            'archivo' => 'https://ejemplo.com/new-video.mp4',
            'categoria_id' => $this->categoria->id,
        ]);

        $response->assertRedirect("/medios/{$medio->id}");
        $this->assertDatabaseHas('medios', ['titulo' => 'Titulo Actualizado']);
    }

    public function test_user_cannot_update_other_users_medio(): void
    {
        $otherUser = User::factory()->create();
        $medio = Medio::factory()->create([
            'user_id' => $otherUser->id,
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->actingAs($this->user)->put("/medios/{$medio->id}", [
            'titulo' => 'Titulo Actualizado',
            'descripcion' => 'Nueva descripción',
            'tipo' => 'url',
            'archivo' => 'https://ejemplo.com/new-video.mp4',
            'categoria_id' => $this->categoria->id,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_user_can_delete_own_medio(): void
    {
        $medio = Medio::factory()->create([
            'user_id' => $this->user->id,
            'categoria_id' => $this->categoria->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/medios/{$medio->id}");

        $response->assertRedirect('/medios');
        $this->assertDatabaseMissing('medios', ['id' => $medio->id]);
    }

    public function test_user_can_create_comentario(): void
    {
        $medio = Medio::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->actingAs($this->user)->post("/medios/{$medio->id}/comentarios", [
            'contenido' => 'Este es un comentario de prueba',
        ]);

        $this->assertDatabaseHas('comentarios', ['contenido' => 'Este es un comentario de prueba']);
    }

    public function test_comentario_requires_content(): void
    {
        $medio = Medio::factory()->create(['categoria_id' => $this->categoria->id]);

        $response = $this->actingAs($this->user)->post("/medios/{$medio->id}/comentarios", [
            'contenido' => '',
        ]);

        $response->assertSessionHasErrors('contenido');
    }

    public function test_user_can_delete_own_comentario(): void
    {
        $comentario = Comentario::factory()->create([
            'user_id' => $this->user->id,
            'medio_id' => Medio::factory()->create(['categoria_id' => $this->categoria->id])->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/comentarios/{$comentario->id}");

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('comentarios', ['id' => $comentario->id]);
    }

    public function test_user_cannot_delete_other_users_comentario(): void
    {
        $otherUser = User::factory()->create();
        $comentario = Comentario::factory()->create([
            'user_id' => $otherUser->id,
            'medio_id' => Medio::factory()->create(['categoria_id' => $this->categoria->id])->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/comentarios/{$comentario->id}");

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('comentarios', ['id' => $comentario->id]);
    }
}
