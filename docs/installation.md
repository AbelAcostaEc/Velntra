# Instalación local

Esta guía instala Velntra desde un clon nuevo sin requerir PHP, Composer ni Node en el equipo anfitrión. Requiere Docker Engine y Docker Compose, y los puertos 8000 (web), 3307 (MySQL) y 5173 (Vite de desarrollo) libres o cambiados en `.env`.

## 1. Variables locales

En la raíz del repositorio:

```bash
cp .env.example .env
cp src/.env.example src/.env
```

El `.env` de la raíz configura Docker Compose; `src/.env` configura Laravel. Si cambias `DB_DATABASE`, `DB_USERNAME` o `DB_PASSWORD` en la raíz, cambia también esos valores en `src/.env`. Dentro de Docker, Laravel debe conectarse a `DB_HOST=mysql` y `DB_PORT=3306` aunque el puerto publicado en el anfitrión sea 3307.

Si el usuario de tu equipo no tiene UID/GID 1000, ajusta `WWWUSER` y `WWWGROUP` en el `.env` raíz con los valores de `id -u` e `id -g` para que los archivos generados no queden con un propietario inesperado. Cambia `APP_URL` en `src/.env` si cambias `APP_PORT`.

## 2. Dependencias, clave y assets

Ejecuta en este orden:

```bash
docker compose build app
docker compose run --rm --no-deps app composer install
docker compose run --rm --no-deps app php artisan key:generate
docker compose run --rm node npm ci
docker compose run --rm node npm run build
docker compose up -d
docker compose exec -T app php artisan storage:link
```

El primer arranque de `app` espera que MySQL esté saludable y ejecuta `migrate --force` y `db:seed --force`. Este último crea permisos, roles, un administrador inicial, monedas y configuración base; no crea categorías ni productos del negocio. En reinicios posteriores conserva la contraseña del administrador y los datos de configuración editados.

Comprueba el estado:

```bash
docker compose ps
docker compose exec -T app php artisan migrate:status
docker compose exec -T app php artisan test --compact
```

Abre `http://localhost:8000`. En **desarrollo local**, el primer acceso es `admin@velntra.com` / `admin@velntra.com`; cambia la contraseña desde el perfil. El registro público está deshabilitado: los demás usuarios se crean desde Administración.

## 3. Datos de demostración opcionales

En una **base aislada y desechable**, puedes cargar un catálogo y ventas ficticias con el comando de [`docs/demo.md`](demo.md). No forma parte del arranque normal ni debe ejecutarse sobre los datos de un negocio.

## 4. Desarrollo y operación

Para ver logs: `docker compose logs -f app nginx mysql`. Para recompilar estilos: `docker compose run --rm node npm run build`. Para detener contenedores **sin borrar los datos**: `docker compose down`.

El volumen `velntra_mysql_data` contiene MySQL. **`docker compose down -v` borra ese volumen y todas las ventas, clientes y productos guardados.** Haz una copia de seguridad antes de recrearlo. Las imágenes y logos se guardan en `src/storage/app/public`, por lo que también deben incluirse en una copia de seguridad.

Si `app` no inicia desde un clon nuevo, revisa que exista `src/vendor` (paso `composer install`), que `src/.env` tenga `APP_KEY` y que MySQL esté saludable. Si la web no tiene estilos, comprueba que exista `src/public/build/manifest.json` y vuelve a ejecutar el build. Si las imágenes cargadas no aparecen, ejecuta de nuevo `php artisan storage:link`.

Esta composición Docker es para desarrollo local. Para exponer Velntra en internet, sigue la [guía de publicación](deployment.md), no publiques los puertos MySQL/Vite ni mantengas credenciales iniciales.
