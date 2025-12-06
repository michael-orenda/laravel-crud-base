# CrudBase 1.2.1

Automatic CRUD and Relationship Routing for Laravel — Zero Boilerplate.

## ✨ New in 1.2.1
- Added a unified **/relations** endpoint that returns:
  - The model (parent)
  - Parent relation (belongsTo)
  - All children relations (hasMany)
- Documentation updated to include the new endpoint.

## Features
- Auto-detection of controllers → auto-routing
- Auto CRUD endpoints for every BaseController
- Auto relationship endpoints for every RelationshipBaseController
- Parent & children auto-detection via reflection-safe methods
- NEW: `/model/{id}/relations` endpoint combining parent + children in one JSON response
- Ready for integration with recursive relations package

## Requirements
- Laravel 10+
- PHP 8.1+
- PSR-4 autoloading for your controllers

## Installation
```
composer require rminchrist/laravel-crud-base
```

## Usage
Create a controller extending:
- `BaseController` for simple CRUD
- `RelationshipBaseController` for models with parent/children

Example:
```php
class InvoiceController extends RelationshipBaseController {}
```

The package automatically registers routes for you.

