# Guía de desarrollo

La instalación desde un clon nuevo, las variables Docker/Laravel y los comandos de verificación están en [`docs/installation.md`](docs/installation.md). El [README principal](README.md) resume el proyecto; [`docs/deployment.md`](docs/deployment.md) cubre los requisitos para publicarlo.

## Ciclo diario

```bash
docker compose up -d
docker compose ps
docker compose exec -T app php artisan test --compact
docker compose run --rm node npm run build
```

Docker Compose usa el `.env` de la raíz; Laravel, `src/.env`. El contenedor `app` ejecuta migraciones y seeders estructurales al arrancar. Los datos opcionales de portafolio se explican en [`docs/demo.md`](docs/demo.md).

Para detener sin perder MySQL: `docker compose down`. **No uses `down -v` salvo que quieras borrar deliberadamente el volumen de la base de datos.**
