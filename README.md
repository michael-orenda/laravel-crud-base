# Laravel CRUD Base Controller

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rminchrist/laravel-crud-base.svg?style=flat-square)](https://packagist.org/packages/rminchrist/laravel-crud-base)  
[![Total Downloads](https://img.shields.io/packagist/dt/rminchrist/laravel-crud-base.svg?style=flat-square)](https://packagist.org/packages/rminchrist/laravel-crud-base)  
[![License](https://img.shields.io/github/license/rminchrist/laravel-crud-base.svg?style=flat-square)](LICENSE.md)

A lightweight Laravel package that provides a **Base CRUD Controller** so you can build fully functional RESTful APIs with **zero boilerplate**.  

Simply extend `BaseController` in your Laravel controllers and CRUD routes will be automatically handled for your Eloquent models. 🚀

---

## ✨ Features

- ⚡ **Zero boilerplate** → just `extends BaseController` and you’re done.  
- 🔄 **Automatic Model Resolution** → resolves `ProductController → App\Models\Product`.  
- 📦 **Full CRUD out of the box**:
  - `index` → list all records  
  - `show` → get single record  
  - `store` → create a new record  
  - `update` → update an existing record  
  - `destroy` → delete a record  
- 🛠 **Extendable** → override any method to add custom logic.  
- 🧩 Works with Laravel **9, 10, 11, 12**.  
- ✅ Supports `apiResource` routes.  

---

## 📦 Installation

### Step 1: Require via Composer

```bash
composer require rminchrist/laravel-crud-base
```

> If testing locally before Packagist release, use a `path` repository in your project’s `composer.json`:
>
> ```json
> "repositories": [
>     {
>         "type": "path",
>         "url": "packages/rminchrist/laravel-crud-base"
>     }
> ]
> ```
>
> Then install:
> ```bash
> composer require rminchrist/laravel-crud-base:@dev
> ```

---

## 🚀 Usage

### 1. Create a Model
For example, a simple `Product` model:

```bash
php artisan make:model Product -m
```

Update your migration:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 8, 2);
    $table->timestamps();
});
```

Run migration:
```bash
php artisan migrate
```

---

### 2. Create a Controller that Extends BaseController

```php
<?php

namespace App\Http\Controllers;

use Rminchrist\CrudBase\BaseController;

class ProductController extends BaseController
{
    // no code needed 🎉
}
```

---

### 3. Define Routes

In `routes/api.php`:

```php
use App\Http\Controllers\ProductController;

Route::apiResource('products', ProductController::class);
```

---

### 4. Test Endpoints

You now have a full REST API for `App\Models\Product`:

| Method | Endpoint             | Description            |
|--------|----------------------|------------------------|
| GET    | `/api/products`      | List all products      |
| GET    | `/api/products/{id}` | Show single product    |
| POST   | `/api/products`      | Create new product     |
| PUT    | `/api/products/{id}` | Update existing product|
| DELETE | `/api/products/{id}` | Delete product         |

Example request (create product):

```http
POST /api/products
Content-Type: application/json

{
  "name": "Sample Product",
  "price": "19.99"
}
```

---

## 🛠 Customization

You can override any method in your child controller to customize behavior.  
For example, hashing a password in `UserController`:

```php
<?php

namespace App\Http\Controllers;

use Rminchrist\CrudBase\BaseController;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function store(Request $request)
    {
        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        $record = $this->model->create($data);

        return response()->json($record, 201);
    }
}
```

---

## 🧩 Advanced Ideas (Future Roadmap)

- ✅ Automatic **validation** via `$rules` property on models  
- ✅ Automatic **authorization** via policies  
- ✅ Configurable **namespace resolution** (custom model namespace)  
- ✅ Soft delete / restore support  

---

## 📖 Requirements

- PHP >= 8.2  
- Laravel 9.x, 10.x, 11.x, 12.x  

---

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## ❤️ Contributing

Pull requests are welcome! If you’d like to contribute:

1. Fork the repo  
2. Create your feature branch (`git checkout -b feature/my-feature`)  
3. Commit your changes (`git commit -m 'Add my feature'`)  
4. Push to the branch (`git push origin feature/my-feature`)  
5. Open a Pull Request  

---

🔥 With this package, you’ll never write repetitive CRUD again. Just extend, and ship APIs faster!
