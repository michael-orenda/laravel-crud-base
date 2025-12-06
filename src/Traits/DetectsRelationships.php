<?php

namespace Rminchrist\CrudBase\Traits;

use ReflectionClass;
use Illuminate\Database\Eloquent\Relations\Relation;

trait DetectsRelationships
{
    public function detectRelations(): array
    {
        $class = new ReflectionClass($this);
        $methods = $class->getMethods();
        $relations = [];

        foreach ($methods as $method) {

            // Only scan methods declared in THIS model class
            if ($method->class !== $class->getName()) {
                continue;
            }

            // Skip methods with parameters
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            // Skip magic methods
            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            // Skip methods that are not camelCase relationship names
            // Example: invoices, customer, items, payments
            if (!preg_match('/^[a-z][A-Za-z0-9_]*$/', $method->getName())) {
                continue;
            }

            // Try to infer based on return type (Laravel 8+ supports this)
            $returnType = $method->getReturnType();

            // If method has a declared return type and it's NOT Relation → skip
            if ($returnType && !$this->isRelationReturnType($returnType)) {
                continue;
            }

            // Only execute methods with no declared return type OR explicit Relation type
            try {
                $result = $this->{$method->getName()}();

                if ($result instanceof Relation) {
                    $relations[$method->getName()] = $result;
                }
            } catch (\Throwable $e) {
                // Ignore safely
            }
        }

        return $relations;
    }

    private function isRelationReturnType($returnType): bool
    {
        $relationClasses = [
            \Illuminate\Database\Eloquent\Relations\Relation::class,
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            \Illuminate\Database\Eloquent\Relations\HasOne::class,
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            \Illuminate\Database\Eloquent\Relations\MorphMany::class,
            \Illuminate\Database\Eloquent\Relations\MorphTo::class,
        ];

        foreach ($relationClasses as $relation) {
            if (is_a($returnType->getName(), $relation, true)) {
                return true;
            }
        }

        return false;
    }

    public function detectParentRelation(): ?string
    {
        foreach ($this->detectRelations() as $name => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                return $name;
            }
        }
        return null;
    }

    public function detectChildrenRelations(): array
    {
        $children = [];

        foreach ($this->detectRelations() as $name => $relation) {
            if ($relation instanceof \Illuminate\Database\Eloquent\Relations\HasMany) {
                $children[] = $name;
            }
        }

        return $children;
    }
}
