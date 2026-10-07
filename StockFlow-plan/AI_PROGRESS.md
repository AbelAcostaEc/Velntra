# AI Progress - Velntra

## Estado actual

Sprint actual: cierre técnico del Sprint 05 - Ventas.

Estado general: núcleo operativo del MVP implementado. Los Sprints 01 a 04 están
completos y validados mediante pruebas automatizadas y build en Docker. El
Sprint 08 fue adelantado y está casi completo.

Última actualización: 2026-10-07.

## Avance por sprint

| Sprint | Estado | Observaciones |
| --- | ---: | --- |
| 01 - Base | 100 % | Proyecto, Docker, módulos, autenticación, MySQL y dependencias configurados. |
| 02 - Administración | 100 % | Usuarios, roles, permisos, perfil, servicios, policies y pruebas validadas. El cierre del registro público queda como decisión previa a producción. |
| 03 - Inventario | 100 % | Categorías y productos con CRUD, búsqueda, filtros, imágenes, paginación y soft delete validados. Productos y categorías no requieren seeders porque son datos propios de cada negocio. |
| 04 - Clientes | 100 % | CRUD, Consumidor Final, búsqueda, historial, estadísticas y edición desde POS implementados y probados. |
| 05 - Ventas | 90 % | POS, carrito, impuestos, descuento, métodos de pago, control de stock, ventas pendientes y anulaciones implementados. Pendiente validación integral del flujo. |
| 06 - Dashboard | 10 % | La vista actual usa información demostrativa; faltan métricas reales, últimas ventas, stock bajo y gráfico. |
| 07 - Reportes | 0 % | Pendientes reportes de ventas, inventario y clientes. |
| 08 - Configuración | 90 % | Empresa, moneda, IVA y logo implementados; el IVA y la moneda se integran con ventas. |
| 09 - Calidad | 40 % | Existe una suite amplia de feature tests; faltan ejecución certificada, revisión responsive y flujo integral. |
| 10 - Publicación | 5 % | Pendientes README, guía final, capturas, demo y despliegue. |

## Decisiones arquitectónicas aprobadas

### Ventas

- `SaleService` encapsula las operaciones transaccionales de creación,
  finalización, anulación y eliminación de ventas pendientes.
- Las Actions no son una capa obligatoria.
- Una Action solo se extraerá si una operación se vuelve difícil de comprender,
  reutilizar o probar dentro del servicio.

### Validación Livewire

- Los flujos Livewire no utilizarán Form Requests como requisito arquitectónico.
- La entrada se validará con `rules()`, `#[Validate]` o Livewire Form Objects.
- Las reglas críticas de negocio permanecerán protegidas en los Services.

### Datos iniciales de inventario

- No se crearán seeders de categorías ni productos reales, porque esos datos
  dependen de cada negocio.
- Factories o datos ficticios solo se agregarán cuando sean útiles para pruebas o
  para el entorno de demostración.
- Se mantienen seeders para datos estructurales del sistema, como permisos,
  roles, administrador, monedas y configuración base.

## Implementación confirmada

- Los módulos Core, Administration, Dashboard, Inventory, Customers, Sales y
  Settings están creados y habilitados.
- Administración incluye usuarios, roles, permisos, perfil y cambio de contraseña.
- Inventario incluye categorías, productos, imágenes, filtros, búsqueda y stock.
- Clientes incluye Consumidor Final, CRUD e historial de compras.
- Ventas incluye POS, carrito, ventas pendientes, cobro, descuento de stock y
  anulación con restauración de stock.
- Configuración incluye información de empresa, logo, IVA y moneda.
- Hay pruebas feature para administración, inventario, clientes, ventas y
  configuración, además de autenticación, perfil e idioma.

## Verificación técnica ejecutada

- Contenedores `app`, `mysql`, `nginx` y `node`: activos; MySQL saludable.
- Suite base: 30 pruebas aprobadas, 89 aserciones.
- Suite de módulos: 98 pruebas aprobadas, 336 aserciones.
- Total verificado: 128 pruebas y 425 aserciones aprobadas.
- Build Vite de producción: aprobado.
- Las 17 migraciones aparecen aplicadas.
- Laravel registró correctamente 78 rutas de aplicación.

Nota: `php artisan test` ejecuta la suite base, pero no descubre automáticamente
los tests ubicados en `Modules/*/tests`. Hasta ajustar `phpunit.xml`, la suite de
módulos debe ejecutarse indicando explícitamente sus directorios.

Queda recomendada una prueba manual del flujo:

```text
login -> cliente -> categoría -> producto -> venta -> anulación -> historial
```

## Próximas prioridades

1. Probar manualmente el flujo completo de ventas.
2. Ajustar `phpunit.xml` para incluir automáticamente los tests modulares.
3. Decidir y aplicar el cierre del registro público para producción.
4. Implementar Sprint 06 - Dashboard con datos reales.
5. Implementar Sprint 07 - Reportes.
6. Completar Sprint 09 - Calidad y pulido.
7. Preparar Sprint 10 - Publicación y portafolio.

## Notas

- MySQL es la base de datos oficial. Cualquier referencia antigua a PostgreSQL
  debe interpretarse como documentación obsoleta.
- No deben agregarse funcionalidades fuera del alcance de Velntra v1.0.
