# 🗄️ Database Schema - StockFlow v1.0

> Diseño de la base de datos.

---

# Información

| Campo | Valor |
|--------|--------|
| Proyecto | StockFlow |
| Documento | Database Schema |
| Versión | 1.0.0 |

---

# Objetivo

Definir la estructura de la base de datos utilizada por StockFlow.

Este documento servirá como base para la creación de migraciones, modelos y relaciones del sistema.

---

# Convenciones

## Llaves primarias

Todas las tablas utilizarán:

```php
$table->id();
```

---

## Llaves foráneas

Siempre utilizarán:

```php
$table->foreignId();
```

Nunca se utilizarán enteros manuales.

---

## Estados

Cuando una entidad pueda activarse o desactivarse utilizará:

```php
status
```

Tipo

```php
boolean
```

---

## Fechas

Todas las tablas utilizarán

```php
timestamps();
```

---

## Eliminación

Cuando corresponda se utilizará

```php
softDeletes();
```

---

# Tablas

---

# users

Responsable de almacenar los usuarios del sistema.

| Campo | Tipo | Restricción |
|--------|------|-------------|
| id | ID | PK |
| name | string | obligatorio |
| email | string | único |
| password | string | obligatorio |
| created_at | timestamp | |
| updated_at | timestamp | |

Laravel gestionará esta tabla.

---

# categories

Agrupa los productos.

| Campo | Tipo |
|--------|------|
| id | ID |
| name | string |
| description | text nullable |
| status | boolean |
| created_at | timestamp |
| updated_at | timestamp |
| deleted_at | timestamp |

## Índices

- name

---

# products

Productos disponibles para la venta.

| Campo | Tipo |
|--------|------|
| id | ID |
| category_id | FK |
| sku | string |
| barcode | string nullable |
| type | string |
| name | string |
| description | text nullable |
| cost | decimal(10,2) |
| price | decimal(10,2) |
| stock | integer |
| minimum_stock | integer |
| image | string nullable |
| status | boolean |
| created_at | timestamp |
| updated_at | timestamp |
| deleted_at | timestamp |

## Restricciones

SKU único.

## Valor por defecto

type

```text
simple
```

Este campo permitirá soportar productos compuestos en futuras versiones.

---

# customers

Clientes del negocio.

| Campo | Tipo |
|--------|------|
| id | ID |
| document | string nullable |
| name | string |
| phone | string nullable |
| email | string nullable |
| address | string nullable |
| status | boolean |
| created_at | timestamp |
| updated_at | timestamp |
| deleted_at | timestamp |

Deberá existir un cliente llamado

Consumidor Final.

---

# sales

Cabecera de la venta.

| Campo | Tipo |
|--------|------|
| id | ID |
| number | string |
| customer_id | FK |
| user_id | FK |
| subtotal | decimal(10,2) |
| discount | decimal(10,2) |
| tax | decimal(10,2) |
| total | decimal(10,2) |
| payment_method | string |
| status | string |
| notes | text nullable |
| created_at | timestamp |
| updated_at | timestamp |

---

# sale_items

Detalle de cada venta.

| Campo | Tipo |
|--------|------|
| id | ID |
| sale_id | FK |
| product_id | FK |
| quantity | integer |
| price | decimal(10,2) |
| subtotal | decimal(10,2) |
| created_at | timestamp |
| updated_at | timestamp |

---

# settings

Configuración general del negocio.

Siempre existirá un solo registro.

| Campo | Tipo |
|--------|------|
| id | ID |
| company_name | string |
| ruc | string nullable |
| phone | string nullable |
| email | string nullable |
| address | text nullable |
| logo | string nullable |
| currency | string |
| tax_percentage | decimal(5,2) |
| created_at | timestamp |
| updated_at | timestamp |

---

# Relaciones

Category

```
1
```

↓

Products

```
N
```

---

Customer

```
1
```

↓

Sales

```
N
```

---

User

```
1
```

↓

Sales

```
N
```

---

Sale

```
1
```

↓

SaleItems

```
N
```

---

Product

```
1
```

↓

SaleItems

```
N
```

---

# Restricciones

## Categorías

No podrán eliminarse si poseen productos asociados.

---

## Productos

No podrán venderse si el stock es insuficiente.

---

## Clientes

Consumidor Final no podrá eliminarse.

---

## Ventas

No podrán eliminarse.

Únicamente podrán cambiar su estado a

Anulada.

---

# Soft Deletes

Utilizarán Soft Delete

- categories
- products
- customers

No utilizarán Soft Delete

- sales
- sale_items
- settings

Las ventas nunca deberán eliminarse por motivos de auditoría.

---

# Índices Recomendados

## categories

- name

---

## products

- sku (UNIQUE)
- barcode
- name
- category_id

---

## customers

- document
- name

---

## sales

- number (UNIQUE)
- customer_id
- user_id
- created_at

---

## sale_items

- sale_id
- product_id

---

# Valores por defecto

## payment_method

- cash
- card
- transfer

---

## sale status

- paid
- cancelled

---

## product type

- simple

---

# Preparado para futuras versiones

La base de datos queda preparada para incorporar posteriormente:

- Productos compuestos.
- Compras.
- Proveedores.
- Kardex.
- Movimientos.
- Bodegas.
- Facturación electrónica.

Sin modificar las tablas principales.

---

# Observaciones

Toda modificación estructural deberá actualizar este documento antes de modificar las migraciones del proyecto.