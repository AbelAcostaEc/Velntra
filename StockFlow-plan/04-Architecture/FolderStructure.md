# 🧱 Folder Structure - Velntra v1.0

> Technical folder structure and architecture conventions.

---

# General Information

| Field          | Value            |
| -------------- | ---------------- |
| Project        | Velntra        |
| Document       | Folder Structure |
| Version        | 1.0.0            |
| Architecture   | Modular Monolith |
| Framework      | Laravel          |
| Module Package | Laravel Modules  |

---

# Purpose

This document defines the official folder structure, namespaces, naming conventions, and code organization rules for Velntra.

The goal is to keep the project clean, scalable, maintainable, and easy to understand.

---

# Architecture Style

Velntra will use a **Modular Monolith** architecture.

This means the application is a single Laravel project, but its business logic is separated into independent modules.

```text
Modules/
├── Core/
├── Administration/
├── Dashboard/
├── Inventory/
├── Customers/
├── Sales/
└── Settings/
```

Each module owns its own domain logic, routes, models, requests, services, policies, migrations, seeders, factories, and Livewire components.

---

# Root Project Structure

```text
velntra/
├── app/
├── bootstrap/
├── config/
├── database/
├── docs/
├── Modules/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

# Documentation Structure

```text
docs/
├── 01-Product/
│   ├── Vision.md
│   ├── Scope.md
│   └── Roadmap.md
│
├── 02-Analysis/
│   ├── BusinessRules.md
│   └── Modules.md
│
├── 03-Database/
│   └── DatabaseSchema.md
│
├── 04-Architecture/
│   ├── FolderStructure.md
│   └── NamingConventions.md
│
├── 05-Development/
│   ├── Sprint01.md
│   ├── Sprint02.md
│   └── Sprint03.md
│
├── 06-Deployment/
│   └── Installation.md
│
└── 07-Portfolio/
    └── README.md
