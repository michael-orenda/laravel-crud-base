<?php

namespace Rminchrist\CrudBase;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->booted(fn () => $this->registerAutoRoutes());
    }

    protected function registerAutoRoutes()
    {
        $ns     = 'App\\Http\\Controllers\\';
        $path   = app_path('Http/Controllers');
        $prefix = config('crudbase.route_prefix');

        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        foreach ($it as $f) {
            if (!$f->isFile()) continue;

            if (!str_ends_with($f->getFilename(), 'Controller.php')) continue;

            $cname = pathinfo($f->getFilename(), PATHINFO_FILENAME);
            $fqcn  = $ns . $cname;

            if (!class_exists($fqcn)) continue;

            // Basic CRUD
            if (is_subclass_of($fqcn, BaseController::class)) {
                $this->crud($fqcn, $prefix);
            }

            // Relationship + Explicit + M2M
            if (is_subclass_of($fqcn, RelationshipBaseController::class)) {
                $this->rel($fqcn, $prefix);
                $this->explicit($fqcn, $prefix);
                $this->mtm($fqcn, $prefix);
            }
        }
    }

    protected function crud($controller, $prefix)
    {
        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "$prefix/$base" : $base;

        Route::middleware('api')->group(function () use ($controller, $uri) {
            Route::get($uri,            [$controller, 'index']);
            Route::post($uri,           [$controller, 'store']);
            Route::get("$uri/{id}",     [$controller, 'show']);
            Route::put("$uri/{id}",     [$controller, 'update']);
            Route::delete("$uri/{id}",  [$controller, 'destroy']);
        });
    }

    protected function rel($controller, $prefix)
    {
        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "$prefix/$base" : $base;

        Route::middleware('api')->group(function () use ($controller, $uri) {
            Route::get("$uri/{id}/children",  [$controller, 'children']);
            Route::get("$uri/{id}/parent",    [$controller, 'parent']);
            Route::get("$uri/{id}/relations", [$controller, 'relations']);
        });
    }

    protected function explicit($controller, $prefix)
    {
        $instance = new $controller;
        $model    = $instance->getModelInstance(); // model MUST expose getter
        $rels     = $model->detectRelations();

        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "$prefix/$base" : $base;

        Route::middleware('api')->group(function () use ($controller, $uri, $rels) {
            foreach ($rels as $name => $relation) {
                Route::get("$uri/{id}/$name", [$controller, "relation_{$name}"]);
            }
        });
    }

    protected function mtm($controller, $prefix)
    {
        $instance = new $controller;
        $model    = $instance->getModelInstance();
        $rels     = $model->detectManyToManyRelations();

        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "$prefix/$base" : $base;

        Route::middleware('api')->group(function () use ($controller, $uri, $rels) {
            foreach ($rels as $name) {
                Route::get("$uri/{id}/$name",           [$controller, "mtm_get_{$name}"]);
                Route::post("$uri/{id}/$name/attach",   [$controller, "mtm_attach_{$name}"]);
                Route::post("$uri/{id}/$name/detach",   [$controller, "mtm_detach_{$name}"]);
                Route::post("$uri/{id}/$name/sync",     [$controller, "mtm_sync_{$name}"]);
            }
        });
    }
}
