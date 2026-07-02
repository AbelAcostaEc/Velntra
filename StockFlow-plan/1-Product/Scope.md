
# 📋 Scope - Velntra v1.0

> Documento de alcance del proyecto.

---

# Información General

| Campo | Valor |
|--------|--------|
| Proyecto | Velntra |
| Versión | 1.0.0 |
| Estado | En desarrollo |
| Tipo | MVP (Minimum Viable Product) |

---

# Objetivo del MVP

Desarrollar una aplicación web que permita a un pequeño negocio administrar su catálogo de productos, controlar el stock disponible, registrar ventas y consultar información básica del negocio desde un único sistema.

El objetivo de esta primera versión es entregar un producto completamente funcional, estable y fácil de utilizar.

---

# Alcance General

La versión **1.0** estará diseñada para un único negocio.

El sistema administrará:

- Una empresa.
- Una sucursal.
- Un único inventario.
- Un administrador.
- Usuarios internos.
- Clientes.
- Productos.
- Ventas.

No se contemplan escenarios empresariales complejos.

---

# Módulos Incluidos

- Autenticación
- Dashboard
- Categorías
- Productos
- Clientes
- Ventas
- Reportes
- Configuración

---

# Módulo: Autenticación

## Objetivo

Permitir el acceso seguro al sistema.

## Incluye

- Inicio de sesión.
- Cierre de sesión.
- Recuperación de contraseña.
- Perfil de usuario.
- Cambio de contraseña.

## No incluye

- Registro público.
- Inicio de sesión con Google.
- Inicio de sesión con Microsoft.
- Doble autenticación.

---

# Módulo: Dashboard

## Objetivo

Mostrar un resumen general del negocio.

## Incluye

- Total de productos.
- Total de clientes.
- Ventas del día.
- Ventas del mes.
- Productos con poco stock.
- Últimas ventas.
- Gráfico de ventas.

## No incluye

- Dashboard personalizable.
- Widgets configurables.

---

# Módulo: Categorías

## Objetivo

Organizar los productos.

## Incluye

- Crear categoría.
- Editar categoría.
- Eliminar categoría.
- Buscar.
- Activar o desactivar.

## No incluye

- Categorías anidadas.
- Subcategorías.

---

# Módulo: Productos

## Objetivo

Administrar el catálogo de productos.

## Incluye

- Crear producto.
- Editar producto.
- Eliminar producto.
- Buscar.
- Filtrar.
- Imagen.
- Categoría.
- Código SKU.
- Código de barras.
- Precio de compra.
- Precio de venta.
- Stock.
- Stock mínimo.
- Estado.

## No incluye

- Variantes.
- Colores.
- Tallas.
- Series.
- Lotes.
- Fecha de vencimiento.
- Múltiples bodegas.

---

# Módulo: Clientes

## Objetivo

Registrar los clientes del negocio.

## Incluye

- Crear cliente.
- Editar cliente.
- Eliminar cliente.
- Buscar cliente.
- Historial de compras.
- Cliente "Consumidor Final".

## No incluye

- Crédito.
- Cuentas por cobrar.
- Límites de crédito.

---

# Módulo: Ventas

## Objetivo

Registrar todas las ventas realizadas.

## Incluye

- Buscar cliente.
- Buscar producto.
- Carrito de compra.
- Agregar productos.
- Modificar cantidades.
- Eliminar productos del carrito.
- Calcular subtotal.
- Calcular IVA.
- Aplicar descuento.
- Total de la venta.
- Método de pago.
- Registrar venta.
- Descontar stock automáticamente.
- Anular venta.
- Restaurar stock al anular.

## Métodos de pago

- Efectivo.
- Tarjeta.
- Transferencia.

## Estados

- Pagada.
- Anulada.

## No incluye

- Facturación electrónica.
- Notas de crédito.
- Cotizaciones.
- Apartados.
- Devoluciones.
- Pagos parciales.

---

# Módulo: Reportes

## Objetivo

Consultar información del negocio.

## Incluye

### Reporte de ventas

- Por fecha.
- Total vendido.
- IVA.
- Cantidad de ventas.

### Reporte de productos

- Stock actual.
- Valor del inventario.

### Reporte de clientes

- Clientes con más compras.

## No incluye

- Reportes financieros.
- Flujo de caja.
- Ganancias detalladas.

---

# Módulo: Configuración

## Objetivo

Configurar la información general del negocio.

## Incluye

- Nombre de la empresa.
- RUC.
- Dirección.
- Teléfono.
- Correo.
- Logo.
- IVA.
- Moneda.

## No incluye

- Configuración de sucursales.
- Configuración avanzada.

---

# Funcionalidades Globales

Todos los módulos deberán incluir cuando corresponda:

- Validaciones.
- Soft Deletes.
- Paginación.
- Búsqueda.
- Filtros.
- Ordenamiento.
- Confirmación antes de eliminar.
- Mensajes de éxito y error.
- Diseño responsive.

---

# Funcionalidades Fuera del Alcance

Las siguientes funcionalidades **NO** forman parte de la versión 1.0.

## Inventario

- Compras.
- Proveedores.
- Kardex.
- Movimientos.
- Ajustes de inventario.
- Múltiples almacenes.

## Ventas

- Facturación electrónica.
- Notas de crédito.
- Cotizaciones.
- Devoluciones.
- Caja diaria.
- Arqueo de caja.

## Empresa

- Multiempresa.
- Múltiples sucursales.
- Multi moneda.

## Clientes

- Crédito.
- Estados de cuenta.
- Cuentas por cobrar.

## Seguridad

- Auditoría completa.
- Registro de actividad.
- Doble autenticación.

## Integraciones

- API REST pública.
- WhatsApp.
- Correo automático.
- Pasarela de pagos.
- API SRI.

---

# Restricciones

La aplicación deberá funcionar para:

- Un único negocio.
- Una única sucursal.
- Un único inventario.

No se desarrollará ninguna funcionalidad que aumente la complejidad del modelo de datos durante la versión 1.0.

---

# Definition of Done (DoD)

La versión 1.0 será considerada terminada únicamente cuando se cumplan todos los siguientes puntos.

## Funcional

- Todos los módulos implementados.
- Flujo completo de venta funcionando.
- Descuento automático de stock.
- Anulación de ventas.
- Reportes funcionando.

## Calidad

- Sin errores críticos.
- Validaciones completas.
- Soft Deletes implementados.
- Código organizado por módulos.

## Base de datos

- Todas las migraciones.
- Seeders.
- Factories.

## Documentación

- README completo.
- Manual de instalación.
- Capturas del sistema.

## Portafolio

- Proyecto publicado en GitHub.
- Proyecto desplegado.
- Datos de prueba.
- Video demostrativo.

---

# Criterio de Éxito

El proyecto será exitoso si un pequeño negocio puede:

- Registrar productos.
- Registrar clientes.
- Registrar ventas.
- Consultar el stock disponible.
- Consultar reportes básicos.

Sin necesidad de utilizar herramientas externas.

---

# Control de Cambios

Durante el desarrollo de la versión 1.0 no se agregarán nuevas funcionalidades.

Toda nueva idea deberá registrarse en el archivo:

ROADMAP.md

y será evaluada para una futura versión.