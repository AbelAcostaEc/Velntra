# AI Progress - Velntra

## Estado actual

Sprint actual: Sprint 09 - Calidad y pulido.

Estado general: núcleo operativo del MVP implementado. Los Sprints 01 a 04 están
completos y validados mediante pruebas automatizadas y build en Docker. El
Sprint 05 quedó validado con pruebas automatizadas reforzadas y con la prueba
manual integral previamente realizada. El Sprint 06 ya funciona con datos
reales y quedó cubierto por pruebas. El Sprint 07 incluye tres componentes de
reportes independientes, exportaciones verificadas y botones funcionales. El
Sprint 08 cumple su Definition of Done: la moneda configurada se muestra en
POS, catálogo, historial, dashboard y reportes.

Última actualización: 2026-10-16.

## Avance por sprint

| Sprint | Estado | Observaciones |
| --- | ---: | --- |
| 01 - Base | 100 % | Proyecto, Docker, módulos, autenticación, MySQL y dependencias configurados. |
| 02 - Administración | 100 % | Usuarios, roles, permisos, perfil, servicios, policies y pruebas validadas. El registro público está deshabilitado; las cuentas se administran internamente. |
| 03 - Inventario | 100 % | Categorías y productos con CRUD, búsqueda, filtros, imágenes, paginación y soft delete validados. Productos y categorías no requieren seeders porque son datos propios de cada negocio. |
| 04 - Clientes | 100 % | CRUD, Consumidor Final, búsqueda, historial, estadísticas y edición desde POS implementados y probados. |
| 05 - Ventas | 100 % | POS, carrito, impuestos, descuento, métodos de pago, control de stock, ventas pendientes y anulaciones implementados y validados. |
| 06 - Dashboard | 100 % | Métricas reales, últimas ventas, stock bajo, gráfico con períodos predefinidos y rango personalizado, accesos rápidos, permisos y diseño responsive implementados y probados. |
| 07 - Reportes | 100 % | Reportes independientes de ventas, inventario y clientes con filtros, métricas, paginación, permisos y exportación Excel/PDF. Las pruebas leen el contenido Excel y verifican los PDF. |
| 08 - Configuración | 100 % | Empresa, moneda, IVA y logo implementados. El IVA se usa en ventas y el símbolo de moneda configurado aparece en las vistas monetarias del MVP. |
| 09 - Calidad | 60 % | La suite y el build pasan. Hay prueba integral automatizada y permisos reforzados. El usuario confirmó Reportes responsive y los flujos de creación probados en navegador. Faltan revisar POS en móvil, abrir visualmente PDF/Excel y completar el recorrido manual de venta/anulación. |
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
- El registro público está deshabilitado; los usuarios se crean desde el módulo
  de Administración o mediante el administrador inicial estructural.
- Inventario incluye categorías, productos, imágenes, filtros, búsqueda y stock.
- Clientes incluye Consumidor Final, CRUD e historial de compras.
- Ventas incluye POS, carrito, ventas pendientes, cobro, descuento de stock y
  anulación con restauración de stock.
- Dashboard incluye ventas del día y del mes, productos y clientes activos,
  últimas ventas, alertas de stock bajo y evolución por últimos siete días, mes
  actual, mes anterior o un rango personalizado de hasta 93 días.
- Reportes incluye componentes y rutas independientes para ventas, inventario y
  clientes. Cada reporte exporta el conjunto filtrado completo en Excel y PDF;
  las métricas excluyen ventas pendientes y anuladas. Los botones de exportación
  se renderizan dentro de Livewire y las pruebas leen los datos exportados.
- Configuración incluye información de empresa, logo, IVA y moneda. El símbolo
  de moneda seleccionado se refleja en POS, productos, historial de compras,
  dashboard y reportes. Cambiar la moneda no convierte importes existentes.
- Hay pruebas feature para administración, inventario, clientes, ventas,
  dashboard y configuración, además de autenticación, perfil e idioma.
- La prueba integral automatizada recorre login, alta de categoría, producto y
  cliente, venta, anulación, restitución de stock y coherencia de métricas.
- El POS exige permisos en acciones de ventas pendientes y de clientes; el
  historial de compras limita los recibos al cliente consultado.

## Verificación técnica ejecutada

- Contenedores `app`, `mysql`, `nginx` y `node`: activos; MySQL saludable.
- Suite unificada: 164 pruebas y 598 aserciones aprobadas (2026-10-15).
- Build Vite de producción: aprobado.
- Validación manual comunicada por el usuario (2026-10-16): Reportes funciona
  en responsive y los flujos de creación que probó funcionan. No incluye aún
  confirmación de apertura visual de PDF/Excel ni del POS en móvil.
- Las 17 migraciones aparecen aplicadas.
- Laravel registró correctamente 78 rutas de aplicación.
- El contenedor `app` ejecuta migraciones y seeders estructurales al arrancar;
  después de recrear el volumen quedan disponibles permisos, roles y el
  administrador inicial.

`phpunit.xml` incluye la suite base y todos los tests de `Modules/*/tests`, por lo
que `php artisan test` valida el proyecto completo con un solo comando.

La prueba automatizada no sustituye el recorrido manual en navegador:

```text
login -> cliente -> categoría -> producto -> venta -> anulación -> historial
```

## Próximas prioridades

1. Completar Sprint 09 - Calidad y pulido.
2. Preparar Sprint 10 - Publicación y portafolio.

## Notas

- MySQL es la base de datos oficial. Cualquier referencia antigua a PostgreSQL
  debe interpretarse como documentación obsoleta.
- No deben agregarse funcionalidades fuera del alcance de Velntra v1.0.
