<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MedioApiController;
use App\Http\Controllers\Api\CategoriaApiController;
use App\Http\Controllers\Api\ComentarioApiController;

Route::prefix('v1')->name('api.')->group(function () {

    Route::get('/', function () {
        return response()->json([
            'nombre' => 'MediaHub API',
            'version' => '1.0.0',
            'descripcion' => 'API RESTful para MediaHub'
        ]);
    });

    Route::get('/test', function () {
        return response()->json(['test' => 'ok', 'time' => now()->toIso8601String()]);
    });

    Route::post('/test-post', function () {
        return response()->json(['received' => 'ok']);
    });

    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
    });

    Route::post('/auth/register', [AuthApiController::class, 'register']);
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    Route::middleware('api.auth')->group(function () {
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/auth/user', [AuthApiController::class, 'user']);

        Route::apiResource('medios', MedioApiController::class);
        Route::apiResource('categorias', CategoriaApiController::class);

        Route::get('/medios/{medio}/comentarios', [ComentarioApiController::class, 'index']);
        Route::post('/medios/{medio}/comentarios', [ComentarioApiController::class, 'store']);
        Route::get('/medios/{medio}/comentarios/{comentario}', [ComentarioApiController::class, 'show']);
        Route::put('/medios/{medio}/comentarios/{comentario}', [ComentarioApiController::class, 'update']);
        Route::delete('/medios/{medio}/comentarios/{comentario}', [ComentarioApiController::class, 'destroy']);
    });

    Route::get('/medios/{medio}/comentarios', [ComentarioApiController::class, 'index']);
});
