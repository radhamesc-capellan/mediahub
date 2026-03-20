<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 401);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 401);
        }

        $token = Str::random(64);
        
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'auth_token',
            'token' => hash('sha256', $token),
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if (!$name || !$email || !$password) {
            return response()->json(['error' => 'Faltan campos requeridos'], 422);
        }

        if (User::where('email', $email)->exists()) {
            return response()->json(['error' => 'El email ya está registrado'], 422);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $token = Str::random(64);
        
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'auth_token',
            'token' => hash('sha256', $token),
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        if ($token) {
            $token = str_replace('Bearer ', '', $token);
            ApiToken::where('token', $token)->delete();
        }
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
