<?php

declare(strict_types=1);

namespace StevenFox\Eloquaint\Traits;

use ReflectionClass;
use StevenFox\Eloquaint\Attributes\Scope;

/**
 * Trait for handling Eloquent scopes defined via PHP attributes.
 *
 * This trait allows models to define local scopes using PHP attributes
 * instead of traditional methods, reducing boilerplate code.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasAttributeScopes
{
    /**
     * Cache for resolved attribute scopes.
     *
     * @var array<string, array<string, Scope>>
     */
    protected static array $attributeScopesCache = [];

    /**
     * Boot the HasAttributeScopes trait.
     *
     * This method is called automatically by Eloquent when the model is booted.
     */
    protected static function bootHasAttributeScopes(): void
    {
        static::resolveAttributeScopes();
    }

    /**
     * Resolve and cache all attribute scopes for this model.
     */
    protected static function resolveAttributeScopes(): void
    {
        $class = static::class;

        if (isset(static::$attributeScopesCache[$class])) {
            return;
        }

        $reflection = new ReflectionClass($class);
        static::$attributeScopesCache[$class] = [];

        // Parse class-level attributes
        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof Scope) {
                static::$attributeScopesCache[$class][$instance->getName()] = $instance;
            }
        }
    }

    /**
     * Get an attribute scope by name.
     *
     * @param  string  $name  The scope name
     */
    protected function getAttributeScope(string $name): ?Scope
    {
        return static::$attributeScopesCache[static::class][$name] ?? null;
    }

    /**
     * Handle dynamic method calls for attribute scopes.
     *
     * This method checks if the called method matches an attribute-defined scope.
     * If found, it applies the scope to the query builder.
     *
     * @param  string  $method  The method name
     * @param  array<mixed>  $parameters  The method parameters
     * @return mixed
     */
    protected function handleAttributeScope(string $method, array $parameters)
    {
        if ($scope = $this->getAttributeScope($method)) {
            // Get the query builder - for scopes, we need a fresh query
            $query = $this->newQuery();

            // Apply simple where clause
            return $query->where($scope->getColumn(), $scope->getOperator(), $scope->getValue());
        }

        return null;
    }

    /**
     * Handle static method calls for attribute scopes.
     *
     * This method handles static scope calls like Model::published().
     *
     * @param  string  $method  The method name
     * @param  array<mixed>  $parameters  The method parameters
     * @return mixed
     */
    protected static function handleStaticAttributeScope(string $method, array $parameters)
    {
        $instance = new static;

        if ($scope = $instance->getAttributeScope($method)) {
            // Get a fresh query builder for static calls
            $query = $instance->newQuery();

            // Apply simple where clause
            return $query->where($scope->getColumn(), $scope->getOperator(), $scope->getValue());
        }

        return null;
    }

    /**
     * Check if a method name corresponds to an attribute scope.
     *
     * @param  string  $method  The method name
     * @return bool
     */
    public function isAttributeScope(string $method): bool
    {
        return $this->getAttributeScope($method) !== null;
    }

    /**
     * Check if a method name corresponds to an attribute scope (static version).
     *
     * @param  string  $method  The method name
     * @return bool
     */
    public static function isStaticAttributeScope(string $method): bool
    {
        $instance = new static;
        return $instance->getAttributeScope($method) !== null;
    }
}