```

---

# Modules Structure

Each module must follow this base structure:

```text
Modules/{ModuleName}/
├── Config/
├── Database/
│   ├── Factories/
│   ├── Migrations/
│   └── Seeders/
│
├── app/
│   ├── Actions/
│   ├── Enums/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   │
│   ├── Livewire/
│   ├── Models/
│   ├── Policies/
│   ├── Providers/
│   ├── Repositories/
│   ├── Requests/
│   └── Services/
│
├── resources/
│   └── views/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── composer.json
├── module.json
└── README.md
```

---

# Main Modules

## Core

```text
Modules/Core/
├── app/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Helpers/
│   ├── Interfaces/
│   ├── Services/
│   ├── Support/
│   └── Traits/
```

The `Core` module contains shared code used by other modules.

Examples:

```text
Modules/Core/app/Enums/Status.php
Modules/Core/app/Traits/HasStatus.php
Modules/Core/app/Traits/HasUuid.php
Modules/Core/app/Services/CodeGeneratorService.php
Modules/Core/app/Helpers/MoneyHelper.php
```

Rule:

Shared code must live in `Core`.

Do not duplicate shared helpers, traits, or enums inside multiple modules.

---

## Administration

```text
Modules/Administration/
├── app/
│   ├── Livewire/
│   │   ├── Users/
│   │   ├── Roles/
│   │   └── Permissions/
│   │
│   ├── Models/
│   ├── Policies/
│   ├── Requests/
│   └── Services/
```

Responsible for:

* Authentication
* Users
* Roles
* Permissions
* Profile management

Main classes:

```text
UserService
StoreUserRequest
UpdateUserRequest
UserPolicy
```

---

## Dashboard

```text
Modules/Dashboard/
├── app/
│   ├── Livewire/
│   └── Services/
```

Responsible for:

* Main dashboard
* Metrics
* Charts
* Latest sales
* Low stock products

Main classes:

```text
DashboardService
DashboardIndex
```

---

## Inventory

```text
Modules/Inventory/
├── app/
│   ├── Livewire/
│   │   ├── Categories/
│   │   └── Products/
│   │
│   ├── Models/
│   ├── Policies/
│   ├── Requests/
│   ├── Services/
│   └── Enums/
```

Responsible for:

* Categories
* Products
* Stock fields

Future features inside this module:

* Purchases
* Suppliers
* Stock movements
* Kardex
* Product bundles
* Warehouses

Main classes:

```text
Category
Product
CategoryService
ProductService
StoreCategoryRequest
UpdateCategoryRequest
StoreProductRequest
UpdateProductRequest
CategoryPolicy
ProductPolicy
ProductType
```

---

## Customers

```text
Modules/Customers/
├── app/
│   ├── Livewire/
│   ├── Models/
│   ├── Policies/
│   ├── Requests/
│   └── Services/
```

Responsible for:

* Customers
* Customer purchase history
* Final consumer customer

Main classes:

```text
Customer
CustomerService
StoreCustomerRequest
UpdateCustomerRequest
CustomerPolicy
```

---

## Sales

```text
Modules/Sales/
├── app/
│   ├── Actions/
│   ├── Enums/
│   ├── Livewire/
│   ├── Models/
│   ├── Policies/
│   ├── Requests/
│   └── Services/
```

Responsible for:

* Sales
* Sale items
* Cart logic
* Stock discount
* Sale cancellation
* Sales reports

Main classes:

```text
Sale
SaleItem
SaleService
CancelSaleAction
CreateSaleAction
StoreSaleRequest
SalePolicy
SaleStatus
PaymentMethod
```

---

## Settings

```text
Modules/Settings/
├── app/
│   ├── Livewire/
│   ├── Models/
│   ├── Policies/
│   ├── Requests/
│   └── Services/
```

Responsible for:

* Company information
* Tax percentage
* Currency
* Logo

Main classes:

```text
Setting
SettingService
UpdateSettingRequest
SettingPolicy
```

---

# Namespace Convention

All module namespaces must use English names.

Base namespace:

```php
Modules\{ModuleName}
```

Examples:

```php
Modules\Inventory\Models\Product;
Modules\Inventory\Services\ProductService;
Modules\Sales\Models\Sale;
Modules\Sales\Services\SaleService;
Modules\Customers\Models\Customer;
Modules\Settings\Models\Setting;
```

---

# Naming Conventions

## Models

Singular PascalCase.

```text
Product
Category
Customer
Sale
SaleItem
Setting
```

---

## Tables

Plural snake_case.

```text
products
categories
customers
sales
sale_items
settings
```

---

## Services

Model name + `Service`.

```text
ProductService
CategoryService
CustomerService
SaleService
SettingService
DashboardService
```

---

## Requests

Action + Model + `Request`.

```text
StoreProductRequest
UpdateProductRequest
StoreCustomerRequest
UpdateCustomerRequest
StoreSaleRequest
UpdateSettingRequest
```

---

## Policies

Model name + `Policy`.

```text
ProductPolicy
CustomerPolicy
SalePolicy
SettingPolicy
```

---

## Enums

Singular PascalCase.

```text
Status
ProductType
SaleStatus
PaymentMethod
Currency
```

---

## Livewire Components

PascalCase by feature.

```text
ProductIndex
ProductCreate
ProductEdit
CategoryIndex
CustomerIndex
SaleCreate
SaleShow
DashboardIndex
SettingsForm
```

---

# Livewire Structure

Livewire components should be grouped by feature.

```text
Modules/Inventory/app/Livewire/Products/
├── ProductIndex.php
├── ProductCreate.php
└── ProductEdit.php

Modules/Inventory/resources/views/livewire/products/
├── product-index.blade.php
├── product-create.blade.php
└── product-edit.blade.php
```

---

# Service Layer Rule

Business logic must live inside Services or Actions.

Livewire components should not contain heavy business logic.

Allowed in Livewire:

* Form state
* UI actions
* Calling services
* Pagination
* Search filters

Not allowed in Livewire:

* Stock calculations
* Sale creation logic
* Sale cancellation logic
* Complex validation logic
* Business rules

Example:

```php
$this->saleService->createSale($validatedData);
```

---

# Action Classes

Actions are used for specific business operations.

Examples:

```text
CreateSaleAction
CancelSaleAction
RestoreStockAction
GenerateSaleNumberAction
```

Use Actions when a process has several steps or affects multiple models.

---

# Repository Layer

Repositories are optional in v1.0.

They should only be created when query logic becomes complex.

Allowed:

```text
ProductRepository
SaleRepository
CustomerRepository
```

Do not create repositories just to wrap simple Eloquent calls.

---

# Request Validation Rule

Validation must live inside Form Requests whenever possible.

Example:

```text
StoreProductRequest
UpdateProductRequest
StoreSaleRequest
```

Livewire components may use simple inline validation only for temporary UI state.

Business validation must remain centralized.

---

# Policy Rule

Authorization must be handled using Policies.

Examples:

```php
$this->authorize('create', Product::class);
$this->authorize('update', $product);
$this->authorize('delete', $product);
```

Avoid checking roles directly inside views or Livewire components.

---

# Routes Structure

Each module owns its own routes.

```text
Modules/Inventory/routes/web.php
Modules/Sales/routes/web.php
Modules/Customers/routes/web.php
Modules/Settings/routes/web.php
```

Example route naming:

```php
inventory.products.index
inventory.products.create
inventory.products.edit

