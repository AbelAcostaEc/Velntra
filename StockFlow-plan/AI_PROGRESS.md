# AI Progress - Velntra

## Estado actual

Sprint actual: Sprint 01 - Base del Proyecto

Estado general: Base técnica validada; listo para continuar implementación en Laravel.

Última actualización: 2026-07-02

## Avance confirmado

### Completado

- Proyecto Laravel creado en `src/`.
- Repositorio Git configurado.
- Rama `develop` creada y en uso.
- Primeros commits realizados.
- Docker base funcionando para Laravel, Nginx, MySQL y Node.
- `.env.example` raíz creado para Docker Compose.
- `.gitignore` raíz creado para evitar subir `.env` y archivos locales.
- Laravel Breeze instalado.
- Livewire y Volt instalados.
- Rutas de autenticación generadas.
- Vistas de login, registro, recuperación, verificación y confirmación de contraseña creadas.
- Ruta de dashboard protegida por autenticación.
- Ruta de perfil protegida por autenticación.
- Spatie Permission instalado.
- Migración de permisos publicada.
- Laravel Modules instalado.
- Configuración de módulos publicada en `config/modules.php`.
- Módulo `Administration` creado y habilitado.
- Tailwind CSS y Vite configurados.
- Archivo `src/.env` configurado para MySQL.
- Conexión de Laravel con MySQL validada desde Docker.
- Migraciones ejecutadas correctamente en el entorno Docker.

### En progreso

- Completar la estructura inicial de módulos definida para Sprint 01.
- Preparar el avance hacia implementación Laravel de Sprint 02.

### Pendiente de confirmar

- Login funcionando en navegador.
- Compilación de assets con Vite.

## Sprint 01 - Checklist

### Hecho

- Crear proyecto Laravel.
- Configurar repositorio Git.
- Crear rama `develop`.
- Instalar Laravel Modules.
- Instalar Laravel Breeze.
- Instalar Livewire.
- Instalar Spatie Permission.
- Configurar TailwindCSS.
- Crear primer commit.
- Preparar Docker como base reutilizable.
- Configurar MySQL.
- Configurar archivo `.env`.
- Ejecutar `php artisan migrate`.

### Parcial

- Crear estructura inicial de módulos.

### Pendiente

- Crear módulo `Core`.
- Crear módulo `Dashboard`.
- Crear módulo `Inventory`.
- Crear módulo `Customers`.
- Crear módulo `Sales`.
- Crear módulo `Settings`.
- Probar login completo.
- Probar dashboard autenticado.

## Prioridades inmediatas

### P1 - Cerrar Sprint 01

1. Probar autenticación:

```text
http://localhost:8000/register
http://localhost:8000/login
http://localhost:8000/dashboard
```

2. Crear los módulos faltantes de Sprint 01:

```bash
docker compose exec app php artisan module:make Core
docker compose exec app php artisan module:make Dashboard
docker compose exec app php artisan module:make Inventory
docker compose exec app php artisan module:make Customers
docker compose exec app php artisan module:make Sales
docker compose exec app php artisan module:make Settings
```

3. Verificar que todos los módulos queden activos en `src/modules_statuses.json`.

4. Hacer commit de cierre de Sprint 01.

5. Pasar a Sprint 02 - Administración.

### P2 - Orden técnico antes de Sprint 02

- Revisar nombres y namespaces generados por Laravel Modules.
- Confirmar que las rutas de módulos no choquen con las rutas base.
- Confirmar que `npm run dev` funcione desde el contenedor Node.
- Confirmar que `npm run build` genere assets correctamente.
- Revisar que `Spatie Permission` esté listo para crear roles y permisos en Sprint 02.

### P3 - Limpieza y documentación mínima

- Actualizar `README.md` del proyecto Laravel solo si hace falta para correr el entorno.
- Mantener `DEV-GUIDE.md` como guía principal de Docker.
- No agregar nuevas funcionalidades fuera del plan.

## Próxima tarea recomendada

Cerrar Sprint 01 y empezar implementación Laravel con este orden:

1. Probar login y dashboard en navegador.
2. Crear módulos faltantes.
3. Confirmar `src/modules_statuses.json`.
4. Hacer commit de cierre de Sprint 01.
5. Iniciar Sprint 02 con roles, permisos y usuario administrador.

Después de eso, el proyecto entra de lleno a Laravel en Sprint 02 - Administración.

## Notas técnicas

- El plan de Sprint 01 usa MySQL como base de datos principal.
- El backlog todavía menciona PostgreSQL en `STK-003`; para este proyecto debe considerarse MySQL, porque Docker, `SprintPlan.md` y la arquitectura actual están alineados con MySQL.
- La validación base de Docker, MySQL y migraciones ya fue realizada en otra PC.
- No se debe avanzar a CRUD de usuarios hasta crear primero roles, permisos y usuario administrador base.
