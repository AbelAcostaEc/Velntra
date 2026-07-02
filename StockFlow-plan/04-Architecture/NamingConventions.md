# 📘 Naming Conventions - Velntra v1.0

> Coding standards and naming conventions used throughout the project.

---

# General Information

| Field    | Value              |
| -------- | ------------------ |
| Project  | Velntra          |
| Version  | 1.0.0              |
| Document | Naming Conventions |

---

# Purpose

This document defines the official naming conventions for the project.

Following these conventions ensures:

* Consistency
* Readability
* Scalability
* Maintainability

These rules apply to the entire project.

---

# General Rules

* All source code must be written in **English**.
* Business terminology must remain consistent.
* Use descriptive names.
* Avoid abbreviations unless they are widely accepted.
* Follow Laravel conventions whenever possible.

---

# Modules

Use **PascalCase**.

```text
Core
Administration
Dashboard
Inventory
Customers
Sales
Settings
```

---

# Namespaces

Use PascalCase.

```php
Modules\Inventory

Modules\Sales

Modules\Customers

Modules\Administration
```

---

# Classes

Use PascalCase.

Examples:

```text
Product

Category

Customer

Sale

SaleItem

Setting
```

---

# Interfaces

Suffix:

```text
Interface
```

Example

```text
ProductRepositoryInterface

StockServiceInterface
```

---

# Traits

Prefix:

```text
Has
```

Examples

```text
HasSlug

HasStatus

HasUuid

HasCreatedBy
```

---

# Enums

Use PascalCase.

Examples

```text
Status

SaleStatus

PaymentMethod

Currency

ProductType
```

---

# Controllers

Suffix

```text
Controller
```

Examples

```text
ProductController

SaleController

CustomerController
```

---

# Livewire Components

Use PascalCase.

Examples

```text
ProductIndex

ProductCreate

ProductEdit

CustomerIndex

SaleCreate

DashboardIndex

SettingsForm
```

---

# Blade Views

Use kebab-case.

Examples

```text
product-index.blade.php

product-create.blade.php

product-edit.blade.php

customer-index.blade.php

sale-create.blade.php
```

---

# Models

Singular.

PascalCase.

Examples

```text
Product

Category

Customer

Sale

SaleItem

Setting
```

---

# Database Tables

Plural.

snake_case.

Examples

```text
products

categories

customers

sales

sale_items

settings
```

---

# Database Columns

snake_case.

Examples

```text
category_id

minimum_stock

payment_method

created_at

updated_at

deleted_at
```

---

# Foreign Keys

Always

```text
table_singular_id
```

Examples

```text
category_id

customer_id

user_id

sale_id

product_id
```

---

# Pivot Tables

Alphabetical order.

Examples

```text
permission_role

role_user
```

---

# Services

Suffix

```text
Service
```

Examples

```text
ProductService

SaleService

CustomerService

SettingService
```

---

# Repositories

Suffix

```text
Repository
```

Examples

```text
ProductRepository

SaleRepository
```

---

# Actions

Use a verb.

Examples

```text
CreateSaleAction

CancelSaleAction

RestoreStockAction

GenerateSaleNumberAction
```

---

# Policies

Suffix

```text
Policy
```

Examples

```text
ProductPolicy

CustomerPolicy

SalePolicy
```

---

# Requests

Action + Model + Request

Examples

```text
StoreProductRequest

UpdateProductRequest

StoreSaleRequest

UpdateCustomerRequest
```

---

# Notifications

Suffix

```text
Notification
```

Examples

```text
LowStockNotification

WelcomeNotification
```

---

# Jobs

Use verbs.

Examples

```text
GenerateMonthlyReportJob

SyncProductsJob

ImportCustomersJob
```

---

# Events

Past tense.

Examples

```text
SaleCreated

ProductUpdated

CustomerCreated
```

---

# Listeners

Describe the action.

Examples

```text
SendSaleReceipt

UpdateInventory

NotifyAdministrator
```

---

# Seeders

Suffix

```text
Seeder
```

Examples

```text
ProductSeeder

CustomerSeeder

InventorySeeder

AdministrationSeeder
```

---

# Factories

Suffix

```text
Factory
```

Examples

```text
ProductFactory

SaleFactory

CustomerFactory
```

---

# Variables

camelCase.

Examples

```php
$product

$productName

$totalAmount

$currentStock

$minimumStock
```

---

# Constants

UPPER_SNAKE_CASE.

Examples

```php
DEFAULT_CUSTOMER

DEFAULT_CURRENCY

MAX_ITEMS_PER_PAGE
```

---

# Methods

camelCase.

Start with a verb.

Examples

```php
createProduct()

updateProduct()

deleteProduct()

calculateTax()

restoreStock()

cancelSale()
```

Avoid names like:

```php
product()

sale()

test()
```

---

# Booleans

Start with:

```text
is

has

can

should
```

Examples

```php
isActive

isAdmin

hasStock

canDelete

shouldNotify
```

---

# Routes

Use dot notation.

Examples

```text
inventory.products.index

inventory.products.create

inventory.products.edit

customers.index

sales.create

settings.index
```

---

# Route URLs

Use kebab-case.

Examples

```text
/products

/products/create

/products/{product}/edit

/customers

/sales

/settings
```

---

# Permissions

Use lowercase.

Format

```text
module.action
```

Examples

```text
products.view

products.create

products.update

products.delete

sales.create

customers.view

settings.update
```

---

# Roles

Lowercase.

Examples

```text
admin

seller
```

---

# Cache Keys

Examples

```text
products.all

dashboard.stats

settings.company
```

---

# Configuration Keys

snake_case.

Examples

```text
tax_percentage

default_currency

company_name
```

---

# Environment Variables

UPPER_SNAKE_CASE.

Examples

```text
APP_NAME

APP_ENV

DB_HOST

DB_DATABASE
```

---

# Git Branches

Main branches

```text
main

develop
```

Feature branches

```text
feature/products

feature/sales

feature/dashboard
```

Bug fixes

```text
fix/stock-validation

fix/login
```

Documentation

```text
docs/database-schema

docs/readme
```

Refactoring

```text
refactor/sale-service
```

---

# Commit Messages

Follow Conventional Commits.

Examples

```text
feat: add product CRUD

feat: implement sales module

fix: prevent selling products without stock

refactor: move stock logic to service

docs: update business rules

style: improve dashboard layout

test: add sale creation tests
```

---

# File Names

Use PascalCase for PHP classes.

Examples

```text
Product.php

ProductService.php

StoreProductRequest.php
```

Markdown documents

Use PascalCase.

Examples

```text
Vision.md

Scope.md

BusinessRules.md

DatabaseSchema.md
```

---

# Comments

Comments may be written in Spanish when explaining business rules.

Example

```php
// Consumidor Final is used when the customer is not registered.
```

Avoid obvious comments.

Bad

```php
// Increment counter
$count++;
```

Prefer expressive code instead.

---

# Forbidden Practices

Do not use:

```text
tmp

test

data

value

obj

var

foo

bar
```

Avoid generic method names.

```php
save()

process()

execute()
```

Prefer descriptive names.

```php
createSale()

updateCustomer()

restoreInventory()
```

---

# Final Rule

Whenever a new class, folder, method, route, or database object is created, it must follow the conventions defined in this document.

Consistency is more important than personal preference.
