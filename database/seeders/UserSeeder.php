<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mediahub.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@mediahub.com',
                'password' => Hash::make('admin123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'editor@mediahub.com'],
            [
                'name' => 'Editor Principal',
                'email' => 'editor@mediahub.com',
                'password' => Hash::make('editor123'),
                'role' => User::ROLE_EDITOR,
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@mediahub.com'],
            [
                'name' => 'Usuario Visitante',
                'email' => 'viewer@mediahub.com',
                'password' => Hash::make('viewer123'),
                'role' => User::ROLE_VIEWER,
            ]
        );
    }
}
