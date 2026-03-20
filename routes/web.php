<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/test-medios', function() {
    return response()->json(\App\Models\Medio::with(['categoria', 'user'])->get());
});

Route::get('/test-categorias', function() {
    return response()->json(\App\Models\Categoria::withCount('medios')->get());
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('medios', MedioController::class);
    Route::resource('categorias', CategoriaController::class);
    
    Route::post('/medios/{id}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    
    Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/{medio}/toggle', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::delete('/favoritos/{medio}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    Route::get('/trash', [TrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/medios/{id}/restore', [TrashController::class, 'restoreMedio'])->name('trash.restoreMedio');
    Route::delete('/trash/medios/{id}', [TrashController::class, 'forceDeleteMedio'])->name('trash.forceDeleteMedio');
    Route::post('/trash/categorias/{id}/restore', [TrashController::class, 'restoreCategoria'])->name('trash.restoreCategoria');
    Route::delete('/trash/categorias/{id}', [TrashController::class, 'forceDeleteCategoria'])->name('trash.forceDeleteCategoria');
    Route::post('/trash/users/{id}/restore', [TrashController::class, 'restoreUser'])->name('trash.restoreUser');
    Route::delete('/trash/users/{id}', [TrashController::class, 'forceDeleteUser'])->name('trash.forceDeleteUser');
    Route::delete('/trash/empty', [TrashController::class, 'emptyTrash'])->name('trash.empty');
});

Route::get('/medios/{id}/comentarios', [ComentarioController::class, 'index'])->name('comentarios.index');
Route::get('/categorias/{id}/medios', [CategoriaController::class, 'show'])->name('categorias.show');

Route::get('/vr/visor-360', function() { return view('vr.visor360'); })->name('vr.visor360');
Route::get('/vr/visor-360/{id}', function($id) {
    $medio = \App\Models\Medio::findOrFail($id);
    return view('vr.visor360', compact('medio'));
})->name('vr.visor360.show');
