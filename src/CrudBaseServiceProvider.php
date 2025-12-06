<?php

namespace Rminchrist\CrudBase;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudBaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish config (optional)
        $this->publishes([
            __DIR__ . '/../config/crudbase.php' => config_path('crudbase.php'),
        ], 'crudbase-config');

        // Defer route registration until the framework has finished booting.
        // This avoids timing/autoload/order issues.
        $this->app->booted(function () {
            $this->registerAutoRoutes();
        });
    }

    protected function registerAutoRoutes()
    {
        Log::info('CrudBaseServiceProvider: registerAutoRoutes start');

        $namespace = config('crudbase.controller_namespace', 'App\\Http\\Controllers\\');
        $path      = config('crudbase.controller_path', app_path('Http/Controllers'));
        $prefix    = config('crudbase.route_prefix');

        // Ensure package controller parents are loaded BEFORE checks
        class_exists(\Rminchrist\CrudBase\BaseController::class);
        class_exists(\Rminchrist\CrudBase\RelationshipBaseController::class);

        // Use recursive scanning so controllers in subfolders are found.
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $filename = $file->getFilename();

            if (! str_ends_with($filename, 'Controller.php')) {
                continue;
            }

            // Build the controller FQCN from the configured namespace + file basename.
            // If you use sub-namespaces mirroring folders, you'll need a more advanced resolver.
            $controllerName = pathinfo($filename, PATHINFO_FILENAME);
            $fqcn = $namespace . $controllerName;

            Log::info("CrudBaseServiceProvider: found file {$file->getPathname()} -> trying {$fqcn}");

            if (! class_exists($fqcn)) {
                Log::info("CrudBaseServiceProvider: class {$fqcn} not found (skipping)");
                continue;
            }

            // Register CRUD routes
            if (
                is_subclass_of($fqcn, \Rminchrist\CrudBase\BaseController::class) &&
                config('crudbase.auto_crud_routes', true)
            ) {
                Log::info("CrudBaseServiceProvider: registering CRUD routes for {$fqcn}");
                $this->registerCrudRoutesFor($fqcn, $prefix);
            }

            // Register Relationship routes
            if (
                is_subclass_of($fqcn, \Rminchrist\CrudBase\RelationshipBaseController::class) &&
                config('crudbase.auto_relationship_routes', true)
            ) {
                Log::info("CrudBaseServiceProvider: registering relationship routes for {$fqcn}");
                $this->registerRelationshipRoutesFor($fqcn, $prefix);
            }
        }

        Log::info('CrudBaseServiceProvider: registerAutoRoutes end');
    }

    protected function registerCrudRoutesFor(string $controller, ?string $prefix)
    {
        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "{$prefix}/{$base}" : $base;

        // Choose api or web according to config or default to 'api'
        $middleware = config('crudbase.route_middleware', 'api');

        Route::middleware($middleware)->group(function () use ($controller, $uri) {
            Route::get($uri,               [$controller, 'index']);
            Route::post($uri,              [$controller, 'store']);
            Route::get("{$uri}/{id}",      [$controller, 'show']);
            Route::put("{$uri}/{id}",      [$controller, 'update']);
            Route::delete("{$uri}/{id}",   [$controller, 'destroy']);
        });
    }

    protected function registerRelationshipRoutesFor(string $controller, ?string $prefix)
    {
        $base = strtolower(Str::snake(Str::replaceLast('Controller', '', class_basename($controller))));
        $uri  = $prefix ? "{$prefix}/{$base}" : $base;

        $middleware = config('crudbase.route_middleware', 'api');

        Route::middleware($middleware)->group(function () use ($controller, $uri) {
            Route::get("{$uri}/{id}/children",   [$controller, 'children']);
            Route::post("{$uri}/{id}/children",  [$controller, 'storeChild']);
            Route::get("{$uri}/{id}/parent",     [$controller, 'parent']);
            Route::put("{$uri}/{id}/parent",     [$controller, 'setParent']);
            Route::get("{$uri}/{id}/relations",  [$controller, 'relations']);

        });
    }
}
