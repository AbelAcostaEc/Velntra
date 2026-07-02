# Dev Guide - Laravel Docker

Esta guia sirve para usar este Docker como base de cualquier proyecto Laravel sin instalar PHP, Composer, MySQL ni Node directamente en tu maquina.

## Estructura

```text
velntra/
├── docker/
│   ├── nginx/
│   └── php/
├── docker-compose.yml
└── src/
```

- `src/`: aqui vive el proyecto Laravel.
- `docker/php/`: imagen PHP con Composer y extensiones comunes para Laravel.
- `docker/nginx/`: configuracion de Nginx.
- `mysql`: base de datos interna del entorno.
- `node`: contenedor para npm, Vite, Tailwind, etc.

## Comandos para iniciar

Desde la carpeta del proyecto:

```bash
docker compose up -d --build
```

Crear Laravel dentro de `src`:

```bash
docker compose exec app composer create-project laravel/laravel .
```

Si `src` ya tiene archivos, Composer puede que no quiera instalar. En ese caso crea Laravel en una carpeta vacia o limpia `src` antes de instalar.

Permisos de Laravel:

```bash
docker compose exec app chmod -R 775 storage bootstrap/cache
```

Configurar `src/.env`:

```env
APP_NAME=Velntra
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=velntra
DB_USERNAME=velntra
DB_PASSWORD=secret
```

Generar key si hace falta:

```bash
docker compose exec app php artisan key:generate
```

Migrar:

```bash
docker compose exec app php artisan migrate
```

Node para Tailwind/Vite:

```bash
docker compose run --rm node npm install
docker compose run --rm node npm run dev
```

Entrar al contenedor PHP:

```bash
docker compose exec app bash
```

## Puertos

### Cambiar el puerto web

Actualmente Nginx se publica asi en `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "8000:80"
```

El primer numero es el puerto de tu PC. El segundo es el puerto interno del contenedor.

Para mostrar el proyecto en `http://localhost:8081`, cambia a:

```yaml
nginx:
  ports:
    - "8081:80"
```

Luego reinicia:

```bash
docker compose up -d
```

Y cambia `APP_URL` en `src/.env`:

```env
APP_URL=http://localhost:8081
```

### Cambiar el puerto externo de MySQL

Actualmente MySQL se publica asi:

```yaml
mysql:
  ports:
    - "3307:3306"
```

Desde otros contenedores Laravel usa siempre:

```env
DB_HOST=mysql
DB_PORT=3306
```

Desde tu PC, DBeaver u otro cliente usa el puerto externo, en este caso `3307`.

Para usar otro puerto en tu PC, por ejemplo `3310`, cambia:

```yaml
mysql:
  ports:
    - "3310:3306"
```

Luego:

```bash
docker compose up -d
```

## Conectarse con DBeaver

Crear una conexion MySQL con estos datos:

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

Importante: en Laravel el host es `mysql`, pero en DBeaver desde tu PC el host es `localhost`.

## Personalizar para otro proyecto Laravel

Cuando copies esta plantilla para otro proyecto, cambia estos valores:

1. Nombre de contenedores en `docker-compose.yml`:

```yaml
container_name: velntra_app
container_name: velntra_nginx
container_name: velntra_mysql
container_name: velntra_node
```

Ejemplo para `inventario`:

```yaml
container_name: inventario_app
container_name: inventario_nginx
container_name: inventario_mysql
container_name: inventario_node
```

2. Nombre de red y volumen:

```yaml
volumes:
  velntra_mysql_data:

networks:
  velntra:
```

Ejemplo:

```yaml
volumes:
  inventario_mysql_data:

networks:
  inventario:
```

Tambien cambia cada `networks: - velntra` por el nuevo nombre.

3. Base de datos:

```yaml
MYSQL_DATABASE: velntra
MYSQL_USER: velntra
MYSQL_PASSWORD: secret
```

Y en `src/.env`:

```env
DB_DATABASE=velntra
DB_USERNAME=velntra
DB_PASSWORD=secret
```

4. Puerto web si tienes varios proyectos corriendo:

```yaml
ports:
  - "8000:80"
```

Usa uno diferente por proyecto, por ejemplo `8001`, `8002`, `8081`.

5. Puerto externo de MySQL si tienes varios proyectos:

```yaml
ports:
  - "3307:3306"
```

Usa uno diferente por proyecto, por ejemplo `3308`, `3309`, `3310`.

## Dominios locales en Linux

Puedes usar un dominio local como:

```text
velntra.test
```

### 1. Editar `/etc/hosts`

Abre el archivo:

```bash
sudo nano /etc/hosts
```

Agrega:

```text
127.0.0.1 velntra.test
```

Guarda el archivo.

### 2. Configurar Nginx

En `docker/nginx/default.conf`, cambia:

```nginx
server_name localhost;
```

Por:

```nginx
server_name velntra.test;
```

### 3. Configurar Laravel

En `src/.env`:

```env
APP_URL=http://velntra.test:8000
```

Si cambiaste el puerto web a `8081`:

```env
APP_URL=http://velntra.test:8081
```

### 4. Reiniciar Docker

```bash
docker compose restart nginx app
```

Abre:

```text
http://velntra.test:8000
```

Si quieres usar `http://velntra.test` sin puerto, cambia el puerto web a:

```yaml
nginx:
  ports:
    - "80:80"
```

Luego:

```bash
docker compose up -d
```

Nota: usar el puerto `80` puede chocar con Apache, Nginx u otro servicio instalado en tu Linux.

## Comandos utiles

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

Detener:

```bash
docker compose down
```

Detener y borrar la base de datos del volumen:

```bash
docker compose down -v
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

Ejecutar npm:

```bash
docker compose run --rm node npm install
docker compose run --rm node npm run dev
docker compose run --rm node npm run build
```

## Permisos

Este Docker esta preparado para que el contenedor PHP y Node creen archivos con el mismo UID/GID de tu usuario Linux.

Si algun archivo queda con permisos incorrectos, corrige una vez con:

```bash
docker compose exec -u root app chown -R 1000:1000 /var/www/html
docker compose exec app chmod -R 775 storage bootstrap/cache
```

Si tu usuario Linux no usa UID/GID `1000`, crea un archivo `.env` en la raiz del Docker, al lado de `docker-compose.yml`:

```env
WWWUSER=1001
WWWGROUP=1001
```

Puedes ver tus valores con:

```bash
id -u
id -g
```

Luego reconstruye:

```bash
docker compose up -d --build
```

## Resumen rapido

```bash
docker compose up -d --build
docker compose exec app composer create-project laravel/laravel .
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose run --rm node npm install
docker compose run --rm node npm run dev
```

Con esto tienes un entorno Docker listo para Laravel, MySQL, Composer, Nginx y Node sin instalar esas herramientas directamente en tu maquina.
