# 📐 Business Rules - Velntra v1.0

> Reglas de negocio del sistema.

---

# Información

| Campo | Valor |
|--------|--------|
| Proyecto | Velntra |
| Documento | Business Rules |
| Versión | 1.0.0 |

---

# Introducción

Las reglas de negocio definen el comportamiento que el sistema debe cumplir independientemente de la tecnología utilizada.

Estas reglas representan las políticas del negocio y deberán respetarse durante todo el desarrollo.

---

# Productos

### BR-001

Todo producto debe pertenecer a una categoría.

---

### BR-002

Todo producto debe tener un nombre.

---

### BR-003

El SKU debe ser único.

---

### BR-004

El código de barras es opcional.

---

### BR-005

El costo debe ser mayor o igual a cero.

---

### BR-006

El precio de venta debe ser mayor al costo.

---

### BR-007

El stock inicial no puede ser negativo.

---

### BR-008

El stock mínimo no puede ser negativo.

---

### BR-009

Un producto inactivo no podrá venderse.

---

### BR-010

Los productos utilizarán Soft Delete.

---

# Categorías

### BR-011

Toda categoría debe tener un nombre.

---

### BR-012

No podrá eliminarse una categoría que tenga productos asociados.

---

### BR-013

Las categorías podrán activarse o desactivarse.

---

# Clientes

### BR-014

El nombre del cliente es obligatorio.

---

### BR-015

El documento de identificación es opcional.

---

### BR-016

El correo electrónico es opcional.

---

### BR-017

Debe existir un cliente llamado **Consumidor Final**.

---

### BR-018

Consumidor Final no podrá eliminarse.

---

### BR-019

Consumidor Final podrá utilizarse cuando una venta no tenga cliente registrado.

---

# Ventas

### BR-020

Toda venta debe contener al menos un producto.

---

### BR-021

Toda venta debe tener un cliente.

---

### BR-022

Si el usuario no selecciona un cliente se utilizará Consumidor Final.

---

### BR-023

No podrá venderse un producto con stock insuficiente.

---

### BR-024

Al registrar una venta el stock disminuirá automáticamente.

---

### BR-025

Una venta podrá contener varios productos.

---

### BR-026

La cantidad vendida debe ser mayor que cero.

---

### BR-027

El precio del producto se copiará en el detalle de la venta.

---

### BR-028

El subtotal será la suma de todos los productos vendidos.

---

### BR-029

El descuento será opcional.

---

### BR-030

El IVA será calculado automáticamente.

---

### BR-031

El total será:

Total = Subtotal - Descuento + IVA

---

### BR-032

Toda venta deberá registrar el usuario que la realizó.

---

### BR-033

Toda venta deberá registrar la fecha y hora.

---

### BR-034

Una venta podrá anularse.

---

### BR-035

Al anular una venta el stock será restaurado automáticamente.

---

### BR-036

Las ventas anuladas no podrán modificarse.

---

# Dashboard

### BR-037

El Dashboard mostrará únicamente información actualizada.

---

### BR-038

Las ventas anuladas no serán consideradas en las estadísticas.

---

### BR-039

Los productos con stock igual o inferior al stock mínimo aparecerán como Stock Bajo.

---

# Reportes

### BR-040

Los reportes de ventas permitirán filtrar por rango de fechas.

---

### BR-041

Los reportes únicamente considerarán ventas pagadas.

---

### BR-042

Los reportes deberán calcular los totales automáticamente.

---

# Configuración

### BR-043

Solo los administradores podrán modificar la configuración del sistema.

---

### BR-044

El porcentaje de IVA será configurable.

---

### BR-045

La información de la empresa será utilizada en los comprobantes y reportes.

---

# Seguridad

### BR-046

Solo usuarios autenticados podrán acceder al sistema.

---

### BR-047

Cada usuario solo podrá realizar las acciones permitidas por su rol.

---

### BR-048

Las operaciones críticas deberán solicitar confirmación antes de ejecutarse.

---

# Restricciones de la versión 1.0

La versión 1.0 NO incluirá:

- Compras.
- Proveedores.
- Productos compuestos.
- Variantes de productos.
- Múltiples sucursales.
- Múltiples bodegas.
- Kardex.
- Movimientos de inventario.
- Facturación electrónica.
- Cotizaciones.
- Devoluciones.
- Cuentas por cobrar.
- Cuentas por pagar.
- Caja diaria.
- API pública.

---

# Observaciones

Todas las funcionalidades nuevas deberán agregarse en versiones posteriores del sistema.

Durante el desarrollo de la versión 1.0 no se modificarán estas reglas salvo correcciones o errores de análisis.