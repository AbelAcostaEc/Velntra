# Velntra

Velntra es un proyecto personal de sistema administrativo/POS orientado a inventario y operaciones de negocio. Lo estoy desarrollando como proyecto de portafolio para aplicar una arquitectura modular sobre Laravel y documentar un flujo de trabajo cercano a un producto real.

## Estado

**En desarrollo activo.** La base del proyecto, autenticación, estructura modular y control de acceso ya están integrados. El desarrollo funcional continúa por módulos.

## Stack principal

- PHP 8.3+
- Laravel 13
- Livewire 3 + Volt
- Laravel Modules
- Spatie Laravel Permission
- MySQL
- Vite
- Docker Compose
- Nginx

## Enfoque técnico

- Arquitectura modular para separar dominios funcionales.
- Autenticación basada en Laravel Breeze.
- Roles y permisos mediante Spatie Laravel Permission.
- Entorno reproducible con Docker para PHP-FPM, Nginx, MySQL y Node.
- Configuración desacoplada mediante variables de entorno.
- Estructura preparada para pruebas y crecimiento por módulos.

Actualmente el repositorio incluye el módulo `Administration`, además de la infraestructura base del proyecto.

## Estructura

```text
Velntra/
├── docker/                 # Configuración de PHP y Nginx
├── docker-compose.yml      # Servicios del entorno local
├── DEV-GUIDE.md            # Guía detallada del entorno Docker
└── src/                    # Aplicación Laravel
    ├── app/
    ├── database/
    ├── Modules/
    │   └── Administration/
    ├── resources/
    └── tests/
```

## Ejecución local con Docker

Requisitos: Docker y Docker Compose.

```bash
git clone https://github.com/AbelAcostaEc/Velntra.git
cd Velntra
cp .env.example .env
docker compose up -d --build
```

Instala las dependencias y prepara Laravel:

```bash
docker compose exec app composer install
cp src/.env.example src/.env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Para los assets del frontend:

```bash
docker compose run --rm node npm install
docker compose run --rm node npm run build
```

La guía completa del entorno está en [`DEV-GUIDE.md`](./DEV-GUIDE.md).

## Objetivo del proyecto

Velntra busca demostrar, con código público, experiencia práctica en desarrollo backend con PHP/Laravel, organización modular, autenticación/autorización y trabajo en entornos Dockerizados.

## Autor

**Abel Acosta** — Full Stack Developer enfocado en PHP, Laravel y sistemas empresariales.

- Portafolio: https://abelacostaec.github.io/asaasysec/
- LinkedIn: https://www.linkedin.com/in/abel-acosta-asaa
