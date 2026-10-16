# Texto para el portafolio

## Velntra — Inventario y punto de venta

Velntra es una aplicación web para pequeños negocios de una sola sucursal. Reúne catálogo, clientes, ventas y reportes en un mismo flujo: al cobrar una venta actualiza el stock; al anularla lo restituye. Su Dashboard resume actividad y alertas, mientras que los reportes de ventas, inventario y clientes se pueden filtrar y exportar en Excel o PDF.

**Mi trabajo:** diseño e implementación del MVP con Laravel, Livewire y MySQL; definición de permisos por rol; construcción del POS, dashboard y reportes; pruebas automatizadas del flujo de negocio; y preparación de un entorno Docker reproducible.

**Tecnologías:** PHP 8.3, Laravel 12, Livewire 4, Blade, Tailwind CSS, MySQL 8, Docker Compose y Vite.

**Desafío técnico destacado:** mantener coherentes venta, impuestos, pagos y existencias en operaciones transaccionales. `SaleService` encapsula la creación, finalización y anulación; las pruebas cubren tanto el descuento como la restitución de stock.

**Límites de v1.0:** una empresa/sucursal; no incluye facturación electrónica ni pasarela de pagos. Las capturas de esta presentación usan datos ficticios de una base aislada.

## Material para la ficha del proyecto

- Imagen principal: [`dashboard.png`](screenshots/dashboard.png).
- Galería: [`products.png`](screenshots/products.png), [`pos.png`](screenshots/pos.png), [`reports.png`](screenshots/reports.png) y [`sales-history-mobile.png`](screenshots/sales-history-mobile.png).
- Código: enlazar el repositorio cuando los commits de publicación estén en GitHub.
- Demo en vivo: pendiente de hosting y dominio.
- Video: pendiente de grabación con el [guion de demostración](demo.md).

No coloques enlaces de demo o video hasta que existan y estén verificados. Si el portafolio tiene una sección de «Resultados», destaca el flujo completo comprobado: producto → venta → descuento de stock → anulación → restitución de stock.
