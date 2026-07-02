# 🚀 Plan de Sprints - Velntra v1.0

> Ruta de desarrollo para terminar la primera versión del sistema.

---

# Información General

| Campo         | Valor              |
| ------------- | ------------------ |
| Proyecto      | Velntra          |
| Versión       | 1.0.0              |
| Base de datos | MySQL              |
| Arquitectura  | Modular Monolith   |
| Framework     | Laravel + Livewire |
| Estado        | Planificación      |

---

# Sprint 01 — Base del Proyecto

## Objetivo

Crear y configurar la base técnica del proyecto.

## Tareas

* Crear proyecto Laravel.
* Configurar repositorio Git.
* Crear rama `develop`.
* Configurar MySQL.
* Configurar archivo `.env`.
* Instalar Laravel Modules.
* Instalar Laravel Breeze.
* Instalar Livewire.
* Instalar Spatie Permission.
* Configurar TailwindCSS.
* Crear estructura inicial de módulos:

  * Core
  * Administration
  * Dashboard
  * Inventory
  * Customers
  * Sales
  * Settings
* Crear primer commit.

## Entregables

* Proyecto Laravel funcionando.
* Base de datos MySQL conectada.
* Login funcionando.
* Módulos creados.
* Repositorio Git configurado.

## Definition of Done

* El proyecto corre correctamente.
* El login funciona.
* MySQL está conectado.
* El repositorio tiene el primer commit.

---

# Sprint 02 — Administración

## Objetivo

Implementar usuarios, roles y permisos.

## Tareas

* Configurar Spatie Permission.
* Crear roles:

  * admin
  * seller
* Crear permisos base.
* Crear usuario administrador por defecto.
* Crear CRUD de usuarios.
* Crear UserService.
* Crear UserPolicy.
* Crear StoreUserRequest.
* Crear UpdateUserRequest.
* Crear vista de perfil.
* Crear cambio de contraseña.

## Entregables

* Módulo Administration funcional.

## Definition of Done

* El administrador puede iniciar sesión.
* El administrador puede gestionar usuarios.
* Los roles funcionan.
* Los permisos funcionan.

---

# Sprint 03 — Inventario

## Objetivo

Implementar categorías y productos.

## Tareas

## Categorías

* Crear migración `categories`.
* Crear modelo `Category`.
* Crear factory.
* Crear seeder.
* Crear CategoryService.
* Crear StoreCategoryRequest.
* Crear UpdateCategoryRequest.
* Crear CategoryPolicy.
* Crear CRUD con Livewire.
* Agregar búsqueda.
* Agregar paginación.
* Agregar Soft Delete.

## Productos

* Crear migración `products`.
* Crear modelo `Product`.
* Crear factory.
* Crear seeder.
* Crear ProductService.
* Crear StoreProductRequest.
* Crear UpdateProductRequest.
* Crear ProductPolicy.
* Crear CRUD con Livewire.
* Subir imagen de producto.
* Agregar búsqueda.
* Agregar filtros.
* Agregar paginación.
* Agregar Soft Delete.

## Entregables

* Módulo Inventory funcional.

## Definition of Done

* Se pueden crear categorías.
* Se pueden crear productos.
* Se puede buscar productos.
* Se puede filtrar productos.
* Se puede controlar stock inicial.
* Las validaciones funcionan.

---

# Sprint 04 — Clientes

## Objetivo

Implementar gestión de clientes.

## Tareas

* Crear migración `customers`.
* Crear modelo `Customer`.
* Crear factory.
* Crear seeder.
* Crear cliente por defecto: `Consumidor Final`.
* Crear CustomerService.
* Crear StoreCustomerRequest.
* Crear UpdateCustomerRequest.
* Crear CustomerPolicy.
* Crear CRUD con Livewire.
* Agregar búsqueda.
* Agregar paginación.
* Agregar historial de compras.

## Entregables

* Módulo Customers funcional.

## Definition of Done

* Se pueden crear clientes.
* Se pueden editar clientes.
* Se pueden buscar clientes.
* Existe el cliente `Consumidor Final`.
* `Consumidor Final` no se puede eliminar.

---

# Sprint 05 — Ventas

## Objetivo

Implementar el flujo completo de ventas.

## Tareas

* Crear migración `sales`.
* Crear migración `sale_items`.
* Crear modelo `Sale`.
* Crear modelo `SaleItem`.
* Crear SaleService.
* Crear CreateSaleAction.
* Crear CancelSaleAction.
* Crear GenerateSaleNumberAction.
* Crear StoreSaleRequest.
* Crear SalePolicy.
* Crear pantalla de venta tipo POS.
* Buscar productos.
* Buscar clientes.
* Usar `Consumidor Final` si no se selecciona cliente.
* Agregar productos al carrito.
* Cambiar cantidades.
* Eliminar productos del carrito.
* Calcular subtotal.
* Calcular descuento.
* Calcular IVA.
* Calcular total.
* Seleccionar método de pago:

  * Efectivo
  * Tarjeta
  * Transferencia
