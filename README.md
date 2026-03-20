# MediaHub

## Autor
[Radhamés Capellán](https://raddev.me)

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

Plataforma web multimedia para gestionar y compartir contenido como videos, audios e imágenes con sistema de usuarios, categorías y comentarios.

## Funcionalidades

- **Gestión de usuarios** - Registro, login, roles y perfiles
- **CRUD de medios** - Subida de videos, audios e imágenes
- **Categorías** - Organización de contenido por tipo
- **Comentarios** - Interacción entre usuarios
- **API REST** - Endpoints completos con autenticación Bearer
- **Panel de administración** - Dashboard para gestionar contenido

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8+ / TiDB
- Docker (opcional)

## Instalación

### Con Docker

```bash
git clone <repo-url>
cd mediahub
docker-compose up -d
docker exec mediahub_app php artisan migrate
docker exec mediahub_app php artisan db:seed
```

### Local

```bash
git clone <repo-url>
cd mediahub
composer install
cp .env.example .env
# Editar .env con credenciales de BD
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Datos de prueba

| Email | Contraseña | Rol |
|-------|------------|-----|
| admin@mediahub.com | admin123 | Administrador |
| editor@mediahub.com | editor123 | Editor Principal |
| viewer@mediahub.com | viewer123 | Usuario Visitante |

## API REST

Base URL: `/api/v1`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/auth/register` | Registrar usuario |
| POST | `/auth/login` | Iniciar sesión |
| GET | `/medios` | Listar medios |
| POST | `/medios` | Crear medio |
| GET | `/categorias` | Listar categorías |
| POST | `/categorias` | Crear categoría |

## Modelo de datos

```
users ────< medios
users ────< comentarios
categorias ────< medios
medios ────< comentarios
```

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Blade + Bootstrap 5
- **BD:** MySQL 8
- **Contenedores:** Docker

