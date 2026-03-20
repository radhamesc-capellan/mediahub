<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Categoria;
use App\Models\Medio;
use App\Models\Comentario;
use App\Models\ApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Categoria $categoria;
    protected string $apiToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->categoria = Categoria::factory()->create(['nombre' => 'Videos']);
        $plainToken = 'test-token-123';
        ApiToken::create([
            'user_id' => $this->user->id,
            'name' => 'Test Token',
            'token' => hash('sha256', $plainToken),
        ]);
        $this->apiToken = $plainToken;
    }

    public function test_full_user_workflow_web(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MediaHub');

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Iniciar Sesión');

        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');

        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => 'Música',
        ]);
        $response->assertRedirect('/categorias');
        $this->assertDatabaseHas('categorias', ['nombre' => 'Música']);

        $categoria = Categoria::where('nombre', 'Música')->first();

        $response = $this->actingAs($this->user)->post('/medios', [
            'titulo' => 'Mi Video de Prueba',
            'descripcion' => 'Descripción del video',
            'tipo' => 'embed',
            'archivo' => 'https://youtube.com/watch?v=test',
            'categoria_id' => $categoria->id,
        ]);
        $response->assertRedirect('/medios');
        $this->assertDatabaseHas('medios', ['titulo' => 'Mi Video de Prueba']);

        $medio = Medio::where('titulo', 'Mi Video de Prueba')->first();

        $response = $this->actingAs($this->user)->get("/medios/{$medio->id}");
        $response->assertStatus(200);
        $response->assertSee('Mi Video de Prueba');

        $response = $this->actingAs($this->user)->post("/medios/{$medio->id}/comentarios", [
            'contenido' => 'Gran video!',
        ]);
        $this->assertDatabaseHas('comentarios', ['contenido' => 'Gran video!']);

        $response = $this->actingAs($this->user)->put("/medios/{$medio->id}", [
            'titulo' => 'Video Actualizado',
            'descripcion' => 'Nueva descripción',
            'tipo' => 'embed',
            'archivo' => 'https://youtube.com/watch?v=updated',
            'categoria_id' => $categoria->id,
        ]);
        $response->assertRedirect("/medios/{$medio->id}");
        $this->assertDatabaseHas('medios', ['titulo' => 'Video Actualizado']);

        $response = $this->actingAs($this->user)->delete("/medios/{$medio->id}");
        $response->assertRedirect('/medios');
        $this->assertDatabaseMissing('medios', ['id' => $medio->id]);

        $this->post('/logout');
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_full_api_workflow(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'API User',
            'email' => 'api@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['user', 'token']);

        $plainToken = $response->json('token');
        $apiUser = User::where('email', 'api@test.com')->first();
        
        $apiTokenRecord = ApiToken::where('user_id', $apiUser->id)->first();
        $this->assertNotNull($apiTokenRecord, 'Token should exist in DB');
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->getJson('/api/v1/auth/user');
        $response->assertStatus(200);
        $response->assertJson(['email' => 'api@test.com']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson('/api/v1/categorias', [
                'nombre' => 'API Categoria',
            ]);
        $response->assertStatus(201);
        $response->assertJsonPath('data.nombre', 'API Categoria');

        $apiCategoria = Categoria::where('nombre', 'API Categoria')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->getJson('/api/v1/categorias');
        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(1, count($response->json('data')));

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson('/api/v1/medios', [
                'titulo' => 'API Video',
                'descripcion' => 'Video desde API',
                'tipo' => 'url',
                'archivo' => 'https://api.example.com/video.mp4',
                'categoria_id' => $apiCategoria->id,
            ]);
        $response->assertStatus(201);
        $response->assertJsonPath('data.titulo', 'API Video');

        $apiMedio = Medio::where('titulo', 'API Video')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->getJson('/api/v1/medios/' . $apiMedio->id);
        $response->assertStatus(200);
        $response->assertJsonPath('data.titulo', 'API Video');

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->putJson('/api/v1/medios/' . $apiMedio->id, [
                'titulo' => 'API Video Modificado',
                'descripcion' => 'Video modificado',
                'tipo' => 'url',
                'archivo' => 'https://api.example.com/modified.mp4',
                'categoria_id' => $apiCategoria->id,
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.titulo', 'API Video Modificado');

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson("/api/v1/medios/{$apiMedio->id}/comentarios", [
                'contenido' => 'Comentario desde API',
            ]);
        $response->assertStatus(201);
        $response->assertJsonPath('data.contenido', 'Comentario desde API');

        $comentario = Comentario::where('contenido', 'Comentario desde API')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->putJson("/api/v1/medios/{$apiMedio->id}/comentarios/{$comentario->id}", [
                'contenido' => 'Comentario modificado',
            ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.contenido', 'Comentario modificado');

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->deleteJson("/api/v1/medios/{$apiMedio->id}/comentarios/{$comentario->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('comentarios', ['id' => $comentario->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->deleteJson('/api/v1/medios/' . $apiMedio->id);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('medios', ['id' => $apiMedio->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson('/api/v1/auth/logout');
        $response->assertStatus(200);
    }

    public function test_categorias_con_medios(): void
    {
        $categoria1 = Categoria::factory()->create(['nombre' => 'Tecnologia']);
        $categoria2 = Categoria::factory()->create(['nombre' => 'Entretenimiento']);

        Medio::factory()->count(5)->create(['categoria_id' => $categoria1->id]);
        Medio::factory()->count(3)->create(['categoria_id' => $categoria2->id]);
        Medio::factory()->count(2)->create();

        $response = $this->actingAs($this->user)->get('/categorias');
        $response->assertStatus(200);
        $response->assertSee('Tecnologia');
        $response->assertSee('Entretenimiento');

        $response = $this->actingAs($this->user)->get("/categorias/{$categoria1->id}");
        $response->assertStatus(200);
        $response->assertSee('Tecnologia');
        $response->assertSee('5');

        $categoria1->refresh();
        $this->assertEquals(5, $categoria1->medios()->count());

        $response = $this->actingAs($this->user)->delete("/categorias/{$categoria1->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categorias', ['id' => $categoria1->id]);

        $response = $this->actingAs($this->user)->delete("/categorias/{$categoria2->id}");
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categorias', ['id' => $categoria2->id]);
    }

    public function test_usuarios_sin_permisos(): void
    {
        $user2 = User::factory()->create();
        $medio = Medio::factory()->create(['user_id' => $this->user->id, 'categoria_id' => $this->categoria->id]);
        $comentario = Comentario::factory()->create(['user_id' => $this->user->id, 'medio_id' => $medio->id]);

        $response = $this->actingAs($user2)->put("/medios/{$medio->id}", [
            'titulo' => 'Intento de modificacion',
            'descripcion' => 'Hackeando...',
            'archivo' => 'https://malicioso.com',
            'categoria_id' => $this->categoria->id,
        ]);
        $response->assertRedirect('/medios');
        $response->assertSessionHas('error');

        $medio->refresh();
        $this->assertNotEquals('Intento de modificacion', $medio->titulo);

        $response = $this->actingAs($user2)->delete("/medios/{$medio->id}");
        $response->assertRedirect('/medios');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('medios', ['id' => $medio->id]);

        $response = $this->actingAs($user2)->delete("/comentarios/{$comentario->id}");
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('comentarios', ['id' => $comentario->id]);
    }

    public function test_api_sin_autenticacion(): void
    {
        $response = $this->getJson('/api/v1/medios');
        $response->assertStatus(401);

        $response = $this->getJson('/api/v1/categorias');
        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/medios', [
            'titulo' => 'Test',
            'categoria_id' => $this->categoria->id,
        ]);
        $response->assertStatus(401);

        $response = $this->putJson('/api/v1/categorias/' . $this->categoria->id, [
            'nombre' => 'Hacked',
        ]);
        $response->assertStatus(401);

        $response = $this->deleteJson('/api/v1/medios/1');
        $response->assertStatus(401);
    }

    public function test_api_token_invalido(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer token-invalido')
            ->getJson('/api/v1/medios');
        $response->assertStatus(401);

        $response = $this->withHeader('Authorization', 'Bearer ')
            ->getJson('/api/v1/medios');
        $response->assertStatus(401);
    }

    public function test_validacion_web(): void
    {
        $response = $this->actingAs($this->user)->post('/medios', [
            'titulo' => '',
            'categoria_id' => $this->categoria->id,
        ]);
        $response->assertSessionHasErrors('titulo');

        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => '',
        ]);
        $response->assertSessionHasErrors('nombre');

        $response = $this->actingAs($this->user)->post('/categorias', [
            'nombre' => $this->categoria->nombre,
        ]);
        $response->assertSessionHasErrors('nombre');
    }

    public function test_validacion_api(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->apiToken)
            ->postJson('/api/v1/medios', [
                'titulo' => '',
            ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['titulo', 'tipo', 'archivo', 'categoria_id']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->apiToken)
            ->postJson('/api/v1/categorias', [
                'nombre' => '',
            ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->apiToken)
            ->postJson('/api/v1/categorias', [
                'nombre' => $this->categoria->nombre,
            ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre']);
    }

    public function test_registro_login_web(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Crear Cuenta');

        $response = $this->post('/register', [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->post('/logout');

        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->post('/login', [
            'email' => 'nuevo@ejemplo.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_registro_login_api(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'API Nuevo',
            'email' => 'api.nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'api.nuevo@test.com']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'api.nuevo@test.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'token']);
    }

    public function test_health_check_api(): void
    {
        $response = $this->getJson('/api/v1/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $response = $this->getJson('/api/v1/');
        $response->assertStatus(200);
        $response->assertJson(['nombre' => 'MediaHub API']);
    }
}
