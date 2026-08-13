# 🤖 Velntra AI Agent Instructions & Architectural Standards

Este directorio (`.ai/`) contiene las **reglas oficiales, estándares de arquitectura, convenciones de código y plantillas** para guiar a cualquier agente de Inteligencia Artificial en el desarrollo, creación y refactorización de funcionalidades dentro del proyecto **Velntra (Modular Monolith Laravel + Livewire)**.

---

## 📚 Índice de Documentación

| Documento | Descripción |
|---|---|
| [CRUD_ARCHITECTURE_GUIDE.md](file:///.ai/CRUD_ARCHITECTURE_GUIDE.md) | **Estándar Oficial para CRUDs**: Estructura de capas (Livewire, Service, Policy, Model, Blade, Lang, Tests), flujo de datos, autorización, experiencia de usuario (loaders) y notificaciones Toast. |
| [NAMING_AND_CODE_CONVENTIONS.md](file:///.ai/NAMING_AND_CODE_CONVENTIONS.md) | **Convenciones de Código y Nomenclatura**: Reglas de nombres en inglés, organización de importaciones, comentarios, docblocks y diseño modular. |
| [TEMPLATES_AND_BOILERPLATES.md](file:///.ai/TEMPLATES_AND_BOILERPLATES.md) | **Plantillas Base Reutilizables**: Código listo para usar de Livewire Component, Service, Policy, Blade View, Service Provider, Archivos de Idioma y Pruebas Feature. |

---

## 🎯 Regla de Oro para Agentes

> [!IMPORTANT]
> El CRUD de Usuarios del módulo `Administration` es el **esquema base canónico** de este proyecto:
> - **Componente Livewire**: `src/Modules/Administration/app/Livewire/Users/UserIndex.php`
> - **Capa de Servicio**: `src/Modules/Administration/app/Services/UserService.php`
> - **Capa de Política**: `src/Modules/Administration/app/Policies/UserPolicy.php`
> - **Vista Blade**: `src/Modules/Administration/resources/views/livewire/users/user-index.blade.php`
> - **Proveedor del Módulo**: `src/Modules/Administration/app/Providers/AdministrationServiceProvider.php`
> - **Traducciones**: `src/Modules/Administration/lang/{es,en}/layout.php`
> - **Pruebas Feature**: `src/Modules/Administration/tests/Feature/UserManagementTest.php`
>
> **Cualquier nuevo CRUD o refactorización debe seguir exactamente este patrón de separación de responsabilidades, estética y calidad.**

---

## 🧱 Resumen de la Arquitectura de Capas

```text
Blade View (UI / Alpine.js / Tailwind / x-components)
    ↓
Livewire Component (Estado UI, validaciones dinámicas, filtros, eventos)
    ↓
Policy Layer (Autorización basada en permisos Spatie: module.action)
    ↓
Service Layer (Lógica de negocio, persistencia en DB::transaction, relaciones/roles)
    ↓
Eloquent Model / Database (Casts, scopes, fillables, migrations)
```
