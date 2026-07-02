# Guía de Desarrollo - Docker para Laravel

Esta guía sirve para usar este Docker como base de cualquier proyecto Laravel sin instalar PHP, Composer, MySQL ni Node directamente en tu máquina.

## Objetivo

El directorio `src/` contiene el proyecto Laravel. La carpeta `docker/` contiene la base reutilizable para levantar PHP-FPM, Nginx, MySQL y Node.

```text
velntra/
├── docker/
│   ├── nginx/
│   └── php/
├── docker-compose.yml
├── .env.example
└── src/
```

## Configuración inicial

Antes de levantar Docker, copia el archivo de variables de la raíz:

```bash
cp .env.example .env
```

Ese archivo `.env` pertenece a Docker Compose, no al `.env` de Laravel dentro de `src/`.

Valores principales:

```env
COMPOSE_PROJECT_NAME=velntra
APP_PORT=8000
FORWARD_DB_PORT=3307
VITE_PORT=5173
DB_DATABASE=velntra
DB_USERNAME=velntra
DB_PASSWORD=secret
DB_ROOT_PASSWORD=root
WWWUSER=1000
WWWGROUP=1000
```

`COMPOSE_PROJECT_NAME` es importante porque Docker Compose usa ese nombre para generar los contenedores, la red y el volumen. Si copias esta plantilla para otro proyecto, cambia ese valor para evitar choques con otros entornos.

## Levantar el entorno

Desde la raíz del proyecto:

```bash
docker compose up -d --build
```

Verifica el estado:

```bash
docker compose ps
```

Abre la aplicación en:

```text
http://localhost:8000
```

Si usas un dominio local, por ejemplo `velntra.test`, recuerda configurarlo en `/etc/hosts` y en `docker/nginx/default.conf`.

## Crear o preparar Laravel

Si `src/` está vacío, puedes crear Laravel dentro de ese directorio:

```bash
docker compose exec app composer create-project laravel/laravel .
```

Si `src/` ya tiene archivos, Composer puede rechazar la instalación. En ese caso, crea Laravel en una carpeta vacía o limpia `src/` antes de instalar.

Configura `src/.env` con la conexión a MySQL del contenedor:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=velntra
DB_USERNAME=velntra
DB_PASSWORD=secret
```

Luego ejecuta:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Si Laravel necesita corregir permisos:

```bash
docker compose exec -u root app chown -R 1000:1000 /var/www/html
docker compose exec app chmod -R 775 storage bootstrap/cache
```

## Node, Vite y Tailwind

Instalar dependencias:

```bash
docker compose run --rm node npm install
```

Modo desarrollo:

```bash
docker compose run --rm --service-ports node npm run dev
```

Compilar assets:

```bash
docker compose run --rm node npm run build
```

## Puertos

El puerto web externo se controla desde el `.env` de la raíz:

```env
APP_PORT=8000
```

Para usar `http://localhost:8081`, cambia:

```env
APP_PORT=8081
```

Luego aplica:

```bash
docker compose up -d
```

También cambia `APP_URL` en `src/.env`:

```env
APP_URL=http://localhost:8081
```

El puerto externo de MySQL también se controla desde el `.env` raíz:

```env
FORWARD_DB_PORT=3307
```

Laravel siempre usa el puerto interno:

```env
DB_HOST=mysql
DB_PORT=3306
```

Desde tu PC, DBeaver u otro cliente usa el puerto externo, por ejemplo `3307`.

El puerto de Vite se controla con:

```env
VITE_PORT=5173
```

## Conectarse con DBeaver

Conexión normal:

```text
Host: localhost
Port: 3307
Database: velntra
Username: velntra
Password: secret
```

Usuario root, si lo necesitas:

```text
Host: localhost
Port: 3307
Database: velntra
Username: root
Password: root
```

Importante: en Laravel el host es `mysql`, pero desde tu PC el host es `localhost`.

## Evitar choques entre proyectos

Este Docker no usa `container_name` fijo. Eso permite que Docker Compose genere nombres usando `COMPOSE_PROJECT_NAME`.

Para otro proyecto, por ejemplo `inventario`, cambia el `.env` raíz:

```env
COMPOSE_PROJECT_NAME=inventario
APP_PORT=8001
FORWARD_DB_PORT=3308
VITE_PORT=5174
DB_DATABASE=inventario
DB_USERNAME=inventario
DB_PASSWORD=secret
```

Con eso, Compose generará recursos como:

```text
inventario-app-1
inventario-nginx-1
inventario-mysql-1
inventario_velntra_mysql_data
```

Aunque el volumen se llame `velntra_mysql_data` dentro del `docker-compose.yml`, Docker Compose lo prefija con `COMPOSE_PROJECT_NAME`, por eso no choca entre proyectos distintos.

## Si Docker dice que el contenedor o volumen ya existe

Primero revisa si ya hay un entorno levantado desde esa carpeta:

```bash
docker compose ps
```

Si solo quieres levantarlo de nuevo:

```bash
docker compose up -d
```

Si quedó un entorno viejo de la misma plantilla, detenlo:

```bash
docker compose down
```

Si el choque viene de contenedores creados antes con `container_name` fijo, elimínalos manualmente una sola vez:

```bash
docker rm -f velntra_app velntra_nginx velntra_mysql velntra_node
```

Si el problema es el volumen viejo y no necesitas conservar esa base de datos:

```bash
docker volume rm velntra_mysql_data
```

Si sí necesitas conservar datos, no borres el volumen. Cambia `COMPOSE_PROJECT_NAME` o migra la información antes.

## Dominios locales en Linux

Puedes usar un dominio local como:

```text
velntra.test
```

Edita `/etc/hosts`:

```bash
sudo nano /etc/hosts
```

Agrega:

```text
127.0.0.1 velntra.test
```

En `docker/nginx/default.conf`, usa:

```nginx
server_name velntra.test;
```

En `src/.env`:

```env
APP_URL=http://velntra.test:8000
```

Reinicia Nginx:

```bash
docker compose restart nginx
```

Si quieres abrir `http://velntra.test` sin puerto, cambia el puerto web a:

```env
APP_PORT=80
```

Luego:

```bash
docker compose up -d
```

Nota: el puerto `80` puede chocar con Apache, Nginx u otro servicio instalado en Linux.

## Comandos útiles

Ver contenedores:

```bash
docker compose ps
```

Ver logs:

```bash
docker compose logs -f
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql
```

Entrar al contenedor PHP:

```bash
docker compose exec app bash
```

Instalar dependencias PHP:

```bash
docker compose exec app composer install
```

Ejecutar comandos Artisan:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
```

Detener:

```bash
docker compose down
```

Detener y borrar también la base de datos del volumen:

```bash
docker compose down -v
```

## Permisos en Linux

Este Docker está preparado para que PHP y Node creen archivos con el mismo UID/GID de tu usuario Linux.

Puedes ver tus valores con:

```bash
id -u
id -g
```

Si tu usuario no usa `1000:1000`, cambia el `.env` de la raíz:

```env
WWWUSER=1001
WWWGROUP=1001
```

Luego reconstruye:

```bash
docker compose up -d --build
```

## Resumen rápido

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose run --rm node npm install
docker compose run --rm --service-ports node npm run dev
```

Con esto tienes un entorno Docker listo para Laravel, MySQL, Composer, Nginx y Node sin instalar esas herramientas directamente en tu máquina.
