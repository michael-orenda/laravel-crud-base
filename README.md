# CrudBase v1.3.0
Automatic CRUD and Relationship Routing for Laravel 12+

## Features
- Zero‑boilerplate CRUD controllers
- Auto-detected parent/child relationships
- `/relations` endpoint (parent + children)
- Explicit relation routes (e.g., `/invoice/1/items`)
- Many-to-many routes (attach/detach/sync)
- Works with Laravel naming conventions
- No model boilerplate except using DetectsRelationships trait

## Installation
```
composer require rminchrist/laravel-crud-base
php artisan vendor:publish --tag=crudbase-config
```

## Usage
Extend:
```
class InvoiceController extends RelationshipBaseController {}
```

Models:
```
use DetectsRelationships;
```

## Auto Routes
See API.md for full list.
