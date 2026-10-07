# 📦 Modules - Velntra v1.0

> Arquitectura modular del sistema.

---

# Información

| Campo | Valor |
|--------|--------|
| Proyecto | Velntra |
| Documento | Modules |
| Versión | 1.0.0 |

---

# Objetivo

Definir la estructura modular del proyecto para mantener una arquitectura organizada, escalable y fácil de mantener.

Cada módulo representa un dominio funcional del sistema.

---

# Estructura General

```text
Modules/

Core/
Administration/
Dashboard/
Inventory/
Customers/
Sales/
Settings/
```

---

# Core

## Objetivo

Contener todos los elementos reutilizables por el resto de módulos.

## Responsabilidades

- Traits
- Helpers
- Enums
- Exceptions
- Base Classes
- Interfaces
- Shared Services
- Notifications
- Utilidades
- Componentes reutilizables

## Base de Datos

No posee tablas.

---

# Administration

## Objetivo

Administrar la seguridad y acceso al sistema.

## Funcionalidades

- Login
- Logout
- Recuperar contraseña
- Perfil
- Usuarios
- Roles
- Permisos

## Modelos

- User

## Tablas

- users
- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions

## Servicios

- UserService

## Validación

- Reglas definidas en los componentes Livewire.

---

# Dashboard

## Objetivo

Mostrar indicadores generales del negocio.

## Funcionalidades

- Dashboard principal
- Tarjetas informativas
- Últimas ventas
- Productos con poco stock
- Gráfico de ventas

## Modelos

Ninguno.

## Base de Datos

No posee tablas.

---

# Inventory

## Objetivo

Administrar el catálogo e inventario del negocio.

Este módulo será ampliado en futuras versiones.

## Funcionalidades v1

- Categorías
- Productos

## Funcionalidades futuras

- Compras
- Proveedores
- Kardex
- Movimientos
- Ajustes
- Bodegas
- Productos compuestos
- Variantes

## Modelos

- Category
- Product

## Tablas

- categories
- products

## Servicios

- ProductService
- CategoryService

## Validación

- Reglas de categorías y productos definidas en sus componentes Livewire.

---

# Customers

## Objetivo

Administrar los clientes.

## Funcionalidades

- CRUD Clientes
- Historial de compras

## Modelos

- Customer

## Tablas

- customers

## Servicios

- CustomerService

## Validación

- Reglas definidas en los componentes Livewire de clientes.

---

# Sales

## Objetivo

Registrar todas las ventas realizadas.

## Funcionalidades

- Crear venta
- Carrito
- Buscar productos
- Buscar clientes
- Registrar venta
- Anular venta
- Reportes de ventas

## Funcionalidades futuras

- Cotizaciones
- Facturación electrónica
- Caja
- Pagos
- Devoluciones

## Modelos

- Sale
- SaleItem

## Tablas

- sales
- sale_items

## Servicios

- SaleService

`SaleService` encapsula las operaciones transaccionales de creación,
finalización, anulación y eliminación de ventas pendientes. Las Actions no son
obligatorias; solo se separarán cuando una operación resulte difícil de
comprender, reutilizar o probar dentro del servicio.

## Validación

- Reglas definidas en los componentes Livewire de ventas.

---

# Settings

## Objetivo

Administrar la configuración general del negocio.

## Funcionalidades

- Datos empresa
- Logo
- IVA
- Moneda

## Modelos

- Setting

## Tablas

- settings

## Servicios

- SettingService

## Validación

- Reglas definidas en los componentes Livewire de configuración.

---

# Organización Interna

Cada módulo utilizará la siguiente estructura.

```text
Module/

Config/

Database/
├── Factories
├── Migrations
├── Seeders

Http/
├── Controllers
├── Middleware

Livewire/

Models/

Policies/

Providers/

Repositories/

Resources/

Routes/

Services/

Tests/
```

Las carpetas adicionales solo se crearán cuando realmente sean necesarias.

---

# Convenciones

## Servicios

Toda la lógica de negocio deberá implementarse en Services.

Ejemplo

- ProductService
- SaleService
- CustomerService

---

## Validación Livewire

Los flujos Livewire validarán la entrada mediante `rules()`, `#[Validate]` o
Livewire Form Objects. Las reglas de negocio críticas permanecerán en los
Services para no depender exclusivamente de la interfaz.

---

## Policies

Los permisos se administrarán mediante Policies.

No se utilizarán validaciones de permisos directamente en los controladores.

---

## Traits

Los Traits compartidos vivirán en el módulo Core.

Solo se crearán Traits dentro de un módulo cuando sean exclusivos de ese dominio.

---

## Helpers

Los Helpers compartidos vivirán en Core.

No se crearán Helpers duplicados entre módulos.

---

## Enums

Los Enums globales vivirán en Core.

Los Enums específicos permanecerán dentro del módulo correspondiente.

---

# Dependencias entre módulos

```text
Dashboard
      │
      ▼
Inventory
      │
      ▼
Sales
      ▲
      │
Customers

Administration

Settings

Core
```

Todos los módulos podrán utilizar funcionalidades compartidas del módulo Core.

---

# Principios

Durante el desarrollo se respetarán los siguientes principios.

- Alta cohesión.
- Bajo acoplamiento.
- Responsabilidad única.
- Reutilización de código.
- Separación de responsabilidades.
- Arquitectura modular.
- Escalabilidad.

---

# Evolución del sistema

Los nuevos módulos o funcionalidades deberán agregarse dentro del dominio correspondiente.

Ejemplo

Inventory

- Compras
- Proveedores
- Kardex

Sales

- Facturación electrónica
- Caja
- Cotizaciones

Customers

- Crédito
- Estado de cuenta

La estructura principal de módulos no deberá modificarse durante la evolución del proyecto.
