# 🏷️ Convenciones de Código y Nomenclatura - Velntra v1.0

Este documento establece las reglas estrictas de nomenclatura, estilos y estándares de codificación para el proyecto **Velntra**.

---

## 1. Idioma del Código

- **Todo el código fuente debe estar escrito en INGLÉS**:
  - Nombres de Clases, Métodos, Variables, Propiedades.
  - Tablas y Columnas de Base de Datos.
  - Rutas, Permisos y Nombres de Eventos.
- **Los comentarios y la interfaz de usuario**:
  - Los comentarios y docblocks en este proyecto se redactan en **Español** para alineación con el equipo.
  - Las cadenas de texto de la UI deben utilizar **siempre** el helper de traducción `__t('key', 'module')`.

---

## 2. Convenciones de Nomenclatura

| Elemento | Convención | Ejemplo |
|---|---|---|
| **Módulos** | PascalCase | `Administration`, `Inventory`, `Sales` |
| **Modelos** | Singular PascalCase | `User`, `Product`, `Customer`, `Sale` |
| **Tablas BD** | Plural snake_case | `users`, `products`, `customers`, `sales` |
| **Columnas BD** | snake_case | `first_name`, `category_id`, `created_at` |
| **Foreign Keys** | `table_singular_id` | `category_id`, `customer_id`, `user_id` |
| **Livewire Components** | PascalCase por función | `UserIndex`, `ProductIndex`, `SaleCreate` |
| **Vistas Blade** | kebab-case | `user-index.blade.php`, `product-index.blade.php` |
| **Services** | `Model` + `Service` | `UserService`, `ProductService`, `SaleService` |
| **Policies** | `Model` + `Policy` | `UserPolicy`, `ProductPolicy`, `CustomerPolicy` |
| **Permisos Spatie** | `module.action` | `users.view`, `products.create`, `sales.cancel` |
| **Roles** | lowercase | `admin`, `seller` |
| **Métodos** | camelCase con verbo inicial | `openCreateModal()`, `save()`, `syncRoles()` |
| **Variables / Propiedades** | camelCase | `$selectedUserId`, `$totalUsers`, `$search` |
| **Constantes** | UPPER_SNAKE_CASE | `DEFAULT_PAGINATION`, `MAX_ATTEMPTS` |

---

## 3. Organización y Estructura de Importaciones

Las importaciones en archivos PHP deben estar claramente categorizadas con comentarios de sección:

### Ejemplo en Componentes Livewire:
```php
<?php

namespace Modules\Administration\Livewire\Users;

// Framework & Livewire
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

// Models
use Modules\Administration\Models\User;
use Spatie\Permission\Models\Role;

// Services
use Modules\Administration\Services\UserService;
```

### Ejemplo en Servicios:
```php
<?php

namespace Modules\Administration\Services;

// Framework & Database
use Illuminate\Support\Facades\DB;

// Models
use Modules\Administration\Models\User;
```

### Ejemplo en Políticas:
```php
<?php

namespace Modules\Administration\Policies;

// Models
use Modules\Administration\Models\User;
```

---

## 4. Estándar de Comentarios y Docblocks

1. **Docblocks en Propiedades**:
   ```php
   /**
    * ID del usuario seleccionado para edición o eliminación (null en creación).
    */
   public ?int $selectedUserId = null;
   ```

2. **Docblocks en Métodos**:
   Deben ser concisos, claros y con tipado `@param` y `@return`:
   ```php
   /**
    * Guardar el usuario (crear nuevo o actualizar existente) mediante UserService.
    *
    * @param UserService $userService
    * @return void
    */
   public function save(UserService $userService): void
   ```

3. **Comentarios Internos en Pasos Clave**:
   ```php
   // 1. Autorizar acción sobre el modelo
   $this->authorize('update', $user);

   // 2. Persistir cambios mediante el servicio
   $userService->update($user, $userData, $this->selectedRoles);

   // 3. Emitir notificación toast y cerrar modal
   $this->dispatch('toast', [
       'type'    => 'success',
       'message' => __t('user_updated', 'administration'),
   ]);
   $this->dispatch('close-modal', 'user-form');
   ```

---

## 5. Estructura de Carpetas de un Módulo

```text
Modules/{ModuleName}/
├── app/
│   ├── Livewire/
│   │   └── {Feature}/
│   │       └── {Feature}Index.php
│   ├── Models/
│   │   └── {Model}.php
│   ├── Policies/
│   │   └── {Model}Policy.php
│   ├── Providers/
│   │   ├── {ModuleName}ServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── Services/
│       └── {Model}Service.php
│
├── lang/
│   ├── en/
│   │   └── layout.php
│   └── es/
│       └── layout.php
│
├── resources/
│   └── views/
│       └── livewire/
│           └── {feature}/
│               └── {feature}-index.blade.php
│
├── routes/
│   ├── web.php
│   └── api.php
│
└── tests/
    └── Feature/
        └── {Model}ManagementTest.php
```
