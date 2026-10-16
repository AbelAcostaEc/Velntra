# Demostración de Velntra

Los ejemplos son **ficticios**. Úsalos únicamente en una base aislada para portafolio: no representan categorías ni productos de un negocio real y nunca se cargan en el arranque normal.

## Preparar una base demo

Si el entorno principal ya está activo, usa un **proyecto Compose distinto** con otros puertos. Comparte el código fuente, pero crea una red y un volumen MySQL propios:

```bash
env APP_PORT=8001 FORWARD_DB_PORT=3308 VITE_PORT=5174 docker compose -p velntra-portfolio-demo up -d
env APP_PORT=8001 FORWARD_DB_PORT=3308 VITE_PORT=5174 docker compose -p velntra-portfolio-demo exec -T app php artisan db:seed --class='Database\Seeders\PortfolioDemoSeeder' --force
```

Abre `http://localhost:8001`. Mantén los mismos puertos alternativos en **cada** comando `up` o `down` de este proyecto: si los omites, Compose intentará volver a los puertos 8000/3307 del entorno principal. Para detener la demo sin borrar su base:

```bash
env APP_PORT=8001 FORWARD_DB_PORT=3308 VITE_PORT=5174 docker compose -p velntra-portfolio-demo down
```

El seeder opcional agrega tres categorías `DEMO-*`, seis productos, dos clientes ficticios, cuatro ventas recientes y una venta en espera. Puede ejecutarse dos veces sin duplicar ventas ni volver a descontar stock. Está bloqueado cuando `APP_ENV=production`. Para una demo pública futura, prepara una instancia aislada antes de ponerla en línea y configura credenciales únicas; no copies datos personales del entorno real.

## Recorrido sugerido (3–4 minutos)

1. Entrar al Dashboard y mostrar métricas, alertas de stock bajo y el selector de rango del gráfico.
2. Abrir Categorías y Productos; buscar un producto `DEMO-*`, ver su stock y precio.
3. Abrir Clientes y mostrar los registros ficticios.
4. En el POS, agregar un producto, elegir cliente, cobrar y comprobar el descuento de stock. Mostrar también la venta en espera.
5. Abrir Historial de Ventas, consultar el ticket y anular la venta recién creada; confirmar la restitución de stock.
6. Abrir los tres Reportes, cambiar filtros y descargar Excel/PDF.
7. Mostrar el mismo historial o un reporte en un ancho móvil para demostrar las tarjetas responsive.

## Capturas para el portafolio

Las siguientes capturas se generaron desde la **base demo aislada**, sin nombres ni ventas reales. Están en `docs/screenshots/`:

| Archivo | Qué mostrar |
| --- | --- |
| [`dashboard.png`](screenshots/dashboard.png) | Métricas y gráfico con un período seleccionado. |
| [`products.png`](screenshots/products.png) | Catálogo y stock visible. |
| [`pos.png`](screenshots/pos.png) | Carrito preparado antes del cobro, sin datos sensibles. |
| [`sales-history-mobile.png`](screenshots/sales-history-mobile.png) | Tarjetas y acciones del historial en móvil. |
| [`reports.png`](screenshots/reports.png) | Reporte de ventas con filtros y botones de exportación. |

Puedes regenerar las cinco capturas automáticamente mientras la demo local esté activa:

```bash
node scripts/capture-demo.mjs
```

El script requiere Node 24+ y Chrome/Chromium local, solo acepta `localhost`/`127.0.0.1` y verifica que exista el catálogo ficticio antes de escribir imágenes. Permite configurar `DEMO_BASE_URL`, `DEMO_EMAIL`, `DEMO_PASSWORD` y `CHROME_BINARY`; si usas un puerto distinto de 8001, exige `DEMO_CONFIRM_ISOLATED=1`. El perfil temporal del navegador se elimina al terminar. Evita mostrar credenciales, tokens, datos personales o claves en capturas/video. El video debe grabarse con la aplicación real y comprobarse antes de enlazarlo en el README; esta guía sirve como guion, no afirma que ya exista una grabación.
