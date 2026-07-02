# 📋 Product Backlog - Velntra v1.0

> Product Backlog for the MVP version.

---

# General Information

| Field    | Value           |
| -------- | --------------- |
| Project  | Velntra       |
| Version  | 1.0.0           |
| Document | Product Backlog |
| Status   | Planning        |

---

# Purpose

This backlog contains every task required to complete Velntra version **1.0**.

Only tasks listed here belong to the current version.

Any new feature request must be added to the roadmap instead of this backlog.

---

# Priority Legend

| Priority | Meaning  |
| -------- | -------- |
| P1       | Critical |
| P2       | High     |
| P3       | Medium   |
| P4       | Low      |

---

# Epic 01 - Project Setup

## Goal

Prepare the development environment.

---

### STK-001

**Title**

Create Laravel project

Priority

P1

---

### STK-002

Install Laravel Modules

Priority

P1

---

### STK-003

Configure PostgreSQL

Priority

P1

---

### STK-004

Configure Tailwind CSS

Priority

P1

---

### STK-005

Install Laravel Breeze

Priority

P1

---

### STK-006

Install Spatie Permission

Priority

P1

---

### STK-007

Configure Git repository

Priority

P1

---

### STK-008

Create module structure

Priority

P1

---

# Epic 02 - Administration

## Goal

Implement authentication and user administration.

---

### STK-009

Configure authentication

P1

---

### STK-010

Create administrator user

P1

---

### STK-011

Configure roles

P1

---

### STK-012

Configure permissions

P1

---

### STK-013

Create users CRUD

P2

---

### STK-014

Create profile page

P2

---

### STK-015

Change password

P2

---

# Epic 03 - Inventory

## Goal

Manage products and categories.

---

### Categories

---

### STK-016

Create categories migration

P1

---

### STK-017

Create Category model

P1

---

### STK-018

Create Category CRUD

P1

---

### STK-019

Category validation

P1

---

### STK-020

Category policy

P2

---

### Products

---

### STK-021

Create products migration

P1

---

### STK-022

Create Product model

P1

---

### STK-023

Create Product CRUD

P1

---

### STK-024

Upload product image

P2

---

### STK-025

Product validation

P1

---

### STK-026

Product policy

P2

---

### STK-027

Product search

P1

---

### STK-028

Product filters

P2

---

### STK-029

Pagination

P2

---

### STK-030

Soft delete

P2

---

# Epic 04 - Customers

## Goal

Manage customers.

---

### STK-031

Create customers migration

P1

---

### STK-032

Create Customer model

P1

---

### STK-033

Customer CRUD

P1

---

### STK-034

Customer validation

P1

---

### STK-035

Customer search

P2

---

### STK-036

Customer purchase history

P3

---

### STK-037

Create default customer

Consumidor Final

P1

---

# Epic 05 - Sales

## Goal

Register sales.

---

### STK-038

Create sales migration

P1

---

### STK-039

Create sale_items migration

P1

---

### STK-040

Sale model

P1

---

### STK-041

SaleItem model

P1

---

### STK-042

SaleService

P1

---

### STK-043

Sale creation

P1

---

### STK-044

Product search

P1

---

### STK-045

Customer search

P1

---

### STK-046

Shopping cart

P1

---

### STK-047

Quantity update

P1

---

### STK-048

Remove item

P1

---

### STK-049

Subtotal calculation

P1

---

### STK-050

Tax calculation

P1

---

### STK-051

Discount support

P2

---

### STK-052

Payment method

P1

---

### STK-053

Store sale

P1

---

### STK-054

Decrease stock

P1

---

### STK-055

Sale cancellation

P2

---

### STK-056

Restore stock

P2

---

### STK-057

Sale detail page

P2

---

# Epic 06 - Dashboard

## Goal

Display business metrics.

---

### STK-058

Dashboard cards

P1

---

### STK-059

Latest sales

P2

---

### STK-060

Low stock widget

P2

---

### STK-061

Sales chart

P3

---

# Epic 07 - Reports

## Goal

Generate business reports.

---

### STK-062

Sales report

P2

---

### STK-063

Inventory report

P2

---

### STK-064

Customer report

P3

---

### STK-065

Date filters

P2

---

# Epic 08 - Settings

## Goal

Manage company settings.

---

### STK-066

Settings migration

P1

---

### STK-067

Setting model

P1

---

### STK-068

Settings form

P2

---

### STK-069

Company information

P2

---

### STK-070

Tax configuration

P2

---

# Epic 09 - Testing

## Goal

Validate application quality.

---

### STK-071

Factories

P2

---

### STK-072

Seeders

P2

---

### STK-073

Feature tests

P3

---

### STK-074

Unit tests

P3

---

# Epic 10 - Portfolio

## Goal

Prepare the project for publication.

---

### STK-075

README

P1

---

### STK-076

Installation guide

P2

---

### STK-077

Screenshots

P2

---

### STK-078

Demo data

P2

---

### STK-079

Deploy application

P1

---

### STK-080

Record demo video

P2

---

# Definition of Done

The project is considered finished when:

* All P1 tasks are completed.
* No critical bugs remain.
* Database migrations run successfully.
* Seeders work correctly.
* Demo data is available.
* README is complete.
* The application is deployed.
* The complete sales flow works correctly.
* Stock updates automatically.
* Reports generate correctly.

---

# Out of Scope

The following features are not part of version 1.0:

* Purchases
* Suppliers
* Composite products
* Product variants
* Multi warehouse
* Multi company
* Multi branch
* Electronic invoicing
* Accounts receivable
* Accounts payable
* POS hardware integration
* REST API
* Mobile application

These items belong to future versions defined in `Roadmap.md`.
