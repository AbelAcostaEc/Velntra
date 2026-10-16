# Preparación para publicación

Velntra todavía no tiene hosting ni dominio asignados. Esta guía define los requisitos y la secuencia de despliegue; la configuración final dependerá del proveedor que se elija. **No uses `docker-compose.yml` tal cual en internet**: es una composición de desarrollo con MySQL y Vite publicados, código montado desde el anfitrión y HTTP sin TLS.

## Requisitos del destino

- PHP 8.3 con las extensiones de [`docker/php/Dockerfile`](../docker/php/Dockerfile), Composer, Node 20+, MySQL 8 y un servidor web cuya raíz pública sea `src/public`.
- HTTPS y una URL pública definida. Configura `APP_URL` con esa URL; sirve únicamente el directorio `public`, nunca la raíz de Laravel.
- Almacenamiento persistente para MySQL y `src/storage/app/public` (logos e imágenes). Conserva también `APP_KEY` entre despliegues y prepara copias de seguridad de base y archivos.
- Un método de despliegue que no exponga los puertos de MySQL ni Vite al público. Para un contenedor de producción, empaqueta el código y assets en la imagen y evita montajes editables del código.

## Variables obligatorias

Parte de [`src/.env.example`](../src/.env.example), pero usa secretos **nuevos** y no comitas el `.env` real:

```env
APP_NAME=Velntra
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.example
APP_KEY=base64:CLAVE_GENERADA_UNA_SOLA_VEZ

DB_CONNECTION=mysql
DB_HOST=HOST_PRIVADO_DE_MYSQL
DB_PORT=3306
DB_DATABASE=velntra
DB_USERNAME=USUARIO_EXCLUSIVO
DB_PASSWORD=CLAVE_UNICA

INITIAL_ADMIN_EMAIL=admin@tu-dominio.example
INITIAL_ADMIN_PASSWORD=CLAVE_UNICA_LARGA
```

El seeder rechaza las credenciales iniciales conocidas si `APP_ENV=production`, incluso si copiaste una base local que ya tenía un administrador con la contraseña por defecto. Solo crea una cuenta inicial cuando **no existe ningún administrador**; ejecutar seeders de nuevo no cambia su contraseña ni la configuración del negocio, ni crea otro administrador si cambias `INITIAL_ADMIN_EMAIL`. Cambia la contraseña desde el perfil tras el primer acceso y elimina la contraseña inicial del gestor de secretos una vez verificado el acceso; la cuenta existente no depende de conservarla. Si el correo ya pertenece a un usuario sin rol admin, el seeder se detiene para evitar asignarle privilegios por accidente.

## Secuencia de un despliegue

1. Aprovisiona una base MySQL privada, almacenamiento persistente y HTTPS. Prueba la restauración de una copia de seguridad antes de publicar datos reales.
2. Construye dependencias y assets del mismo commit: `composer install --no-dev --optimize-autoloader`, `npm ci`, `npm run build`. No publiques `node_modules` ni el servidor Vite.
3. Configura variables de producción. Genera `APP_KEY` **solo en la primera instalación**; cambiarla después invalida datos cifrados y sesiones.
4. Ejecuta `php artisan migrate --force`, `php artisan db:seed --force` y `php artisan storage:link` en el entorno de destino. Haz una copia antes de cada nueva migración.
5. Limpia/carga cachés del despliegue según el entorno (`php artisan optimize`) y arranca los procesos web. Si usarás colas asíncronas, configura también un worker y supervisión; con `QUEUE_CONNECTION=sync` las tareas se ejecutan en la petición.
6. Verifica `GET /up`, inicio de sesión, subida de imagen, venta, anulación y exportaciones. Confirma `APP_DEBUG=false` y que la URL pública fuerza HTTPS.

Antes de cada actualización, guarda una copia de MySQL y de los archivos cargados. **Nunca ejecutes `migrate:fresh`, `db:wipe` ni `docker compose down -v` sobre datos reales.** El seeder [`PortfolioDemoSeeder`](../src/database/seeders/PortfolioDemoSeeder.php) es opcional y está bloqueado en producción; el catálogo de cada negocio se carga desde la interfaz.

Cuando se elija hosting, faltará adaptar estos pasos a su red, almacenamiento, proceso web y certificados; realizar el despliegue real; y publicar la URL. Las recomendaciones de raíz `public`, `APP_DEBUG=false`, cachés y salud se apoyan en la [guía oficial de despliegue de Laravel 12](https://laravel.com/docs/12.x/deployment); la separación entre configuración de desarrollo y producción, en la [documentación de Docker Compose](https://docs.docker.com/compose/how-tos/production/).