* Registrar venta.
* Descontar stock automáticamente.
* Crear vista de detalle de venta.
* Anular venta.
* Restaurar stock al anular venta.

## Entregables

* Módulo Sales funcional.

## Definition of Done

* Se puede registrar una venta completa.
* El stock disminuye correctamente.
* No se puede vender más del stock disponible.
* Se puede anular una venta.
* Al anular una venta, el stock se restaura.
* Las ventas anuladas no se pueden modificar.

---

# Sprint 06 — Dashboard

## Objetivo

Crear el panel principal del sistema.

## Tareas

* Crear DashboardService.
* Crear cards principales:

  * Ventas de hoy.
  * Ventas del mes.
  * Total productos.
  * Total clientes.
* Mostrar últimas ventas.
* Mostrar productos con stock bajo.
* Crear gráfico simple de ventas.
* Agregar accesos rápidos.

## Entregables

* Dashboard funcional.

## Definition of Done

* El dashboard muestra datos reales.
* Las ventas anuladas no aparecen en estadísticas.
* Los productos con bajo stock se muestran correctamente.

---

# Sprint 07 — Reportes

## Objetivo

Crear reportes básicos del negocio.

## Tareas

* Crear reporte de ventas.
* Agregar filtro por rango de fechas.
* Mostrar total vendido.
* Mostrar IVA total.
* Mostrar cantidad de ventas.
* Crear reporte de inventario.
* Mostrar stock actual.
* Mostrar valor del inventario.
* Crear reporte de clientes.
* Mostrar clientes con más compras.

## Entregables

* Módulo de reportes funcional.

## Definition of Done

* Los reportes muestran información correcta.
* Los filtros por fecha funcionan.
* Las ventas anuladas no se consideran en reportes.

---

# Sprint 08 — Configuración

## Objetivo

Permitir configurar la información del negocio.

## Tareas

* Crear migración `settings`.
* Crear modelo `Setting`.
* Crear SettingService.
* Crear UpdateSettingRequest.
* Crear SettingPolicy.
* Crear formulario de configuración.
* Guardar nombre del negocio.
* Guardar RUC.
* Guardar teléfono.
* Guardar correo.
* Guardar dirección.
* Subir logo.
* Configurar IVA.
* Configurar moneda.

## Entregables

* Módulo Settings funcional.

## Definition of Done

* La información del negocio se puede actualizar.
* El IVA configurado se usa en ventas.
* La moneda configurada se muestra en el sistema.

---

# Sprint 09 — Calidad y Pulido

## Objetivo

Revisar errores, mejorar experiencia de usuario y dejar el sistema estable.

## Tareas

* Revisar validaciones.
* Revisar permisos.
* Revisar policies.
* Corregir errores encontrados.
* Mejorar diseño visual.
* Mejorar responsive.
* Crear datos de prueba.
* Revisar mensajes de éxito.
* Revisar mensajes de error.
* Revisar confirmaciones antes de eliminar.
* Revisar flujo completo desde login hasta venta.

## Entregables

* Sistema estable.

## Definition of Done

* No existen errores críticos.
* El sistema se puede usar de inicio a fin.
* La interfaz se ve presentable.
* Hay datos de prueba suficientes.

---

# Sprint 10 — Portafolio y Publicación

## Objetivo

Preparar el proyecto para mostrarlo profesionalmente.

## Tareas

* Crear README completo.
* Crear guía de instalación.
* Agregar capturas del sistema.
* Preparar datos demo.
* Desplegar aplicación.
* Grabar video demo.
* Escribir descripción del proyecto para portafolio.
* Crear sección del proyecto en tu portafolio.
* Revisar repositorio GitHub.

## Entregables

* Proyecto listo para portafolio.

## Definition of Done

* El proyecto está en GitHub.
* El proyecto está desplegado.
* El README explica cómo instalarlo.
* Hay capturas.
* Hay video demo.
* El proyecto se puede mostrar en entrevistas.

---

# Criterio Final de Lanzamiento

Velntra v1.0 estará terminado cuando:

* Todos los sprints estén completados.
* El flujo de venta funcione correctamente.
* El inventario se actualice automáticamente.
* Los reportes funcionen.
* El sistema tenga datos demo.
* El proyecto esté desplegado.
* El README esté completo.
* El proyecto esté listo para el portafolio.

---

# Regla Principal

Durante la versión 1.0 no se agregarán nuevas funcionalidades fuera de este plan.

Toda nueva idea irá al archivo:

```text
Roadmap.md
```