sales.index
sales.create
sales.show

customers.index
settings.index
```

---

# View Structure

Blade views must live inside their module.

```text
Modules/Inventory/resources/views/
Modules/Sales/resources/views/
Modules/Customers/resources/views/
```

Shared layouts may live in:

```text
resources/views/layouts/
```

or

```text
Modules/Core/resources/views/layouts/
```

The final decision should remain consistent across the project.

---

# Database Structure

Each module owns its own database files.

```text
Modules/Inventory/Database/Migrations/
Modules/Inventory/Database/Seeders/
Modules/Inventory/Database/Factories/
```

Example:

```text
Modules/Inventory/Database/Migrations/2026_01_01_000001_create_categories_table.php
Modules/Inventory/Database/Migrations/2026_01_01_000002_create_products_table.php
```

---

# Seeder Convention

Each module should have its own seeder.

```text
InventoryDatabaseSeeder
CustomersDatabaseSeeder
SalesDatabaseSeeder
SettingsDatabaseSeeder
AdministrationDatabaseSeeder
```

Main database seeder may call module seeders.

---

# Factory Convention

Factories must be created for main models.

```text
ProductFactory
CategoryFactory
CustomerFactory
SaleFactory
SaleItemFactory
```

Factories are required for:

* Testing
* Demo data
* Portfolio presentation
* Local development

---

# Testing Structure

Each module contains its own tests.

```text
Modules/Inventory/tests/Feature/
Modules/Inventory/tests/Unit/

Modules/Sales/tests/Feature/
Modules/Sales/tests/Unit/
```

Suggested tests:

```text
ProductCreationTest
ProductUpdateTest
SaleCreationTest
SaleCancellationTest
StockDiscountTest
CustomerCreationTest
```

---

# Code Flow

The preferred application flow is:

```text
Route
↓
Livewire Component
↓
Form Request / Validation
↓
Service / Action
↓
Model
↓
Database
```

For complex processes:

```text
Livewire Component
↓
Service
↓
Action
↓
Model
↓
Database
```

Example sale flow:

```text
SaleCreate
↓
SaleService
↓
CreateSaleAction
↓
Sale / SaleItem / Product
↓
Database
```

---

# Dependency Rules

## Allowed Dependencies

```text
All modules → Core

Dashboard → Inventory
Dashboard → Customers
Dashboard → Sales

Sales → Inventory
Sales → Customers
Sales → Settings

Inventory → Core

Customers → Core

Settings → Core

Administration → Core
```

---

## Not Allowed

Inventory must not depend on Sales.

Customers must not depend on Sales.

Settings must not depend on Sales.

Core must not depend on any module.

---

# Shared Code Rule

Shared code belongs in `Core`.

Examples:

```text
HasStatus
HasUuid
MoneyFormatter
DateRangeHelper
Status enum
```

Domain-specific code belongs inside its module.

Examples:

```text
Sales/Enums/SaleStatus.php
Sales/Enums/PaymentMethod.php
Inventory/Enums/ProductType.php
```

---

# Comments Rule

Code, classes, namespaces, methods, variables, and files must be written in English.

Spanish comments are allowed only when they explain business context.

Example:

```php
// Consumidor Final is the default customer used when no customer is selected.
```

Avoid unnecessary comments.

Prefer clear code over excessive comments.

---

# Language Rule

Use English for:

* Class names
* Method names
* Variable names
* Database tables
* Database columns
* Routes
* Namespaces
* Folders
* Git commits
* Pull requests
* Technical documentation

Spanish can be used for:

* User interface labels
* Business explanations
* End-user manual
* Comments when needed

---

# Git Branch Strategy

Recommended branches:

```text
main
develop
feature/*
fix/*
refactor/*
docs/*
```

Examples:

```text
feature/inventory-products
feature/sales-cart
feature/customers-crud
fix/stock-validation
docs/database-schema
```

---

# Commit Convention

Use conventional commits.

Examples:

```text
feat: add product management module
feat: implement sale creation flow
fix: prevent selling products without stock
refactor: extract sale creation logic into action
docs: add database schema documentation
test: add sale cancellation test
```

---

# Final Rules

* Do not create unnecessary folders.
* Do not duplicate shared logic.
* Do not place business logic inside views.
* Do not place complex business logic inside Livewire components.
* Do not create future-version features in v1.0.
* Keep modules independent.
* Keep namespaces stable.
* Keep code in English.
* Keep UI text in Spanish if the final user is Spanish-speaking.
